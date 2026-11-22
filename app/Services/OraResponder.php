<?php

namespace App\Services;

use App\Models\KnowledgeChunk;
use App\Models\OraLesson;
use Illuminate\Support\Collection;

class OraResponder
{
    /** @return array{reply: string, link: array{label: string, url: string}|null} */
    public function respond(string $message): array
    {
        $question = str($message)->lower()->squish()->toString();

        if ($this->containsAny($question, ['chest pain', 'trouble breathing', 'cannot breathe', 'suicid', 'self harm', 'unconscious', 'severe bleeding'])) {
            return ['reply' => 'This could need urgent help. Please contact your local emergency service or go to the nearest emergency department now. ORA cannot assess emergencies.', 'link' => null];
        }

        $lesson = OraLesson::query()->active()->latest('updated_at')->get()
            ->map(fn (OraLesson $lesson): array => ['lesson' => $lesson, 'score' => $lesson->matchScore($question)])
            ->filter(fn (array $match): bool => $match['score'] > 0)
            ->sortByDesc('score')
            ->first()['lesson'] ?? null;
        if ($lesson !== null) {
            return ['reply' => $lesson->answer, 'link' => null];
        }

        $source = KnowledgeChunk::query()->whereHas('document', fn ($query) => $query->where('status', 'ready'))->with('document')->get()
            ->map(function (KnowledgeChunk $chunk) use ($question): array {
                $triggerAnswer = $this->approvedAnswer($question, $chunk->content);

                return [
                    'chunk' => $chunk,
                    'answer' => $triggerAnswer ?? $this->fallbackApprovedAnswer($chunk->content),
                    'has_trigger_match' => $triggerAnswer !== null,
                    'score' => $this->sourceScore($question, $chunk->content),
                ];
            })
            ->filter(fn (array $source): bool => $source['answer'] !== null)
            ->sortByDesc('score')
            ->first();

        if ($source !== null && ($source['score'] >= 2 || ($source['score'] === 1 && $source['has_trigger_match']))) {
            return ['reply' => $source['answer']."\n\nSource: {$source['chunk']->document->original_filename}", 'link' => null];
        }

        if ($this->containsAny($question, ['medisphere'])
            && $this->containsAny($question, ['what', 'tell me', 'describe', 'about', 'platform', 'website', 'site', 'who are you'])) {
            return $this->answer(
                'MediSphere is a health-information and care-support website. You can explore medical specialties, read general health information, and find services such as pharmacy support, psychology, and food and wellbeing. The information is educational and does not replace advice from a healthcare professional.',
                'Explore care options',
                route('home').'#services',
            );
        }

        if ($this->containsAny($question, ['medicine', 'medication', 'prescription', 'dose', 'dosage', 'side effect', 'pharmacy'])) {
            return $this->answer('For medicine questions, a pharmacist or clinician can help you check what is safe for you. Do not start, stop, or change a prescribed medicine based only on a chat response.', 'Explore pharmacy support', route('service.show', ['service' => 'pharmacy']));
        }
        if ($this->containsAny($question, ['anxiety', 'stress', 'depress', 'mental health', 'therapy', 'psycholog'])) {
            return $this->answer('Feeling overwhelmed can be hard. A qualified mental-health professional can help you work through what you are experiencing. If you might harm yourself, contact local emergency support now.', 'Find psychology support', route('service.show', ['service' => 'psychologists']));
        }
        if ($this->containsAny($question, ['food', 'diet', 'nutrition', 'fruit', 'eat'])) {
            return $this->answer('Small, sustainable changes are usually more helpful than strict rules. Explore our food and wellbeing guidance, and speak with a clinician or dietitian for advice tailored to you.', 'Explore food & fruits', route('service.show', ['service' => 'food-fruits']));
        }
        if ($this->containsAny($question, ['symptom', 'pain', 'rash', 'headache', 'fever', 'cough', 'condition', 'disease'])) {
            return $this->answer('I can help you find reliable information, but I cannot diagnose symptoms. If symptoms are severe, sudden, worsening, or worrying, please seek care from a healthcare professional.', 'Browse the medicine library', route('medicines.index'));
        }

        return $this->answer('Hi, I’m ORA. I can help you find MediSphere information about health topics, pharmacy support, psychology, and food and wellbeing. What would you like to explore?', 'Explore care options', route('home').'#services');
    }

    private function containsAny(string $question, array $phrases): bool
    {
        $questionWords = $this->words($question);

        foreach ($phrases as $phrase) {
            $phraseWords = $this->words($phrase);
            $matchesPhrase = $phraseWords->every(
                fn (string $word): bool => $questionWords->contains(
                    fn (string $questionWord): bool => $questionWord === $word
                        || (mb_strlen($word) >= 5 && str_starts_with($questionWord, $word)),
                ),
            );

            if ($matchesPhrase) {
                return true;
            }
        }

        return false;
    }

    /** @return Collection<int, string> */
    private function words(string $text): Collection
    {
        return collect(preg_split('/[^\pL\pN]+/u', $text) ?: [])
            ->filter()
            ->map(fn (string $word): string => str($word)->lower()->toString())
            ->values();
    }

    private function sourceScore(string $question, string $content): int
    {
        $words = $this->meaningfulWords($question);
        $sourceWords = $this->meaningfulWords($content);

        return $words->intersect($sourceWords)->count();
    }

    private function approvedAnswer(string $question, string $content): ?string
    {
        if (! preg_match('/TRIGGER PHRASES\s+(.*?)\s+APPROVED RESPONSE\s+(.*?)\s+RELATED KEYWORDS/sui', $content, $matches)) {
            return null;
        }

        $questionWords = $this->meaningfulWords($question);
        $triggerWords = $this->meaningfulWords($matches[1]);
        $sharedWordCount = $questionWords->intersect($triggerWords)->count();
        $triggerAcronyms = collect(preg_split('/[^\pL\pN]+/u', $matches[1]) ?: [])
            ->filter(fn (string $word): bool => mb_strlen($word) >= 3
                && mb_strlen($word) <= 5
                && $word === mb_strtoupper($word)
                && $word !== mb_strtolower($word))
            ->map(fn (string $word): string => mb_strtolower($word))
            ->values();
        $hasAcronymMatch = $questionWords->intersect($triggerAcronyms)->isNotEmpty();
        $hasSingleWordMatch = $triggerWords->count() === 1 && $sharedWordCount === 1;

        if ($sharedWordCount < 2 && ! $hasAcronymMatch && ! $hasSingleWordMatch) {
            return null;
        }

        return str($matches[2])->squish()->trim('“”" ')->toString();
    }

    private function fallbackApprovedAnswer(string $content): ?string
    {
        if (! str_contains($content, 'ENTRY ID:')) {
            return null;
        }

        if (! preg_match('/APPROVED RESPONSE\s+(.*?)\s+RELATED KEYWORDS/sui', $content, $matches)) {
            return null;
        }

        return str($matches[1])->squish()->trim('“”" ')->toString();
    }

    /** @return Collection<int, string> */
    private function meaningfulWords(string $text): Collection
    {
        $ignoredWords = [
            'about', 'after', 'again', 'also', 'and', 'are', 'but', 'can', 'could',
            'does', 'for', 'from', 'have', 'help', 'how', 'into', 'is', 'its',
            'more', 'not', 'our', 'please', 'should', 'some', 'that', 'the',
            'their', 'them', 'then', 'there', 'these', 'they', 'this', 'those',
            'through', 'what', 'when', 'where', 'which', 'who', 'why', 'with',
            'would', 'you', 'your',
        ];

        return collect(preg_split('/[^\pL\pN]+/u', $text) ?: [])
            ->filter()
            ->map(fn (string $word): string => str($word)->lower()->toString())
            ->filter(fn (string $word): bool => mb_strlen($word) >= 3 && ! in_array($word, $ignoredWords, true))
            ->unique()
            ->values();
    }

    /** @return array{reply: string, link: array{label: string, url: string}} */
    private function answer(string $reply, string $label, string $url): array
    {
        return ['reply' => $reply, 'link' => ['label' => $label, 'url' => $url]];
    }
}

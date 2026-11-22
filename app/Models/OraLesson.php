<?php

namespace App\Models;

use Database\Factories\OraLessonFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class OraLesson extends Model
{
    /** @use HasFactory<OraLessonFactory> */
    use HasFactory;

    protected $fillable = [
        'trigger_phrases',
        'answer',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function matches(string $question): bool
    {
        return $this->matchScore($question) > 0;
    }

    public function matchScore(string $question): int
    {
        $questionWords = $this->meaningfulWords($question);

        if ($questionWords->isEmpty()) {
            return 0;
        }

        return collect(explode(',', $this->trigger_phrases))
            ->map(function (string $phrase) use ($questionWords): int {
                $triggerWords = $this->meaningfulWords($phrase);

                if ($triggerWords->isEmpty()) {
                    return 0;
                }

                $matchedWords = $triggerWords->intersect($questionWords);
                $matchedCount = $matchedWords->count();

                if ($triggerWords->count() === 1 || $matchedCount >= 2) {
                    return $matchedCount;
                }

                $hasAcronymMatch = $matchedWords->intersect($this->acronymWords($phrase))->isNotEmpty();

                return $hasAcronymMatch ? $matchedCount : 0;
            })
            ->max() ?? 0;
    }

    /**
     * @return Collection<int, string>
     */
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

        return collect(preg_split('/[^\pL\pN]+/u', str($text)->lower()->toString()) ?: [])
            ->filter(fn (string $word): bool => mb_strlen($word) >= 3 && ! in_array($word, $ignoredWords, true))
            ->unique()
            ->values();
    }

    /**
     * @return Collection<int, string>
     */
    private function acronymWords(string $text): Collection
    {
        return collect(preg_split('/[^\pL\pN]+/u', $text) ?: [])
            ->filter(fn (string $word): bool => mb_strlen($word) >= 3
                && mb_strlen($word) <= 5
                && $word === mb_strtoupper($word)
                && $word !== mb_strtolower($word))
            ->map(fn (string $word): string => mb_strtolower($word))
            ->values();
    }
}

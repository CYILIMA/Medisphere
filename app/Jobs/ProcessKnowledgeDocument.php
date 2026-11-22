<?php

namespace App\Jobs;

use App\Models\KnowledgeDocument;
use App\Services\KnowledgeDocumentExtractor;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;
use Throwable;

class ProcessKnowledgeDocument implements ShouldQueue
{
    use Queueable;

    public function __construct(public KnowledgeDocument $knowledgeDocument) {}

    /**
     * Execute the job.
     */
    public function handle(KnowledgeDocumentExtractor $extractor): void
    {
        try {
            $text = trim($extractor->extract(Storage::disk('local')->path($this->knowledgeDocument->path), $this->knowledgeDocument->mime_type));

            if ($text === '') {
                throw new \RuntimeException('No readable text was found in this document.');
            }

            $this->knowledgeDocument->chunks()->delete();
            $chunks = $this->chunk($text);
            foreach ($chunks as $position => $content) {
                $this->knowledgeDocument->chunks()->create(['position' => $position, 'content' => $content]);
            }

            $this->knowledgeDocument->update(['status' => 'ready', 'chunk_count' => count($chunks), 'error_message' => null]);
        } catch (Throwable $exception) {
            report($exception);
            $this->knowledgeDocument->update(['status' => 'failed', 'error_message' => 'ORA could not read this document. Please upload a text-based PDF or DOCX file.']);
        }
    }

    /**
     * @return array<int, string>
     */
    private function chunk(string $text): array
    {
        $entries = preg_split('/(?=ENTRY ID:)/u', $text, -1, PREG_SPLIT_NO_EMPTY);
        $chunks = [];

        foreach ($entries ?: [$text] as $entry) {
            foreach (mb_str_split(trim($entry), 4000) as $chunk) {
                if ($chunk !== '') {
                    $chunks[] = $chunk;
                }
            }
        }

        return $chunks;
    }
}

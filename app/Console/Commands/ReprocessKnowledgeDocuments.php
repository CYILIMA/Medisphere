<?php

namespace App\Console\Commands;

use App\Jobs\ProcessKnowledgeDocument;
use App\Models\KnowledgeDocument;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('app:reprocess-knowledge-documents {--all : Reprocess ready documents too}')]
#[Description('Reprocess failed ORA knowledge documents')]
class ReprocessKnowledgeDocuments extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $documents = KnowledgeDocument::query()
            ->when(! $this->option('all'), fn ($query) => $query->where('status', '!=', 'ready'))
            ->get();

        foreach ($documents as $document) {
            $document->update(['status' => 'processing', 'error_message' => null]);
            ProcessKnowledgeDocument::dispatchSync($document);
        }

        $this->components->info("Reprocessed {$documents->count()} knowledge document(s).");

        return self::SUCCESS;
    }
}

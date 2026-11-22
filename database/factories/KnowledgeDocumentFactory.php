<?php

namespace Database\Factories;

use App\Models\KnowledgeDocument;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<KnowledgeDocument>
 */
class KnowledgeDocumentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => 'ORA knowledge guide',
            'original_filename' => 'ora-knowledge-guide.pdf',
            'mime_type' => 'application/pdf',
            'path' => 'ora-knowledge/ora-knowledge-guide.pdf',
            'size_bytes' => 1024,
            'status' => 'ready',
            'chunk_count' => 1,
            'error_message' => null,
        ];
    }
}

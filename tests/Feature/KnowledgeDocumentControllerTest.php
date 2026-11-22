<?php

use App\Models\KnowledgeDocument;
use App\Models\User;
use Illuminate\Support\Facades\Bus;

it('retries a failed knowledge document for administrators', function () {
    Bus::fake();
    $document = KnowledgeDocument::factory()->create([
        'status' => 'failed',
        'error_message' => 'ORA could not read this document.',
    ]);

    $this->actingAs(User::factory()->create(['is_admin' => true]))
        ->post(route('admin.knowledge-documents.retry', $document))
        ->assertRedirect(route('admin.knowledge-documents.index'))
        ->assertSessionHas('status', 'ORA is retrying this document.');

    $this->assertDatabaseHas('knowledge_documents', [
        'id' => $document->id,
        'status' => 'processing',
        'error_message' => null,
    ]);
});

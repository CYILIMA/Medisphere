<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('knowledge_documents', function (Blueprint $table): void {
            $table->string('name')->after('id');
            $table->string('original_filename')->after('name');
            $table->string('mime_type', 100)->after('original_filename');
            $table->string('path')->after('mime_type');
            $table->unsignedBigInteger('size_bytes')->after('path');
            $table->string('status', 30)->default('processing')->after('size_bytes');
            $table->unsignedInteger('chunk_count')->default(0)->after('status');
            $table->text('error_message')->nullable()->after('chunk_count');
            $table->index(['status', 'updated_at']);
        });

        Schema::table('knowledge_chunks', function (Blueprint $table): void {
            $table->foreignId('knowledge_document_id')->after('id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('position')->after('knowledge_document_id');
            $table->text('content')->after('position');
            $table->unique(['knowledge_document_id', 'position']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('knowledge_chunks', function (Blueprint $table): void {
            $table->dropUnique(['knowledge_document_id', 'position']);
            $table->dropConstrainedForeignId('knowledge_document_id');
            $table->dropColumn(['position', 'content']);
        });

        Schema::table('knowledge_documents', function (Blueprint $table): void {
            $table->dropIndex(['status', 'updated_at']);
            $table->dropColumn(['name', 'original_filename', 'mime_type', 'path', 'size_bytes', 'status', 'chunk_count', 'error_message']);
        });
    }
};

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
        Schema::create('ora_lessons', function (Blueprint $table): void {
            $table->id();
            $table->string('trigger_phrases', 500);
            $table->text('answer');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->index(['is_active', 'updated_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ora_lessons');
    }
};

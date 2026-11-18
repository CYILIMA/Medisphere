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
        Schema::create('medicines', function (Blueprint $table) {
            $table->id();
            $table->string('title', 180);
            $table->string('slug', 190)->unique();
            $table->string('medicine_name', 180);
            $table->string('category', 100);
            $table->string('condition', 180);
            $table->string('summary', 350);
            $table->longText('description');
            $table->string('doctor_name', 160);
            $table->string('doctor_specialty', 160);
            $table->string('doctor_email');
            $table->string('doctor_phone', 40)->nullable();
            $table->string('doctor_image_path');
            $table->boolean('is_published')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medicines');
    }
};

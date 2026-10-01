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
        Schema::create('posts', function (Blueprint $table): void {
            $table->id();
            $table->string('title_en')->nullable();
            $table->string('title_es')->nullable();
            $table->string('slug')->unique();
            $table->string('category_en')->nullable();
            $table->string('category_es')->nullable();
            $table->text('excerpt_en')->nullable();
            $table->text('excerpt_es')->nullable();
            $table->longText('content_en')->nullable();
            $table->longText('content_es')->nullable();
            $table->string('cover_image')->nullable();
            $table->dateTime('published_at')->nullable();
            $table->boolean('is_published')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};

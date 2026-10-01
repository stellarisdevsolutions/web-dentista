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
        Schema::create('banners', function (Blueprint $table): void {
            $table->id();
            $table->string('badge_en')->nullable();
            $table->string('badge_es')->nullable();
            $table->string('title_en')->nullable();
            $table->string('title_es')->nullable();
            $table->text('subtitle_en')->nullable();
            $table->text('subtitle_es')->nullable();
            $table->string('button_text_en')->nullable();
            $table->string('button_text_es')->nullable();
            $table->string('button_url')->nullable();
            $table->string('background_image')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('banners');
    }
};

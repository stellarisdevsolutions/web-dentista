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
        Schema::create('site_sections', function (Blueprint $table) {
            $table->id();
            $table->string('page', 50)->default('home')->index();
            $table->string('section_key', 50)->index();
            $table->string('name');
            $table->string('icon')->default('sparkles');
            $table->unsignedInteger('order')->default(0)->index();
            $table->boolean('is_active')->default(true)->index();
            $table->json('content');
            $table->json('settings')->nullable();
            $table->timestamps();

            $table->unique(['page', 'section_key']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('site_sections');
    }
};

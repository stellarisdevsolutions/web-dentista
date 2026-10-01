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
        Schema::create('faqs', function (Blueprint $table): void {
            $table->id();
            $table->string('category_en')->nullable();
            $table->string('category_es')->nullable();
            $table->string('question_en')->nullable();
            $table->string('question_es')->nullable();
            $table->text('answer_en')->nullable();
            $table->text('answer_es')->nullable();
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
        Schema::dropIfExists('faqs');
    }
};

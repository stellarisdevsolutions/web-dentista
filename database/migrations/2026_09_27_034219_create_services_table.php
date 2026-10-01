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
        Schema::create('services', function (Blueprint $table): void {
            $table->id();
            $table->string('name_en')->nullable();
            $table->string('name_es')->nullable();
            $table->string('slug')->unique();
            $table->string('category_en')->nullable();
            $table->string('category_es')->nullable();
            $table->string('short_description_en')->nullable();
            $table->string('short_description_es')->nullable();
            $table->text('description_en')->nullable();
            $table->text('description_es')->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->unsignedSmallInteger('duration_minutes')->default(45);
            $table->string('image')->nullable();
            $table->string('icon')->nullable();
            $table->boolean('allows_online_booking')->default(true);
            $table->boolean('is_featured')->default(false);
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
        Schema::dropIfExists('services');
    }
};

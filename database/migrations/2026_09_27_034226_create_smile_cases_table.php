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
        Schema::create('smile_cases', function (Blueprint $table): void {
            $table->id();
            $table->string('title_en')->nullable();
            $table->string('title_es')->nullable();
            $table->foreignId('service_id')->nullable()->constrained('services')->nullOnDelete();
            $table->foreignId('dentist_id')->nullable()->constrained('dentists')->nullOnDelete();
            $table->string('before_image');
            $table->string('after_image');
            $table->text('description_en')->nullable();
            $table->text('description_es')->nullable();
            $table->text('patient_testimonial_en')->nullable();
            $table->text('patient_testimonial_es')->nullable();
            $table->boolean('is_featured')->default(true);
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
        Schema::dropIfExists('smile_cases');
    }
};

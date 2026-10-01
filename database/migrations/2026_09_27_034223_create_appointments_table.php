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
        Schema::create('appointments', function (Blueprint $table): void {
            $table->id();
            $table->string('code')->unique()->index();
            $table->foreignId('service_id')->nullable()->constrained('services')->nullOnDelete();
            $table->foreignId('dentist_id')->nullable()->constrained('dentists')->nullOnDelete();
            $table->string('patient_name');
            $table->string('patient_email')->nullable();
            $table->string('patient_phone');
            $table->date('appointment_date')->index();
            $table->string('appointment_time');
            $table->unsignedSmallInteger('duration_minutes')->default(45);
            $table->boolean('is_first_visit')->default(true);
            $table->text('symptoms')->nullable();
            $table->text('internal_notes')->nullable();
            $table->string('preferred_locale')->default('en');
            $table->string('status')->default('pending')->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};

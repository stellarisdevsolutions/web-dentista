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
        Schema::create('clinic_settings', function (Blueprint $table): void {
            $table->id();
            $table->string('clinic_name');
            $table->string('tagline')->nullable();
            $table->string('phone');
            $table->string('whatsapp');
            $table->string('email');
            $table->string('address');
            $table->text('google_maps_url')->nullable();
            $table->text('opening_hours')->nullable();
            $table->string('emergency_phone')->nullable();
            $table->string('logo_url')->nullable();
            $table->boolean('show_language_switcher')->default(true);
            $table->json('social_links')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clinic_settings');
    }
};

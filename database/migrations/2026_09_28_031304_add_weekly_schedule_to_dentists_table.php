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
        Schema::table('dentists', function (Blueprint $table): void {
            $table->json('weekly_schedule')->nullable()->after('working_hours_end');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dentists', function (Blueprint $table): void {
            $table->dropColumn('weekly_schedule');
        });
    }
};

<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database based on configured locale.
     */
    public function run(): void
    {
        if (File::isDirectory(database_path('seed_assets'))) {
            File::ensureDirectoryExists(storage_path('app/public'));
            File::copyDirectory(database_path('seed_assets'), storage_path('app/public'));
        }

        User::firstOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'Admin Clinic',
                'password' => 'password',
                'role' => UserRole::Admin,
            ]
        );

        $configuredLocale = env('SEEDER_LOCALE', config('app.locale', 'es'));
        $isEnglish = str_starts_with(strtolower((string) $configuredLocale), 'en');

        $this->call([
            $isEnglish ? DentalClinicEnSeeder::class : DentalClinicSeeder::class,
            $isEnglish ? SiteSectionEnSeeder::class : SiteSectionSeeder::class,
            MediaAssetSeeder::class,
        ]);
    }
}

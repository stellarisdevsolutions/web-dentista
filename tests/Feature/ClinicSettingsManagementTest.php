<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Filament\Resources\ClinicSettings\Pages\ManageClinicSettings;
use App\Models\ClinicSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class ClinicSettingsManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_clinic_settings_page(): void
    {
        $admin = User::factory()->create([
            'role' => UserRole::Admin,
        ]);

        $clinic = ClinicSetting::create([
            'clinic_name' => 'Aura Dental Studio VIP',
            'tagline' => 'La mejor sonrisa para ti',
            'phone' => '+1 (555) 123-4567',
            'whatsapp' => '+15551234567',
            'email' => 'admin@auradental.com',
            'address' => 'Avenida Principal 123',
            'opening_hours' => "Lunes a Viernes: 9:00 AM - 6:00 PM\nSábado: 9:00 AM - 1:00 PM",
            'show_language_switcher' => false,
        ]);

        $this->actingAs($admin);

        $response = $this->get('/admin/clinic-settings');
        $response->assertSuccessful();
        $response->assertSee('Aura Dental Studio VIP');
        $response->assertSee('+1 (555) 123-4567');
        $response->assertSee('Avenida Principal 123');
    }

    public function test_non_admin_cannot_access_clinic_settings(): void
    {
        $receptionist = User::factory()->create([
            'role' => UserRole::Receptionist,
        ]);

        $this->actingAs($receptionist);

        $response = $this->get('/admin/clinic-settings');
        $response->assertForbidden();
    }

    public function test_admin_can_edit_clinic_settings_through_action(): void
    {
        $admin = User::factory()->create([
            'role' => UserRole::Admin,
        ]);

        $clinic = ClinicSetting::create([
            'clinic_name' => 'Aura Dental Studio Inicial',
            'tagline' => 'Eslogan inicial',
            'phone' => '+1 (555) 000-0000',
            'whatsapp' => '+15550000000',
            'email' => 'old@auradental.com',
            'address' => 'Dirección antigua',
            'opening_hours' => 'Lunes a Viernes: 8:00 AM - 5:00 PM',
            'show_language_switcher' => false,
            'social_links' => [
                'instagram' => 'https://instagram.com/old',
            ],
        ]);

        Storage::fake('public');

        $logoFile = UploadedFile::fake()->image('logo.png', 400, 200);
        $faviconFile = UploadedFile::fake()->image('favicon.png', 64, 64);

        $this->actingAs($admin);

        Livewire::test(ManageClinicSettings::class)
            ->callAction('edit', [
                'clinic_name' => 'Aura Dental Studio Premium',
                'tagline' => 'Excelencia y vanguardia dental',
                'logo_url' => $logoFile,
                'favicon_url' => $faviconFile,
                'phone' => '+1 (555) 999-8888',
                'email' => 'nuevo@auradental.com',
                'emergency_phone' => '+1 (555) 911-0000',
                'address' => 'Torre Médica Aura, Piso 8',
                'google_maps_url' => 'https://maps.google.com/?q=AuraDental',
                'opening_hours' => "Lunes a Viernes: 8:00 AM - 8:00 PM\nSábado: 9:00 AM - 4:00 PM",
                'social_links' => [
                    'instagram' => 'https://instagram.com/aurapremium',
                    'facebook' => 'https://facebook.com/aurapremium',
                    'tiktok' => 'https://tiktok.com/@aurapremium',
                ],
            ])
            ->assertHasNoActionErrors();

        $clinic->refresh();

        $this->assertEquals('Aura Dental Studio Premium', $clinic->clinic_name);
        $this->assertEquals('Excelencia y vanguardia dental', $clinic->tagline);
        $this->assertNotNull($clinic->logo_url);
        $this->assertNotNull($clinic->favicon_url);
        $this->assertStringContainsString('storage/', $clinic->logo_asset_url);
        $this->assertStringContainsString('storage/', $clinic->favicon_asset_url);
        $this->assertEquals('+1 (555) 999-8888', $clinic->phone);
        $this->assertEquals('+15550000000', $clinic->whatsapp); // Preserved in DB
        $this->assertEquals('nuevo@auradental.com', $clinic->email);
        $this->assertEquals('+1 (555) 911-0000', $clinic->emergency_phone);
        $this->assertEquals('Torre Médica Aura, Piso 8', $clinic->address);
        $this->assertEquals('https://instagram.com/aurapremium', $clinic->social_links['instagram']);
    }
}

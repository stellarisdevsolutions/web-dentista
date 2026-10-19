<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Filament\Resources\Services\Pages\CreateService;
use App\Filament\Resources\Services\Pages\ListServices;
use App\Models\Dentist;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class ServiceSlugAndValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_generate_unique_slug_transliterates_accents_and_special_characters(): void
    {
        $this->assertEquals('blanqueamiento-laser', Service::generateUniqueSlug('Blanqueamiento Láser'));
        $this->assertEquals('cirugia-e-implantes', Service::generateUniqueSlug('Cirugía e Implantes'));
        $this->assertEquals('endodoncia-rehabilitacion-oral', Service::generateUniqueSlug('Endodoncia & Rehabilitación Oral'));
    }

    public function test_generate_unique_slug_appends_incremental_counter_when_collision_exists(): void
    {
        Service::factory()->create([
            'name' => 'Limpieza Dental',
            'slug' => 'limpieza-dental',
        ]);

        $secondSlug = Service::generateUniqueSlug('Limpieza Dental');
        $this->assertEquals('limpieza-dental-1', $secondSlug);

        Service::factory()->create([
            'name' => 'Limpieza Dental Avanzada',
            'slug' => 'limpieza-dental-1',
        ]);

        $thirdSlug = Service::generateUniqueSlug('Limpieza Dental');
        $this->assertEquals('limpieza-dental-2', $thirdSlug);
    }

    public function test_generate_unique_slug_ignores_current_record_id_when_editing(): void
    {
        $service = Service::factory()->create([
            'name' => 'Ortodoncia Invisible',
            'slug' => 'ortodoncia-invisible',
        ]);

        $slugWhenEditingSelf = Service::generateUniqueSlug('Ortodoncia Invisible', $service->id);
        $this->assertEquals('ortodoncia-invisible', $slugWhenEditingSelf);
    }

    public function test_service_model_preserves_published_slug_when_name_changes_in_hybrid_mode(): void
    {
        $service = Service::create([
            'name' => 'Carillas de Porcelana',
            'slug' => 'carillas-de-porcelana',
            'category' => 'Estética',
        ]);

        // When name updates without touching slug, the original URL slug remains intact
        $service->update([
            'name' => 'Carillas de Porcelana Ultra Finas',
        ]);

        $this->assertEquals('carillas-de-porcelana', $service->fresh()->slug);
        $this->assertEquals('Carillas de Porcelana Ultra Finas', $service->fresh()->name);
    }

    public function test_service_model_updates_slug_when_slug_is_explicitly_changed(): void
    {
        $service = Service::create([
            'name' => 'Carillas de Porcelana',
            'slug' => 'carillas-de-porcelana',
            'category' => 'Estética',
        ]);

        $service->update([
            'slug' => 'carillas-esteticas',
        ]);

        $this->assertEquals('carillas-esteticas', $service->fresh()->slug);
    }

    public function test_filament_create_service_requires_image(): void
    {
        $user = User::factory()->create([
            'role' => UserRole::Admin,
        ]);

        $this->actingAs($user);

        Livewire::test(CreateService::class)
            ->fillForm([
                'name' => 'Profilaxis Simple',
                'slug' => 'profilaxis-simple',
                // 'image' left empty intentionally
            ])
            ->call('create')
            ->assertHasErrors(['data.image']);
    }

    public function test_filament_create_service_succeeds_with_valid_image_and_data(): void
    {
        Storage::fake('public');

        $user = User::factory()->create([
            'role' => UserRole::Admin,
        ]);

        $this->actingAs($user);

        $file = UploadedFile::fake()->image('treatment.jpg', 1200, 900);

        Livewire::test(CreateService::class)
            ->fillForm([
                'name' => 'Blanqueamiento Premium',
                'slug' => 'blanqueamiento-premium',
                'category' => 'Estética',
                'image' => $file,
                'price' => 200,
                'duration_minutes' => 60,
                'order' => 1,
            ])
            ->call('create')
            ->assertHasNoErrors();

        $service = Service::where('slug', 'blanqueamiento-premium')->first();
        $this->assertNotNull($service);
        $this->assertEquals('Blanqueamiento Premium', $service->name);
        $this->assertNotNull($service->image);
    }

    public function test_list_services_get_service_details_returns_payload_for_slide_over_drawer(): void
    {
        $user = User::factory()->create([
            'role' => UserRole::Admin,
        ]);

        $this->actingAs($user);

        $dentist = Dentist::factory()->create([
            'name' => 'Dr. Robert Martinez',
        ]);

        $service = Service::create([
            'name' => 'Diseño de Sonrisa 3D',
            'slug' => 'diseno-de-sonrisa-3d',
            'category' => 'Estética Avanzada',
            'short_description' => 'Simulación digital de sonrisa',
            'description' => 'Fases clínicas completas y fotografía dental de alta resolución.',
            'price' => 500.00,
            'duration_minutes' => 90,
            'allows_online_booking' => true,
            'is_featured' => true,
            'is_active' => true,
            'order' => 2,
        ]);

        $service->dentists()->attach($dentist->id);

        $component = Livewire::test(ListServices::class);
        $details = $component->instance()->getServiceDetails($service->id);

        $this->assertNotNull($details);
        $this->assertEquals($service->id, $details['id']);
        $this->assertEquals('Diseño de Sonrisa 3D', $details['name']);
        $this->assertEquals('diseno-de-sonrisa-3d', $details['slug']);
        $this->assertEquals('Estética Avanzada', $details['category']);
        $this->assertEquals('$500.00', $details['formatted_price']);
        $this->assertEquals(90, $details['duration_minutes']);
        $this->assertCount(1, $details['dentists']);
        $this->assertEquals('Dr. Robert Martinez', $details['dentists'][0]['name']);
        $this->assertStringContainsString('/admin/services/'.$service->id.'/edit', $details['edit_url']);
    }
}

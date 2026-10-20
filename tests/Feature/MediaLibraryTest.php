<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\MediaAsset;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MediaLibraryTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_list_media_assets(): void
    {
        $admin = User::factory()->create([
            'role' => UserRole::Admin,
        ]);

        MediaAsset::create([
            'filename' => 'clinica-fachada.jpg',
            'file_path' => 'media/clinica-fachada.jpg',
            'disk' => 'public',
            'mime_type' => 'image/jpeg',
            'file_size' => 102400,
            'title' => 'Fachada Clínica',
        ]);

        $response = $this->actingAs($admin)->getJson('/admin/media-library');

        $response->assertSuccessful();
        $response->assertJsonStructure([
            'success',
            'assets' => [
                '*' => ['id', 'filename', 'url', 'formatted_size'],
            ],
        ]);
        $response->assertSee('clinica-fachada.jpg');
    }

    public function test_admin_can_upload_image_to_media_library(): void
    {
        Storage::fake('public');

        $admin = User::factory()->create([
            'role' => UserRole::Admin,
        ]);

        $file = UploadedFile::fake()->image('equipo-quirurgico.png', 800, 600);

        $response = $this->actingAs($admin)->postJson('/admin/media-library', [
            'file' => $file,
            'title' => 'Equipo Quirúrgico Dental',
        ]);

        $response->assertStatus(201);
        $response->assertJson([
            'success' => true,
        ]);

        $this->assertDatabaseHas('media_assets', [
            'filename' => 'equipo-quirurgico.png',
            'title' => 'Equipo Quirúrgico Dental',
            'width' => 800,
            'height' => 600,
        ]);
    }

    public function test_admin_can_delete_media_asset(): void
    {
        Storage::fake('public');

        $admin = User::factory()->create([
            'role' => UserRole::Admin,
        ]);

        $file = UploadedFile::fake()->image('foto-a-borrar.jpg');
        $path = $file->store('media', 'public');

        $asset = MediaAsset::create([
            'filename' => 'foto-a-borrar.jpg',
            'file_path' => $path,
            'disk' => 'public',
            'mime_type' => 'image/jpeg',
            'file_size' => 50000,
        ]);

        $this->assertTrue(Storage::disk('public')->exists($path));

        $response = $this->actingAs($admin)->deleteJson("/admin/media-library/{$asset->id}");

        $response->assertSuccessful();
        $response->assertJson(['success' => true]);

        $this->assertDatabaseMissing('media_assets', [
            'id' => $asset->id,
        ]);
        $this->assertFalse(Storage::disk('public')->exists($path));
    }

    public function test_non_admin_cannot_access_media_library(): void
    {
        $receptionist = User::factory()->create([
            'role' => UserRole::Receptionist,
        ]);

        $response = $this->actingAs($receptionist)->getJson('/admin/media-library');

        $response->assertForbidden();
    }

    public function test_admin_can_update_asset_metadata_title_and_alt_text(): void
    {
        $admin = User::factory()->create([
            'role' => UserRole::Admin,
        ]);

        $asset = MediaAsset::create([
            'filename' => 'sonrisa-perfecta.jpg',
            'file_path' => 'media/sonrisa-perfecta.jpg',
            'disk' => 'public',
            'mime_type' => 'image/jpeg',
            'file_size' => 45000,
            'title' => 'Título Inicial',
            'alt_text' => 'Alt Inicial',
        ]);

        $response = $this->actingAs($admin)->patchJson("/admin/media-library/{$asset->id}", [
            'title' => 'Diseño de Sonrisa Digital 3D',
            'alt_text' => 'Paciente sonriendo tras blanqueamiento dental',
        ]);

        $response->assertSuccessful();
        $response->assertJson([
            'success' => true,
        ]);

        $this->assertDatabaseHas('media_assets', [
            'id' => $asset->id,
            'title' => 'Diseño de Sonrisa Digital 3D',
            'alt_text' => 'Paciente sonriendo tras blanqueamiento dental',
        ]);
    }

    public function test_admin_can_batch_upload_multiple_images(): void
    {
        Storage::fake('public');

        $admin = User::factory()->create([
            'role' => UserRole::Admin,
        ]);

        $files = [
            UploadedFile::fake()->image('foto1.jpg', 600, 400),
            UploadedFile::fake()->image('foto2.png', 800, 600),
        ];

        $response = $this->actingAs($admin)->postJson('/admin/media-library', [
            'files' => $files,
        ]);

        $response->assertStatus(201);
        $response->assertJson([
            'success' => true,
        ]);

        $this->assertDatabaseHas('media_assets', [
            'filename' => 'foto1.jpg',
        ]);
        $this->assertDatabaseHas('media_assets', [
            'filename' => 'foto2.png',
        ]);
    }

    public function test_admin_can_sync_existing_clinic_images(): void
    {
        Storage::fake('public');

        $admin = User::factory()->create([
            'role' => UserRole::Admin,
        ]);

        Storage::disk('public')->put('dentists/dr-ana-martinez.jpg', 'fake-image-bytes');

        $response = $this->actingAs($admin)->postJson('/admin/media-library/sync');

        $response->assertSuccessful();
        $response->assertJson([
            'success' => true,
            'synced_count' => 1,
        ]);

        $this->assertDatabaseHas('media_assets', [
            'file_path' => 'dentists/dr-ana-martinez.jpg',
            'filename' => 'dr-ana-martinez.jpg',
        ]);
    }

    public function test_admin_can_access_standalone_media_library_page(): void
    {
        $admin = User::factory()->create([
            'role' => UserRole::Admin,
        ]);

        $response = $this->actingAs($admin)->get('/admin/media');

        $response->assertSuccessful();
        $response->assertSee('Biblioteca Multimedia de la Clínica');
        $response->assertSee('Abrir Biblioteca');
    }
}

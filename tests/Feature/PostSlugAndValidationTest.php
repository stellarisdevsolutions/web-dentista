<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Filament\Resources\Posts\Pages\CreatePost;
use App\Filament\Resources\Posts\Pages\ListPosts;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class PostSlugAndValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_generate_unique_slug_transliterates_accents_and_special_characters(): void
    {
        $this->assertEquals('higiene-y-salud-bucal', Post::generateUniqueSlug('Higiene y Salud Bucal'));
        $this->assertEquals('cuidado-de-encias-prevencion', Post::generateUniqueSlug('Cuidado de Encías & Prevención'));
        $this->assertEquals('guia-de-ortodoncia-para-ninos', Post::generateUniqueSlug('Guía de Ortodoncia para Niños'));
    }

    public function test_generate_unique_slug_appends_incremental_counter_when_collision_exists(): void
    {
        Post::factory()->create([
            'title' => 'Cuidado Bucal Diario',
            'slug' => 'cuidado-bucal-diario',
        ]);

        $secondSlug = Post::generateUniqueSlug('Cuidado Bucal Diario');
        $this->assertEquals('cuidado-bucal-diario-1', $secondSlug);

        Post::factory()->create([
            'title' => 'Cuidado Bucal Diario Avanzado',
            'slug' => 'cuidado-bucal-diario-1',
        ]);

        $thirdSlug = Post::generateUniqueSlug('Cuidado Bucal Diario');
        $this->assertEquals('cuidado-bucal-diario-2', $thirdSlug);
    }

    public function test_generate_unique_slug_ignores_current_record_id_when_editing(): void
    {
        $post = Post::factory()->create([
            'title' => 'Prevención de Caries',
            'slug' => 'prevencion-de-caries',
        ]);

        $slugWhenEditingSelf = Post::generateUniqueSlug('Prevención de Caries', $post->id);
        $this->assertEquals('prevencion-de-caries', $slugWhenEditingSelf);
    }

    public function test_post_model_preserves_published_slug_when_title_changes_in_hybrid_mode(): void
    {
        $post = Post::create([
            'title' => 'Mitos y Verdades del Blanqueamiento',
            'slug' => 'mitos-y-verdades-del-blanqueamiento',
            'category' => 'Estética',
            'content' => 'Contenido del artículo...',
        ]);

        $post->update([
            'title' => 'Mitos y Verdades Actualizadas del Blanqueamiento',
        ]);

        $this->assertEquals('mitos-y-verdades-del-blanqueamiento', $post->fresh()->slug);
        $this->assertEquals('Mitos y Verdades Actualizadas del Blanqueamiento', $post->fresh()->title);
    }

    public function test_post_model_updates_slug_when_slug_is_explicitly_changed(): void
    {
        $post = Post::create([
            'title' => 'Mitos del Blanqueamiento',
            'slug' => 'mitos-del-blanqueamiento',
            'category' => 'Estética',
            'content' => 'Contenido del artículo...',
        ]);

        $post->update([
            'slug' => 'blanqueamiento-mitos-reales',
        ]);

        $this->assertEquals('blanqueamiento-mitos-reales', $post->fresh()->slug);
    }

    public function test_filament_create_post_requires_cover_image(): void
    {
        $user = User::factory()->create([
            'role' => UserRole::Admin,
        ]);

        $this->actingAs($user);

        Livewire::test(CreatePost::class)
            ->fillForm([
                'title' => 'Beneficios del Hilo Dental',
                'slug' => 'beneficios-del-hilo-dental',
                'content' => 'Explicación detallada...',
                // 'cover_image' left empty intentionally
            ])
            ->call('create')
            ->assertHasErrors(['data.cover_image']);
    }

    public function test_filament_create_post_succeeds_with_valid_image_and_data(): void
    {
        Storage::fake('public');

        $user = User::factory()->create([
            'role' => UserRole::Admin,
        ]);

        $this->actingAs($user);

        $file = UploadedFile::fake()->image('post-cover.jpg', 1280, 720);

        Livewire::test(CreatePost::class)
            ->fillForm([
                'title' => 'Importancia de la Limpieza Dental',
                'slug' => 'importancia-de-la-limpieza-dental',
                'category' => 'Prevención',
                'cover_image' => $file,
                'excerpt' => 'Resumen sobre limpiezas semestrales.',
                'content' => '<p>La limpieza dental previene enfermedades periodontales...</p>',
                'published_at' => now()->toDateTimeString(),
                'is_published' => true,
            ])
            ->call('create')
            ->assertHasNoErrors();

        $post = Post::where('slug', 'importancia-de-la-limpieza-dental')->first();
        $this->assertNotNull($post);
        $this->assertEquals('Importancia de la Limpieza Dental', $post->title);
        $this->assertNotNull($post->cover_image);
    }

    public function test_list_posts_get_post_details_returns_payload_for_slide_over_drawer(): void
    {
        $user = User::factory()->create([
            'role' => UserRole::Admin,
        ]);

        $this->actingAs($user);

        $post = Post::create([
            'title' => 'Todo sobre Implantes Dentales',
            'slug' => 'todo-sobre-implantes-dentales',
            'category' => 'Implantología',
            'excerpt' => 'Guía completa sobre el proceso de colocación de implantes.',
            'content' => '<p>Los implantes de titanio son la solución más duradera...</p>',
            'published_at' => now(),
            'is_published' => true,
        ]);

        $component = Livewire::test(ListPosts::class);
        $details = $component->instance()->getPostDetails($post->id);

        $this->assertNotNull($details);
        $this->assertEquals($post->id, $details['id']);
        $this->assertEquals('Todo sobre Implantes Dentales', $details['title']);
        $this->assertEquals('todo-sobre-implantes-dentales', $details['slug']);
        $this->assertEquals('Implantología', $details['category']);
        $this->assertStringContainsString('/blog/todo-sobre-implantes-dentales', $details['display_url']);
        $this->assertStringContainsString('/admin/posts/'.$post->id.'/edit', $details['edit_url']);
    }

    public function test_post_form_renders_dynamic_prefix_from_app_url(): void
    {
        $user = User::factory()->create([
            'role' => UserRole::Admin,
        ]);

        $this->actingAs($user);

        $response = $this->get('/admin/posts/create');
        $response->assertSuccessful();

        $appUrl = config('app.url', 'http://localhost:8000');
        $host = preg_replace('#^https?://#', '', rtrim((string) $appUrl, '/'));
        $expectedPrefix = $host.'/blog/';

        $response->assertSee($expectedPrefix);
    }

    public function test_posts_table_toggle_is_published_sends_notification_toast(): void
    {
        $user = User::factory()->create([
            'role' => UserRole::Admin,
        ]);

        $this->actingAs($user);

        $post = Post::factory()->create([
            'title' => 'Guía de Cepillado Correcto',
            'is_published' => true,
        ]);

        // Toggle from published to draft
        Livewire::test(ListPosts::class)
            ->call('updateTableColumnState', 'is_published', (string) $post->getKey(), false)
            ->assertNotified('Artículo en borrador');

        $this->assertFalse($post->fresh()->is_published);

        // Toggle from draft to published
        Livewire::test(ListPosts::class)
            ->call('updateTableColumnState', 'is_published', (string) $post->getKey(), true)
            ->assertNotified('Artículo publicado');

        $this->assertTrue($post->fresh()->is_published);
    }
}

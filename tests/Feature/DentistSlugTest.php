<?php

namespace Tests\Feature;

use App\Models\Dentist;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DentistSlugTest extends TestCase
{
    use RefreshDatabase;

    public function test_generate_unique_slug_transliterates_accents_and_special_characters(): void
    {
        $this->assertEquals('juan-jose', Dentist::generateUniqueSlug('Juan José'));
        $this->assertEquals('angel-munoz', Dentist::generateUniqueSlug('Ángel Muñoz'));
        $this->assertEquals('dra-maria-belen-gonzalez', Dentist::generateUniqueSlug('Dra. María Belén González'));
    }

    public function test_generate_unique_slug_appends_incremental_counter_when_collision_exists(): void
    {
        Dentist::factory()->create([
            'name' => 'Juan José',
            'slug' => 'juan-jose',
        ]);

        $secondSlug = Dentist::generateUniqueSlug('Juan José');
        $this->assertEquals('juan-jose-1', $secondSlug);

        Dentist::factory()->create([
            'name' => 'Juan José Segundo',
            'slug' => 'juan-jose-1',
        ]);

        $thirdSlug = Dentist::generateUniqueSlug('Juan José');
        $this->assertEquals('juan-jose-2', $thirdSlug);
    }

    public function test_generate_unique_slug_ignores_current_record_id_when_editing(): void
    {
        $dentist = Dentist::factory()->create([
            'name' => 'Juan José',
            'slug' => 'juan-jose',
        ]);

        $slugWhenEditingSelf = Dentist::generateUniqueSlug('Juan José', $dentist->id);
        $this->assertEquals('juan-jose', $slugWhenEditingSelf);
    }

    public function test_dentist_model_automatically_assigns_unique_slug_on_save(): void
    {
        $d1 = Dentist::create([
            'name' => 'Juan José Pérez',
            'title' => 'Cirujano Dentista',
            'specialty' => 'Odontología General',
        ]);

        $this->assertEquals('juan-jose-perez', $d1->slug);

        $d2 = Dentist::create([
            'name' => 'Juan José Pérez',
            'title' => 'Especialista en Ortodoncia',
            'specialty' => 'Ortodoncia',
        ]);

        $this->assertEquals('juan-jose-perez-1', $d2->slug);

        $d3 = Dentist::create([
            'name' => 'Juan José Pérez',
            'title' => 'Endodoncista',
            'specialty' => 'Endodoncia',
        ]);

        $this->assertEquals('juan-jose-perez-2', $d3->slug);
    }
}

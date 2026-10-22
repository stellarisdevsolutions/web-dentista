<?php

namespace Tests\Feature;

use App\Models\ClinicSetting;
use App\Models\Service;
use App\Models\SiteSection;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeederLocalizationTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        putenv('SEEDER_LOCALE');
        parent::tearDown();
    }

    public function test_database_seeder_populates_spanish_data_when_locale_is_es(): void
    {
        putenv('SEEDER_LOCALE=es');
        config(['app.locale' => 'es']);

        $this->seed(DatabaseSeeder::class);

        // Check Clinic Settings
        $clinic = ClinicSetting::first();
        $this->assertNotNull($clinic);
        $this->assertEquals('Aura Dental Studio', $clinic->clinic_name);
        $this->assertStringContainsString('Odontología moderna', $clinic->tagline);
        $this->assertStringContainsString('Lunes a Viernes', $clinic->opening_hours);

        // Check Services
        $checkup = Service::where('slug', 'general-checkup-consultation')->first();
        $this->assertNotNull($checkup);
        $this->assertEquals('Revisión Dental General y Diagnóstico', $checkup->name);
        $this->assertEquals('Odontología General', $checkup->category);
        $this->assertEquals(1, $checkup->order);

        $service = Service::where('slug', 'comprehensive-exam-cleaning')->first();
        $this->assertNotNull($service);
        $this->assertEquals('Examen Integral y Profilaxis Profunda', $service->name);
        $this->assertEquals('General y Prevención', $service->category);

        // Check Site Sections
        $hero = SiteSection::where('page', 'home')->where('section_key', 'hero')->first();
        $this->assertNotNull($hero);
        $this->assertEquals('Portada Principal (Hero)', $hero->name);
        $this->assertEquals('Odontología Suave, Sin Dolor para Toda tu Familia.', $hero->content['title']);
    }

    public function test_database_seeder_populates_english_data_when_locale_is_en(): void
    {
        putenv('SEEDER_LOCALE=en');
        config(['app.locale' => 'en']);

        $this->seed(DatabaseSeeder::class);

        // Check Clinic Settings
        $clinic = ClinicSetting::first();
        $this->assertNotNull($clinic);
        $this->assertEquals('Aura Dental Studio', $clinic->clinic_name);
        $this->assertStringContainsString('Modern, comprehensive, and pain-free dentistry', $clinic->tagline);
        $this->assertStringContainsString('Monday to Friday', $clinic->opening_hours);

        // Check Services
        $checkup = Service::where('slug', 'general-checkup-consultation')->first();
        $this->assertNotNull($checkup);
        $this->assertEquals('General Dental Checkup & Consultation', $checkup->name);
        $this->assertEquals('General Dentistry', $checkup->category);
        $this->assertEquals(1, $checkup->order);

        $service = Service::where('slug', 'comprehensive-exam-cleaning')->first();
        $this->assertNotNull($service);
        $this->assertEquals('Comprehensive Exam & 3D Prophylaxis', $service->name);
        $this->assertEquals('General & Prevention', $service->category);

        // Check Site Sections
        $hero = SiteSection::where('page', 'home')->where('section_key', 'hero')->first();
        $this->assertNotNull($hero);
        $this->assertEquals('Main Hero Section', $hero->name);
        $this->assertEquals('Gentle, Pain-Free Dentistry for Your Whole Family.', $hero->content['title']);
    }

    public function test_seeder_locale_env_takes_precedence(): void
    {
        config(['app.locale' => 'es']);
        putenv('SEEDER_LOCALE=en');

        $this->seed(DatabaseSeeder::class);

        $service = Service::where('slug', 'comprehensive-exam-cleaning')->first();
        $this->assertNotNull($service);
        $this->assertEquals('Comprehensive Exam & 3D Prophylaxis', $service->name);
    }
}

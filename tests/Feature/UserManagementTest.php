<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Filament\Resources\Users\Pages\CreateUser;
use App\Filament\Resources\Users\Pages\ListUsers;
use App\Models\Dentist;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_initials_accessor_extracts_two_uppercase_letters(): void
    {
        $user1 = new User(['name' => 'Carlos Mendoza']);
        $this->assertEquals('CM', $user1->initials);

        $user2 = new User(['name' => 'Dra. María Elena']);
        $this->assertEquals('DM', $user2->initials);

        $user3 = new User(['name' => 'Admin']);
        $this->assertEquals('AD', $user3->initials);
    }

    public function test_filament_create_user_requires_name_email_and_password(): void
    {
        $admin = User::factory()->create([
            'role' => UserRole::Admin,
        ]);

        $this->actingAs($admin);

        Livewire::test(CreateUser::class)
            ->fillForm([
                'name' => '',
                'email' => '',
                'password' => '',
            ])
            ->call('create')
            ->assertHasErrors(['data.name', 'data.email', 'data.password']);
    }

    public function test_filament_create_user_succeeds_and_hashes_password(): void
    {
        $admin = User::factory()->create([
            'role' => UserRole::Admin,
        ]);

        $this->actingAs($admin);

        Livewire::test(CreateUser::class)
            ->fillForm([
                'name' => 'Lucía Recepcionista',
                'email' => 'lucia@auradental.com',
                'password' => 'secret12345',
                'role' => UserRole::Receptionist->value,
            ])
            ->call('create')
            ->assertHasNoErrors();

        $user = User::where('email', 'lucia@auradental.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals('Lucía Recepcionista', $user->name);
        $this->assertEquals(UserRole::Receptionist, $user->role);
        $this->assertTrue(Hash::check('secret12345', $user->password));
    }

    public function test_filament_create_dentist_user_requires_and_links_dentist_id(): void
    {
        $admin = User::factory()->create([
            'role' => UserRole::Admin,
        ]);

        $this->actingAs($admin);

        $dentist = Dentist::factory()->create([
            'name' => 'Dr. Fernando Gómez',
        ]);

        // 1. Role Dentist without dentist_id fails
        Livewire::test(CreateUser::class)
            ->fillForm([
                'name' => 'Dr. Fernando Gómez',
                'email' => 'dr.gomez@auradental.com',
                'password' => 'dentistpass123',
                'role' => UserRole::Dentist->value,
                'dentist_id' => null,
            ])
            ->call('create')
            ->assertHasErrors(['data.dentist_id']);

        // 2. Role Dentist with dentist_id succeeds
        Livewire::test(CreateUser::class)
            ->fillForm([
                'name' => 'Dr. Fernando Gómez',
                'email' => 'dr.gomez@auradental.com',
                'password' => 'dentistpass123',
                'role' => UserRole::Dentist->value,
                'dentist_id' => $dentist->id,
            ])
            ->call('create')
            ->assertHasNoErrors();

        $user = User::where('email', 'dr.gomez@auradental.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals($dentist->id, $user->dentist_id);
    }

    public function test_filament_selecting_dentist_automatically_populates_user_name(): void
    {
        $admin = User::factory()->create([
            'role' => UserRole::Admin,
        ]);

        $this->actingAs($admin);

        $dentist = Dentist::factory()->create([
            'name' => 'Dr. Roberto Canales',
        ]);

        Livewire::test(CreateUser::class)
            ->set('data.role', UserRole::Dentist->value)
            ->set('data.dentist_id', $dentist->id)
            ->assertSet('data.name', 'Dr. Roberto Canales');
    }

    public function test_list_users_get_user_details_returns_payload_for_slide_over_drawer(): void
    {
        $admin = User::factory()->create([
            'role' => UserRole::Admin,
        ]);

        $this->actingAs($admin);

        $dentist = Dentist::factory()->create([
            'name' => 'Dra. Gabriela Vega',
            'specialty' => 'Periodoncia',
        ]);

        $user = User::factory()->create([
            'name' => 'Dra. Gabriela Vega',
            'email' => 'gaby@auradental.com',
            'role' => UserRole::Dentist,
            'dentist_id' => $dentist->id,
        ]);

        $component = Livewire::test(ListUsers::class);
        $details = $component->instance()->getUserDetails($user->id);

        $this->assertNotNull($details);
        $this->assertEquals($user->id, $details['id']);
        $this->assertEquals('Dra. Gabriela Vega', $details['name']);
        $this->assertEquals('gaby@auradental.com', $details['email']);
        $this->assertEquals('dentist', $details['role']);
        $this->assertEquals('Odontólogo', $details['role_label']);
        $this->assertNotNull($details['dentist']);
        $this->assertEquals('Periodoncia', $details['dentist']['specialty']);
        $this->assertStringContainsString('/admin/users/'.$user->id.'/edit', $details['edit_url']);
    }
}

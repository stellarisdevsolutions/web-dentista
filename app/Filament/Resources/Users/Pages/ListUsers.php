<?php

namespace App\Filament\Resources\Users\Pages;

use App\Enums\UserRole;
use App\Filament\Resources\Users\UserResource;
use App\Models\User;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Livewire\Attributes\Renderless;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('+ '.__('New User'))
                ->extraAttributes([
                    'style' => 'border-radius: 9999px !important; background-color: #0EA5E9 !important; color: #0F172A !important; font-weight: 800 !important; min-height: 48px !important; padding: 0 24px !important; box-shadow: 0 10px 24px rgba(14, 165, 233, 0.40) !important; border: none !important; font-size: 14px !important;',
                ]),
        ];
    }

    /**
     * Fetch user details for the instant slide-over drawer.
     *
     * @return array<string, mixed>|null
     */
    #[Renderless]
    public function getUserDetails(int $id): ?array
    {
        try {
            /** @var User|null $user */
            $user = User::with('dentist')->find($id);

            if (! $user) {
                return null;
            }

            $roleDescription = match ($user->role) {
                UserRole::Admin => __('Full and unrestricted access to all clinical modules, reports, system settings, and user management.'),
                UserRole::Receptionist => __('Authorized for patient reception, appointment scheduling and confirmation, and service catalog.'),
                UserRole::Dentist => __('Personalized access focused on own medical schedule and consultation of assigned patient appointments.'),
                default => __('Role with no specific permissions defined.'),
            };

            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'initials' => $user->initials,
                'role' => $user->role->value,
                'role_label' => $user->role->label(),
                'role_color' => $user->role->color(),
                'role_description' => $roleDescription,
                'is_current_user' => auth()->id() === $user->id,
                'dentist' => $user->dentist ? [
                    'id' => $user->dentist->id,
                    'name' => $user->dentist->name,
                    'title' => $user->dentist->title,
                    'specialty' => $user->dentist->specialty,
                    'photo_url' => $user->dentist->photo_url,
                    'initials' => $user->dentist->initials,
                ] : null,
                'created_at' => $user->created_at?->format('d M, Y H:i'),
                'created_at_human' => $user->created_at?->locale(app()->getLocale())->diffForHumans(),
                'edit_url' => UserResource::getUrl('edit', ['record' => $user->id]),
            ];
        } catch (\Throwable $e) {
            report($e);

            return null;
        }
    }
}

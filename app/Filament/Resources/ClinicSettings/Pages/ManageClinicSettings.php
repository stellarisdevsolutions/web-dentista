<?php

namespace App\Filament\Resources\ClinicSettings\Pages;

use App\Filament\Resources\ClinicSettings\ClinicSettingResource;
use App\Filament\Resources\ClinicSettings\Schemas\ClinicSettingForm;
use App\Models\ClinicSetting;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Filament\Support\Enums\Width;

class ManageClinicSettings extends Page
{
    protected static string $resource = ClinicSettingResource::class;

    protected string $view = 'filament.resources.clinic-settings.pages.manage-clinic-settings';

    public ?ClinicSetting $record = null;

    public function mount(): void
    {
        $this->loadRecord();
    }

    public function loadRecord(): void
    {
        $this->record = ClinicSetting::first() ?? ClinicSetting::create([
            'clinic_name' => 'Aura Dental Studio',
            'tagline' => 'Odontología moderna, integral y sin dolor para sonrisas radiantes.',
            'phone' => '+1 (555) 234-5678',
            'whatsapp' => '+15552345678',
            'email' => 'hello@auradental.com',
            'address' => '742 Evergreen Terrace, Suite 100, Beverly Hills, CA 90210',
            'google_maps_url' => 'https://maps.google.com/?q=Beverly+Hills+CA',
            'opening_hours' => "Lunes a Viernes: 8:00 AM - 7:00 PM\nSábado: 9:00 AM - 3:00 PM\nDomingo: Solo Urgencias",
            'emergency_phone' => '+1 (555) 911-7775',
            'show_language_switcher' => false,
            'social_links' => [
                'instagram' => 'https://instagram.com/auradental',
                'facebook' => 'https://facebook.com/auradental',
                'tiktok' => 'https://tiktok.com/@auradental',
            ],
        ]);
    }

    public function getTitle(): string
    {
        return __('Clinic Profile & Settings');
    }

    public function getHeading(): string
    {
        return '';
    }

    public function getSubheading(): ?string
    {
        return null;
    }

    protected function getHeaderActions(): array
    {
        return [];
    }

    public function editAction(): Action
    {
        return Action::make('edit')
            ->label(__('Edit Settings'))
            ->icon('heroicon-m-pencil-square')
            ->color('primary')
            ->modalHeading(__('Edit Clinic Profile & Settings'))
            ->modalDescription(__('Update institutional data, contact phones, opening hours, and social media links.'))
            ->modalWidth(Width::FourExtraLarge)
            ->modalSubmitActionLabel(__('Save Changes'))
            ->fillForm(function (): array {
                $this->loadRecord();

                return [
                    'clinic_name' => $this->record->clinic_name,
                    'tagline' => $this->record->tagline,
                    'logo_url' => $this->record->logo_url,
                    'favicon_url' => $this->record->favicon_url,
                    'phone' => $this->record->phone,
                    'email' => $this->record->email,
                    'emergency_phone' => $this->record->emergency_phone,
                    'address' => $this->record->address,
                    'google_maps_url' => $this->record->google_maps_url,
                    'opening_hours' => $this->record->opening_hours,
                    'social_links' => $this->record->social_links ?? [],
                ];
            })
            ->form(ClinicSettingForm::schema())
            ->action(function (array $data): void {
                $this->loadRecord();
                $this->record->update($data);

                Notification::make()
                    ->title(__('Settings saved successfully'))
                    ->body(__('Clinic details have been updated throughout the system.'))
                    ->success()
                    ->send();

                $this->loadRecord();
            });
    }
}

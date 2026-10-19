<?php

namespace App\Filament\Resources\Services\Pages;

use App\Filament\Resources\Services\ServiceResource;
use App\Models\Service;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Livewire\Attributes\Renderless;

class ListServices extends ListRecords
{
    protected static string $resource = ServiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('+ Nuevo Tratamiento')
                ->extraAttributes([
                    'style' => 'border-radius: 9999px !important; background-color: #0EA5E9 !important; color: #0F172A !important; font-weight: 800 !important; min-height: 48px !important; padding: 0 24px !important; box-shadow: 0 10px 24px rgba(14, 165, 233, 0.40) !important; border: none !important; font-size: 14px !important;',
                ]),
        ];
    }

    /**
     * Fetch treatment details for the instant slide-over drawer.
     *
     * @return array<string, mixed>|null
     */
    #[Renderless]
    public function getServiceDetails(int $id): ?array
    {
        try {
            /** @var Service|null $service */
            $service = Service::with('dentists')->find($id);

            if (! $service) {
                return null;
            }

            return [
                'id' => $service->id,
                'name' => $service->name,
                'slug' => $service->slug,
                'category' => $service->category ?? 'Sin categoría',
                'short_description' => $service->short_description,
                'description' => $service->description,
                'price' => $service->price,
                'formatted_price' => $service->formatted_price,
                'duration_minutes' => $service->duration_minutes,
                'image_url' => $service->image_url,
                'allows_online_booking' => (bool) $service->allows_online_booking,
                'is_featured' => (bool) $service->is_featured,
                'is_active' => (bool) $service->is_active,
                'order' => $service->order,
                'dentists' => $service->dentists->map(fn ($d) => [
                    'id' => $d->id,
                    'name' => $d->name,
                    'title' => $d->title,
                    'specialty' => $d->specialty,
                    'initials' => $d->initials,
                    'photo_url' => $d->photo_url,
                ])->values()->all(),
                'public_url' => url('/servicios/'.$service->slug),
                'edit_url' => ServiceResource::getUrl('edit', ['record' => $service->id]),
            ];
        } catch (\Throwable $e) {
            report($e);

            return null;
        }
    }
}

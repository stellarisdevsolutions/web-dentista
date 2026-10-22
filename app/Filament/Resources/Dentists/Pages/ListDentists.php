<?php

namespace App\Filament\Resources\Dentists\Pages;

use App\Filament\Resources\Dentists\DentistResource;
use App\Models\Dentist;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Livewire\Attributes\Renderless;

class ListDentists extends ListRecords
{
    protected static string $resource = DentistResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('+ '.__('New Specialist'))
                ->extraAttributes([
                    'style' => 'border-radius: 9999px !important; background-color: #0EA5E9 !important; color: #0F172A !important; font-weight: 800 !important; min-height: 48px !important; padding: 0 24px !important; box-shadow: 0 10px 24px rgba(14, 165, 233, 0.40) !important; border: none !important; font-size: 14px !important;',
                ]),
        ];
    }

    /**
     * Fetch dentist details for the instant slide-over drawer.
     *
     * @return array<string, mixed>|null
     */
    #[Renderless]
    public function getDentistDetails(int $id): ?array
    {
        try {
            /** @var Dentist|null $dentist */
            $dentist = Dentist::with('services')->find($id);

            if (! $dentist) {
                return null;
            }

            $cleanName = preg_replace('/^(Dr\.|Dra\.|Doctor|Doctora)\s+/i', '', trim($dentist->name));
            $words = preg_split('/\s+/', trim((string) $cleanName));
            $initials = '';
            if (! empty($words)) {
                $first = mb_substr($words[0], 0, 1, 'UTF-8');
                $last = count($words) > 1 ? mb_substr(end($words), 0, 1, 'UTF-8') : '';
                $initials = strtoupper($first.$last);
            }

            $daysMap = [
                'monday' => __('Monday'),
                'tuesday' => __('Tuesday'),
                'wednesday' => __('Wednesday'),
                'thursday' => __('Thursday'),
                'friday' => __('Friday'),
                'saturday' => __('Saturday'),
            ];

            $scheduleDays = [];
            $weeklySchedule = $dentist->weekly_schedule ?? [];

            foreach ($daysMap as $dayKey => $dayName) {
                $dayConfig = $weeklySchedule[$dayKey] ?? null;
                $isWorking = false;
                $scheduleText = __('Non-working');

                if ($dayConfig && ! empty($dayConfig['is_working'])) {
                    $isWorking = true;
                    $parts = [];
                    if (! empty($dayConfig['morning_start']) && ! empty($dayConfig['morning_end'])) {
                        $parts[] = "{$dayConfig['morning_start']} - {$dayConfig['morning_end']}";
                    }
                    if (! empty($dayConfig['afternoon_start']) && ! empty($dayConfig['afternoon_end'])) {
                        $parts[] = "{$dayConfig['afternoon_start']} - {$dayConfig['afternoon_end']}";
                    }
                    $scheduleText = ! empty($parts) ? implode(' / ', $parts) : __('General hours');
                } elseif (empty($weeklySchedule) && $dentist->isAvailableOn($dayKey)) {
                    $isWorking = true;
                    $start = substr((string) ($dentist->working_hours_start ?? '09:00'), 0, 5);
                    $end = substr((string) ($dentist->working_hours_end ?? '18:00'), 0, 5);
                    $scheduleText = "{$start} - {$end}";
                }

                $scheduleDays[] = [
                    'day_key' => $dayKey,
                    'day_name' => $dayName,
                    'is_working' => $isWorking,
                    'schedule_text' => $scheduleText,
                ];
            }

            return [
                'id' => $dentist->id,
                'name' => $dentist->name,
                'initials' => $dentist->initials,
                'title' => $dentist->title,
                'specialty' => $dentist->specialty,
                'email' => $dentist->email,
                'phone' => $dentist->phone,
                'license_number' => $dentist->license_number,
                'bio' => $dentist->bio,
                'photo_url' => $dentist->photo_url,
                'is_active' => (bool) $dentist->is_active,
                'order' => $dentist->order,
                'services' => $dentist->services->map(fn ($s) => [
                    'id' => $s->id,
                    'name' => $s->name,
                ])->values()->all(),
                'schedule_days' => $scheduleDays,
                'edit_url' => DentistResource::getUrl('edit', ['record' => $dentist->id]),
            ];
        } catch (\Throwable $e) {
            report($e);

            return null;
        }
    }
}

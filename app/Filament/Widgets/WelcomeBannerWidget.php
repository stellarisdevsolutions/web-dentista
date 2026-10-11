<?php

namespace App\Filament\Widgets;

use App\Enums\UserRole;
use App\Filament\Resources\Appointments\AppointmentResource;
use App\Models\Appointment;
use App\Models\User;
use Carbon\Carbon;
use Filament\Widgets\Widget;

class WelcomeBannerWidget extends Widget
{
    protected static ?int $sort = 0;

    protected static bool $isLazy = false;

    protected int|string|array $columnSpan = 'full';

    protected string $view = 'filament.widgets.welcome-banner-widget';

    public function getColumnSpan(): int|string|array
    {
        return 'full';
    }

    /**
     * Get the greeting string based on the current hour.
     */
    public function getGreeting(): string
    {
        $hour = (int) Carbon::now()->format('H');

        return match (true) {
            $hour >= 5 && $hour < 12 => 'Buenos días',
            $hour >= 12 && $hour < 19 => 'Buenas tardes',
            default => 'Buenas noches',
        };
    }

    /**
     * Get clean user name without titles or prefixes.
     */
    public function getCleanUserName(?User $user): string
    {
        if (! $user) {
            return 'Usuario';
        }

        // Strip any accidental Dr., Dra., Sr., Sra. prefixes to enforce the rule strictly
        $name = preg_replace('/^(dr\.|dra\.|sr\.|sra\.|doctor|doctora)\s+/i', '', trim($user->name));

        return $name ?: 'Usuario';
    }

    /**
     * Build banner data payload depending on the authenticated user role.
     *
     * @return array{
     *     greeting: string,
     *     userName: string,
     *     subtitle: string,
     *     primaryButton: array{label: string, url: string},
     *     secondaryButton: array{label: string, url: string},
     *     cards: array<int, array{value: int, label: string, isHighlight?: bool}>
     * }
     */
    public function getViewData(): array
    {
        /** @var User|null $user */
        $user = auth()->user();
        $greeting = $this->getGreeting();
        $userName = $this->getCleanUserName($user);
        $role = $user?->role ?? UserRole::Admin;

        $today = Carbon::today();

        if ($role === UserRole::Dentist) {
            $dentistId = $user?->dentist_id;

            $dentistQuery = Appointment::query()
                ->where('dentist_id', $dentistId)
                ->whereDate('appointment_date', $today);

            $counts = (clone $dentistQuery)
                ->selectRaw('status, count(*) as total')
                ->groupBy('status')
                ->pluck('total', 'status');

            $myTodayCount = (int) $counts->sum();
            $myPendingCount = (int) ($counts['pending'] ?? 0);

            if ($myTodayCount === 0) {
                $subtitle = 'No tienes citas pendientes hoy.';
            } else {
                $currentTime = Carbon::now()->format('H:i');
                $nextAppt = (clone $dentistQuery)
                    ->whereIn('status', ['confirmed', 'pending', 'in_progress'])
                    ->where('appointment_time', '>=', $currentTime)
                    ->orderBy('appointment_time', 'asc')
                    ->first();

                if (! $nextAppt) {
                    $nextAppt = (clone $dentistQuery)
                        ->whereIn('status', ['confirmed', 'pending', 'in_progress'])
                        ->orderBy('appointment_time', 'asc')
                        ->first();
                }

                $nextTime = $nextAppt?->appointment_time ? Carbon::parse($nextAppt->appointment_time)->format('H:i') : '--:--';
                $pacientesText = $myTodayCount === 1 ? '1 paciente' : "{$myTodayCount} pacientes";
                $subtitle = "Tienes {$pacientesText} hoy. Tu próxima cita es a las {$nextTime}.";
            }

            return [
                'greeting' => $greeting,
                'userName' => $userName,
                'subtitle' => $subtitle,
                'primaryButton' => [
                    'label' => 'Ver mi agenda',
                    'url' => AppointmentResource::getUrl('index'),
                ],
                'secondaryButton' => [
                    'label' => 'Mis pacientes',
                    'url' => AppointmentResource::getUrl('index'),
                ],
                'cards' => [
                    [
                        'value' => $myTodayCount,
                        'label' => 'mis citas hoy',
                    ],
                    [
                        'value' => $myPendingCount,
                        'label' => 'por confirmar',
                        'isHighlight' => true,
                    ],
                ],
            ];
        }

        if ($role === UserRole::Receptionist) {
            $todayCounts = Appointment::query()
                ->whereDate('appointment_date', $today)
                ->selectRaw('status, count(*) as total')
                ->groupBy('status')
                ->pluck('total', 'status');

            $pendingCount = (int) ($todayCounts['pending'] ?? 0);
            $inProgressCount = (int) ($todayCounts['in_progress'] ?? 0);

            $citasText = $pendingCount === 1 ? '1 cita' : "{$pendingCount} citas";
            $pacientesText = $inProgressCount === 1 ? '1 paciente' : "{$inProgressCount} paciente(s)";
            $subtitle = "Hay {$citasText} por confirmar y {$pacientesText} en sala. Empieza por las pendientes.";

            return [
                'greeting' => $greeting,
                'userName' => $userName,
                'subtitle' => $subtitle,
                'primaryButton' => [
                    'label' => '+ Nueva cita',
                    'url' => AppointmentResource::getUrl('create'),
                ],
                'secondaryButton' => [
                    'label' => 'Confirmar pendientes',
                    'url' => AppointmentResource::getUrl('index', ['activeTab' => 'pending']),
                ],
                'cards' => [
                    [
                        'value' => $inProgressCount,
                        'label' => 'en sala ahora',
                    ],
                    [
                        'value' => $pendingCount,
                        'label' => 'por confirmar',
                        'isHighlight' => true,
                    ],
                ],
            ];
        }

        // Default: Admin
        $todayCounts = Appointment::query()
            ->whereDate('appointment_date', $today)
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $todayTotal = (int) $todayCounts->sum();
        $pendingCount = (int) ($todayCounts['pending'] ?? 0);

        return [
            'greeting' => $greeting,
            'userName' => $userName,
            'subtitle' => 'Monitorea citas, solicitudes de pacientes y especialistas en tiempo real.',
            'primaryButton' => [
                'label' => '+ Nueva cita',
                'url' => AppointmentResource::getUrl('create'),
            ],
            'secondaryButton' => [
                'label' => 'Ver agenda',
                'url' => AppointmentResource::getUrl('index'),
            ],
            'cards' => [
                [
                    'value' => $todayTotal,
                    'label' => 'citas hoy',
                ],
                [
                    'value' => $pendingCount,
                    'label' => 'por confirmar',
                    'isHighlight' => true,
                ],
            ],
        ];
    }
}

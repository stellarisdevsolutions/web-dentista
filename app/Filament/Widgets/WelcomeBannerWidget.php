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
            $hour >= 5 && $hour < 12 => __('Good morning'),
            $hour >= 12 && $hour < 19 => __('Good afternoon'),
            default => __('Good evening'),
        };
    }

    /**
     * Get clean user name without titles or prefixes.
     */
    public function getCleanUserName(?User $user): string
    {
        if (! $user) {
            return __('User');
        }

        // Strip any accidental Dr., Dra., Sr., Sra. prefixes to enforce the rule strictly
        $name = preg_replace('/^(dr\.|dra\.|sr\.|sra\.|doctor|doctora)\s+/i', '', trim($user->name));

        return $name ?: __('User');
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
                $subtitle = __('No pending appointments today.');
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

                $nextTime = Appointment::formatTime($nextAppt?->appointment_time);
                $pacientesText = $myTodayCount === 1 ? __('1 patient') : __(':count patients', ['count' => $myTodayCount]);
                $subtitle = __('You have :count today. Your next appointment is at :time.', [
                    'count' => $pacientesText,
                    'time' => $nextTime,
                ]);
            }

            return [
                'greeting' => $greeting,
                'userName' => $userName,
                'subtitle' => $subtitle,
                'primaryButton' => [
                    'label' => __('View my schedule'),
                    'url' => AppointmentResource::getUrl('index'),
                ],
                'secondaryButton' => [
                    'label' => __('My patients'),
                    'url' => AppointmentResource::getUrl('index'),
                ],
                'cards' => [
                    [
                        'value' => $myTodayCount,
                        'label' => __('my appointments today'),
                    ],
                    [
                        'value' => $myPendingCount,
                        'label' => __('to confirm'),
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

            $citasText = $pendingCount === 1 ? __('1 appointment') : __(':count appointments', ['count' => $pendingCount]);
            $pacientesText = $inProgressCount === 1 ? __('1 in clinic') : __(':count in clinic', ['count' => $inProgressCount]);
            $subtitle = __('There are :pending to confirm and :in_progress in clinic. Start with pending.', [
                'pending' => $citasText,
                'in_progress' => $pacientesText,
            ]);

            return [
                'greeting' => $greeting,
                'userName' => $userName,
                'subtitle' => $subtitle,
                'primaryButton' => [
                    'label' => '+ '.__('New Appointment'),
                    'url' => AppointmentResource::getUrl('create'),
                ],
                'secondaryButton' => [
                    'label' => __('Confirm pending'),
                    'url' => AppointmentResource::getUrl('index', ['activeTab' => 'pending']),
                ],
                'cards' => [
                    [
                        'value' => $inProgressCount,
                        'label' => __('in clinic now'),
                    ],
                    [
                        'value' => $pendingCount,
                        'label' => __('to confirm'),
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
            'subtitle' => __('Monitor appointments, patient requests and specialists in real time.'),
            'primaryButton' => [
                'label' => '+ '.__('New Appointment'),
                'url' => AppointmentResource::getUrl('create'),
            ],
            'secondaryButton' => [
                'label' => __('View schedule'),
                'url' => AppointmentResource::getUrl('index'),
            ],
            'cards' => [
                [
                    'value' => $todayTotal,
                    'label' => __('appointments today'),
                ],
                [
                    'value' => $pendingCount,
                    'label' => __('to confirm'),
                    'isHighlight' => true,
                ],
            ],
        ];
    }
}

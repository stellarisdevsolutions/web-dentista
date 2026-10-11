<?php

namespace App\Filament\Widgets;

use App\Services\DashboardSpecialistsService;
use App\Services\TodaySpecialistsSummaryData;
use Filament\Widgets\Widget;

class TodaySpecialistsWidget extends Widget
{
    protected static ?int $sort = 11;

    protected static bool $isLazy = false;

    protected ?string $pollingInterval = '60s';

    protected int|string|array $columnSpan = [
        'default' => 'full',
        'sm' => 'full',
        'md' => 'full',
        'lg' => 4,
        'xl' => 4,
        '2xl' => 4,
    ];

    public function getColumnSpan(): int|string|array
    {
        return [
            'default' => 'full',
            'sm' => 'full',
            'md' => 'full',
            'lg' => 4,
            'xl' => 4,
            '2xl' => 4,
        ];
    }

    protected string $view = 'filament.widgets.today-specialists-widget';

    public static function canView(): bool
    {
        $user = auth()->user();
        if (! $user) {
            return false;
        }

        // Solo admin y secretaria. Para el dentista el widget NO existe.
        return $user->isAdmin() || $user->isReceptionist();
    }

    /**
     * @return array{data: TodaySpecialistsSummaryData}
     */
    public function getViewData(): array
    {
        /** @var DashboardSpecialistsService $service */
        $service = app(DashboardSpecialistsService::class);

        return [
            'data' => $service->getSpecialistsSummary(auth()->user()),
        ];
    }
}

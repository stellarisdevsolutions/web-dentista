<?php

namespace App\Filament\Widgets;

use App\Services\DashboardKpiData;
use App\Services\DashboardKpiService;
use Filament\Widgets\Widget;

class KpiCardsWidget extends Widget
{
    protected static ?int $sort = 1;

    protected static bool $isLazy = false;

    protected static ?string $pollingInterval = '60s';

    protected int|string|array $columnSpan = 'full';

    protected string $view = 'filament.widgets.kpi-cards-widget';

    public function getColumnSpan(): int|string|array
    {
        return 'full';
    }

    /**
     * Pass computed KPI data to the view.
     *
     * @return array{kpi: DashboardKpiData}
     */
    public function getViewData(): array
    {
        /** @var DashboardKpiService $service */
        $service = app(DashboardKpiService::class);

        return [
            'kpi' => $service->getKpiData(auth()->user()),
        ];
    }
}

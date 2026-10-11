<?php

namespace App\Filament\Resources\Appointments\Pages;

use App\Filament\Resources\Appointments\AppointmentResource;
use Carbon\Carbon;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListAppointments extends ListRecords
{
    protected static string $resource = AppointmentResource::class;

    public function getTitle(): string
    {
        return 'Gestión de Citas';
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Nueva Cita'),
        ];
    }

    public function getTabs(): array
    {
        $baseQuery = static::getResource()::getEloquentQuery();

        return [
            'all' => Tab::make('Todas')
                ->badge((clone $baseQuery)->count()),

            'today' => Tab::make('Hoy')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereDate('appointment_date', Carbon::today()))
                ->badge((clone $baseQuery)->whereDate('appointment_date', Carbon::today())->count())
                ->badgeColor('primary'),

            'pending' => Tab::make('Por Confirmar')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'pending'))
                ->badge((clone $baseQuery)->where('status', 'pending')->count())
                ->badgeColor('warning'),

            'confirmed' => Tab::make('Confirmadas')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'confirmed'))
                ->badge((clone $baseQuery)->where('status', 'confirmed')->count())
                ->badgeColor('success'),

            'in_progress' => Tab::make('En Consulta')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'in_progress'))
                ->badge((clone $baseQuery)->where('status', 'in_progress')->count())
                ->badgeColor('info'),

            'completed' => Tab::make('Completadas')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'completed'))
                ->badge((clone $baseQuery)->where('status', 'completed')->count())
                ->badgeColor('gray'),

            'cancelled' => Tab::make('Canceladas')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereIn('status', ['cancelled', 'no_show']))
                ->badge((clone $baseQuery)->whereIn('status', ['cancelled', 'no_show'])->count())
                ->badgeColor('danger'),
        ];
    }

    public function getDefaultActiveTab(): string|int|null
    {
        return 'all';
    }
}

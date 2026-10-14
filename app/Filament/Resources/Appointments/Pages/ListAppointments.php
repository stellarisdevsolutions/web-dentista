<?php

namespace App\Filament\Resources\Appointments\Pages;

use App\Filament\Resources\Appointments\AppointmentResource;
use App\Models\Dentist;
use App\Models\User;
use Carbon\Carbon;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\EmbeddedTable;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Url;

class ListAppointments extends ListRecords
{
    protected static string $resource = AppointmentResource::class;

    #[Url(as: 'fecha')]
    public string $selectedDate = '';

    #[Url(as: 'especialista')]
    public ?int $dentistId = null;

    #[Url(as: 'estado')]
    public ?string $activeTab = 'all';

    public function mount(): void
    {
        parent::mount();

        if (empty($this->selectedDate)) {
            $this->selectedDate = Carbon::today()->toDateString();
        }

        if (empty($this->activeTab)) {
            $this->activeTab = 'all';
        }
    }

    public function updatedSelectedDate(): void
    {
        $this->resetPage();
        $this->resetTable();
    }

    public function updatedDentistId(): void
    {
        $this->resetPage();
        $this->resetTable();
    }

    public function previousDay(): void
    {
        $this->selectedDate = Carbon::parse($this->selectedDate ?: Carbon::today())->subDay()->toDateString();
        $this->resetPage();
        $this->resetTable();
    }

    public function nextDay(): void
    {
        $this->selectedDate = Carbon::parse($this->selectedDate ?: Carbon::today())->addDay()->toDateString();
        $this->resetPage();
        $this->resetTable();
    }

    public function goToToday(): void
    {
        $this->selectedDate = Carbon::today()->toDateString();
        $this->resetPage();
        $this->resetTable();
    }

    public function clearSearch(): void
    {
        $this->resetTableSearch();
    }

    public function selectTab(string $tab): void
    {
        $this->activeTab = $tab;
        $this->resetPage();
        $this->resetTable();
    }

    public function getTitle(): string
    {
        /** @var User|null $user */
        $user = auth()->user();
        if ($user && $user->isDentist()) {
            return 'Mis citas';
        }

        return 'Agenda';
    }

    public function getSubheading(): ?string
    {
        $dateCarbon = Carbon::parse($this->selectedDate ?: Carbon::today());
        $dateText = str_replace('.', '', $dateCarbon->locale('es')->isoFormat('dddd D [de] MMMM'));

        $counts = $this->getTabCounts();
        $count = $counts['all'] ?? 0;

        $search = trim((string) $this->getTableSearch());
        if (mb_strlen($search) >= 2) {
            return "{$count} ".($count === 1 ? 'cita encontrada' : 'citas encontradas').' en la búsqueda';
        }

        return "{$count} ".($count === 1 ? 'cita' : 'citas')." el {$dateText}";
    }

    public function getFormattedDateLabel(): string
    {
        $dateCarbon = Carbon::parse($this->selectedDate ?: Carbon::today());
        $isToday = $dateCarbon->isToday();
        $formatted = str_replace('.', '', $dateCarbon->locale('es')->isoFormat('ddd D MMM'));

        return $isToday ? "Hoy · {$formatted}" : $formatted;
    }

    /**
     * @return Collection<int, Dentist>
     */
    public function getDentists(): Collection
    {
        return Dentist::query()->where('is_active', true)->orderBy('name')->get();
    }

    /**
     * @return array<string, int>
     */
    public function getTabCounts(): array
    {
        $baseQuery = static::getResource()::getEloquentQuery();

        if ($this->dentistId && ! auth()->user()?->isDentist()) {
            $baseQuery->where('dentist_id', $this->dentistId);
        }

        $search = trim((string) $this->getTableSearch());
        if (mb_strlen($search) >= 2) {
            $cleanedPhone = preg_replace('/[\s\-+()]/', '', $search);
            $baseQuery->where(function (Builder $q) use ($search, $cleanedPhone): void {
                $q->where('patient_name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
                if (! empty($cleanedPhone)) {
                    $q->orWhereRaw("REPLACE(REPLACE(REPLACE(REPLACE(patient_phone, ' ', ''), '-', ''), '+', ''), '(', '') LIKE ?", ["%{$cleanedPhone}%"]);
                }
            });
        } else {
            $baseQuery->whereDate('appointment_date', $this->selectedDate ?: Carbon::today()->toDateString());
        }

        return [
            'all' => (clone $baseQuery)->count(),
            'pending' => (clone $baseQuery)->where('status', 'pending')->count(),
            'confirmed' => (clone $baseQuery)->whereIn('status', ['confirmed', 'in_progress'])->count(),
            'cancelled' => (clone $baseQuery)->whereIn('status', ['cancelled', 'no_show'])->count(),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('+ Nueva cita')
                ->extraAttributes([
                    'class' => 'agenda-create-action-btn',
                    'style' => 'border-radius: 9999px !important; background-color: #0EA5E9 !important; color: #0F172A !important; font-weight: 800 !important; min-height: 48px !important; padding: 0 24px !important; box-shadow: 0 10px 24px rgba(14, 165, 233, 0.40) !important; border: none !important; font-size: 14px !important;',
                ])
                ->visible(fn (): bool => ! auth()->user()?->isDentist()),
        ];
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                EmbeddedTable::make(),
            ]);
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Todas'),
            'pending' => Tab::make('Pendientes')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'pending')),
            'confirmed' => Tab::make('Confirmadas')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereIn('status', ['confirmed', 'in_progress'])),
            'cancelled' => Tab::make('Canceladas')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereIn('status', ['cancelled', 'no_show'])),
        ];
    }

    public function getDefaultActiveTab(): string|int|null
    {
        return 'all';
    }
}

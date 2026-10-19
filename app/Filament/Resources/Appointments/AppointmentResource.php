<?php

namespace App\Filament\Resources\Appointments;

use App\Enums\AppointmentStatus;
use App\Filament\Resources\Appointments\Pages\CreateAppointment;
use App\Filament\Resources\Appointments\Pages\EditAppointment;
use App\Filament\Resources\Appointments\Pages\ListAppointments;
use App\Filament\Resources\Appointments\Schemas\AppointmentForm;
use App\Filament\Resources\Appointments\Tables\AppointmentsTable;
use App\Models\Appointment;
use App\Models\User;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class AppointmentResource extends Resource
{
    protected static ?string $model = Appointment::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'code';

    public static function getGloballySearchableAttributes(): array
    {
        return ['code', 'patient_name', 'patient_email', 'patient_phone', 'symptoms'];
    }

    public static function getGlobalSearchResultTitle(Model $record): string
    {
        /** @var Appointment $record */
        return "{$record->code} - {$record->patient_name}";
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        /** @var Appointment $record */
        $isSpanish = app()->getLocale() === 'es';

        $statusLabel = $record->status instanceof AppointmentStatus
            ? $record->status->label()
            : (AppointmentStatus::tryFrom((string) $record->status)?->label() ?? (string) $record->status);

        return [
            $isSpanish ? 'Fecha' : 'Date' => "{$record->appointment_date->format('d/m/Y')} {$record->formatted_time}",
            $isSpanish ? 'Estado' : 'Status' => $statusLabel,
            $isSpanish ? 'Tratamiento' : 'Service' => $record->service?->name ?? '-',
        ];
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Clinical Management');
    }

    public static function getNavigationLabel(): string
    {
        return 'Agenda';
    }

    public static function getModelLabel(): string
    {
        return 'Cita';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Citas';
    }

    public static function form(Schema $schema): Schema
    {
        return AppointmentForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AppointmentsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->with(['service', 'dentist']);
        /** @var User|null $user */
        $user = auth()->user();

        if ($user && $user->isDentist() && $user->dentist_id) {
            $query->where('dentist_id', $user->dentist_id);
        }

        return $query;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAppointments::route('/'),
            'create' => CreateAppointment::route('/create'),
            'edit' => EditAppointment::route('/{record}/edit'),
        ];
    }
}

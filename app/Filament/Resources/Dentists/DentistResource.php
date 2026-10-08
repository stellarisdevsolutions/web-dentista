<?php

namespace App\Filament\Resources\Dentists;

use App\Filament\Resources\Dentists\Pages\CreateDentist;
use App\Filament\Resources\Dentists\Pages\EditDentist;
use App\Filament\Resources\Dentists\Pages\ListDentists;
use App\Filament\Resources\Dentists\Schemas\DentistForm;
use App\Filament\Resources\Dentists\Tables\DentistsTable;
use App\Models\Dentist;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class DentistResource extends Resource
{
    protected static ?string $model = Dentist::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'specialty', 'email', 'phone'];
    }

    public static function getGlobalSearchResultTitle(Model $record): string
    {
        /** @var Dentist $record */
        return $record->name;
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        /** @var Dentist $record */
        $isSpanish = app()->getLocale() === 'es';

        return [
            $isSpanish ? 'Especialidad' : 'Specialty' => $record->specialty,
            $isSpanish ? 'Teléfono' : 'Phone' => $record->phone ?? '-',
        ];
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Clinical Management');
    }

    public static function getNavigationLabel(): string
    {
        return __('Dentists');
    }

    public static function getModelLabel(): string
    {
        return __('Dentist');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Dentists');
    }

    public static function form(Schema $schema): Schema
    {
        return DentistForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DentistsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDentists::route('/'),
            'create' => CreateDentist::route('/create'),
            'edit' => EditDentist::route('/{record}/edit'),
        ];
    }
}

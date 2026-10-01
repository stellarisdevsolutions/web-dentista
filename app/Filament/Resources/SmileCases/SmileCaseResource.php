<?php

namespace App\Filament\Resources\SmileCases;

use App\Filament\Resources\SmileCases\Pages\CreateSmileCase;
use App\Filament\Resources\SmileCases\Pages\EditSmileCase;
use App\Filament\Resources\SmileCases\Pages\ListSmileCases;
use App\Filament\Resources\SmileCases\Schemas\SmileCaseForm;
use App\Filament\Resources\SmileCases\Tables\SmileCasesTable;
use App\Models\SmileCase;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class SmileCaseResource extends Resource
{
    protected static ?string $model = SmileCase::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCamera;

    protected static ?int $navigationSort = 1;

    public static function getRecordTitleAttribute(): ?string
    {
        return app()->getLocale() === 'es' ? 'title_es' : 'title_en';
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['title_es', 'title_en', 'description_es', 'description_en'];
    }

    public static function getGlobalSearchResultTitle(Model $record): string
    {
        /** @var SmileCase $record */
        return $record->title;
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        /** @var SmileCase $record */
        $isSpanish = app()->getLocale() === 'es';

        return [
            $isSpanish ? 'Tratamiento' : 'Service' => $record->service?->name ?? '-',
            $isSpanish ? 'Especialista' : 'Dentist' => $record->dentist?->name ?? '-',
        ];
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Smile Gallery & Reviews');
    }

    public static function getNavigationLabel(): string
    {
        return __('Smile Cases');
    }

    public static function getModelLabel(): string
    {
        return __('Smile Case');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Smile Cases');
    }

    public static function form(Schema $schema): Schema
    {
        return SmileCaseForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SmileCasesTable::configure($table);
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
            'index' => ListSmileCases::route('/'),
            'create' => CreateSmileCase::route('/create'),
            'edit' => EditSmileCase::route('/{record}/edit'),
        ];
    }
}

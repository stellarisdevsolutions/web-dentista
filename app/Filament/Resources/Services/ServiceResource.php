<?php

namespace App\Filament\Resources\Services;

use App\Filament\Resources\Services\Pages\CreateService;
use App\Filament\Resources\Services\Pages\EditService;
use App\Filament\Resources\Services\Pages\ListServices;
use App\Filament\Resources\Services\Schemas\ServiceForm;
use App\Filament\Resources\Services\Tables\ServicesTable;
use App\Models\Service;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class ServiceResource extends Resource
{
    protected static ?string $model = Service::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSparkles;

    protected static ?int $navigationSort = 3;

    public static function getRecordTitleAttribute(): ?string
    {
        return app()->getLocale() === 'es' ? 'name_es' : 'name_en';
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name_es', 'name_en', 'category_es', 'category_en', 'short_description_es', 'short_description_en'];
    }

    public static function getGlobalSearchResultTitle(Model $record): string
    {
        /** @var Service $record */
        return $record->name;
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        /** @var Service $record */
        $isSpanish = app()->getLocale() === 'es';

        return [
            $isSpanish ? 'Categoría' : 'Category' => $record->category,
            $isSpanish ? 'Precio' : 'Price' => $record->formatted_price,
        ];
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Clinical Management');
    }

    public static function getNavigationLabel(): string
    {
        return __('Treatments & Services');
    }

    public static function getModelLabel(): string
    {
        return __('Treatment');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Treatments');
    }

    public static function form(Schema $schema): Schema
    {
        return ServiceForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ServicesTable::configure($table);
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
            'index' => ListServices::route('/'),
            'create' => CreateService::route('/create'),
            'edit' => EditService::route('/{record}/edit'),
        ];
    }
}

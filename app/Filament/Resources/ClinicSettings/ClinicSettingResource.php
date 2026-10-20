<?php

namespace App\Filament\Resources\ClinicSettings;

use App\Filament\Resources\ClinicSettings\Pages\ManageClinicSettings;
use App\Filament\Resources\ClinicSettings\Schemas\ClinicSettingForm;
use App\Models\ClinicSetting;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Model;

class ClinicSettingResource extends Resource
{
    protected static ?string $model = ClinicSetting::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'clinic_name';

    public static function getGloballySearchableAttributes(): array
    {
        return ['clinic_name', 'phone', 'whatsapp', 'email', 'address'];
    }

    public static function getGlobalSearchResultTitle(Model $record): string
    {
        /** @var ClinicSetting $record */
        return $record->clinic_name;
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        /** @var ClinicSetting $record */
        return [
            'Teléfono' => $record->phone,
            'WhatsApp' => $record->whatsapp,
        ];
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Ajustes';
    }

    public static function getNavigationLabel(): string
    {
        return 'Perfil y Ajustes de la Clínica';
    }

    public static function getModelLabel(): string
    {
        return 'Ajuste de la Clínica';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Perfil y Ajustes de la Clínica';
    }

    public static function form(Schema $schema): Schema
    {
        return ClinicSettingForm::configure($schema);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageClinicSettings::route('/'),
        ];
    }
}

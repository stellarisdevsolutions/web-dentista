<?php

namespace App\Filament\Resources\ClinicSettings\Pages;

use App\Filament\Resources\ClinicSettings\ClinicSettingResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageClinicSettings extends ManageRecords
{
    protected static string $resource = ClinicSettingResource::class;

    public function getTitle(): string
    {
        return 'Perfil y Ajustes de la Clínica';
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Nuevo Ajuste'),
        ];
    }
}

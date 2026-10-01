<?php

namespace App\Filament\Resources\SmileCases\Pages;

use App\Filament\Resources\SmileCases\SmileCaseResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSmileCase extends EditRecord
{
    protected static string $resource = SmileCaseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

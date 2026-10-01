<?php

namespace App\Filament\Resources\SmileCases\Pages;

use App\Filament\Resources\SmileCases\SmileCaseResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSmileCases extends ListRecords
{
    protected static string $resource = SmileCaseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

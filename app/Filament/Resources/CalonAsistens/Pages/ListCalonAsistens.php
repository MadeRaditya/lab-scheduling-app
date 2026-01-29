<?php

namespace App\Filament\Resources\CalonAsistens\Pages;

use App\Filament\Resources\CalonAsistens\CalonAsistenResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCalonAsistens extends ListRecords
{
    protected static string $resource = CalonAsistenResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

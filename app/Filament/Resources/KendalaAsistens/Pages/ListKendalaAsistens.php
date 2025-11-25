<?php

namespace App\Filament\Resources\KendalaAsistens\Pages;

use App\Filament\Resources\KendalaAsistens\KendalaAsistenResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListKendalaAsistens extends ListRecords
{
    protected static string $resource = KendalaAsistenResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

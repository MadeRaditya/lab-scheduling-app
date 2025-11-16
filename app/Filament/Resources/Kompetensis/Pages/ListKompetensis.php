<?php

namespace App\Filament\Resources\Kompetensis\Pages;

use App\Filament\Resources\Kompetensis\KompetensiResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListKompetensis extends ListRecords
{
    protected static string $resource = KompetensiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

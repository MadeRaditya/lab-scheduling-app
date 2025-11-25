<?php

namespace App\Filament\Resources\JadwalFinals\Pages;

use App\Filament\Resources\JadwalFinals\JadwalFinalResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListJadwalFinals extends ListRecords
{
    protected static string $resource = JadwalFinalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

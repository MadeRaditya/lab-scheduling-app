<?php

namespace App\Filament\Resources\JadwalAsistens\Pages;

use App\Filament\Resources\JadwalAsistens\JadwalAsistenResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListJadwalAsistens extends ListRecords
{
    protected static string $resource = JadwalAsistenResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

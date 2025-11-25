<?php

namespace App\Filament\Resources\PreferensiJadwalAsistens\Pages;

use App\Filament\Resources\PreferensiJadwalAsistens\PreferensiJadwalAsistenResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPreferensiJadwalAsistens extends ListRecords
{
    protected static string $resource = PreferensiJadwalAsistenResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

<?php

namespace App\Filament\Resources\PreferensiJadwalAsistens\Pages;

use App\Filament\Resources\PreferensiJadwalAsistens\PreferensiJadwalAsistenResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPreferensiJadwalAsisten extends EditRecord
{
    protected static string $resource = PreferensiJadwalAsistenResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

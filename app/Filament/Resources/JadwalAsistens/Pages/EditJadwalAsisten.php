<?php

namespace App\Filament\Resources\JadwalAsistens\Pages;

use App\Filament\Resources\JadwalAsistens\JadwalAsistenResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditJadwalAsisten extends EditRecord
{
    protected static string $resource = JadwalAsistenResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

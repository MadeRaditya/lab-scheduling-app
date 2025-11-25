<?php

namespace App\Filament\Resources\JadwalFinals\Pages;

use App\Filament\Resources\JadwalFinals\JadwalFinalResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditJadwalFinal extends EditRecord
{
    protected static string $resource = JadwalFinalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

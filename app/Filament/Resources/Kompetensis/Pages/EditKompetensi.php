<?php

namespace App\Filament\Resources\Kompetensis\Pages;

use App\Filament\Resources\Kompetensis\KompetensiResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditKompetensi extends EditRecord
{
    protected static string $resource = KompetensiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

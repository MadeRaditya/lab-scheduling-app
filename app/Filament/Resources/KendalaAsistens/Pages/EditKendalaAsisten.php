<?php

namespace App\Filament\Resources\KendalaAsistens\Pages;

use App\Filament\Resources\KendalaAsistens\KendalaAsistenResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditKendalaAsisten extends EditRecord
{
    protected static string $resource = KendalaAsistenResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

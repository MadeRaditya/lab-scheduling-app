<?php

namespace App\Filament\Resources\CalonAsistens\Pages;

use App\Filament\Resources\CalonAsistens\CalonAsistenResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCalonAsisten extends EditRecord
{
    protected static string $resource = CalonAsistenResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

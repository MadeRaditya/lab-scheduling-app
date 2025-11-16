<?php

namespace App\Filament\Resources\Asistens\Pages;

use App\Filament\Resources\Asistens\AsistenResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAsisten extends EditRecord
{
    protected static string $resource = AsistenResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

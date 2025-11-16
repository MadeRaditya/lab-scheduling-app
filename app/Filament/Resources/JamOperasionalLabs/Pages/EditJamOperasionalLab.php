<?php

namespace App\Filament\Resources\JamOperasionalLabs\Pages;

use App\Filament\Resources\JamOperasionalLabs\JamOperasionalLabResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditJamOperasionalLab extends EditRecord
{
    protected static string $resource = JamOperasionalLabResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

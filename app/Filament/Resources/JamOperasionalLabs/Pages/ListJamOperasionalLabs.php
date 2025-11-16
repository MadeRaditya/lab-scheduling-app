<?php

namespace App\Filament\Resources\JamOperasionalLabs\Pages;

use App\Filament\Resources\JamOperasionalLabs\JamOperasionalLabResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListJamOperasionalLabs extends ListRecords
{
    protected static string $resource = JamOperasionalLabResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

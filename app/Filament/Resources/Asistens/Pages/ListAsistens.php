<?php

namespace App\Filament\Resources\Asistens\Pages;

use App\Filament\Resources\Asistens\AsistenResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAsistens extends ListRecords
{
    protected static string $resource = AsistenResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

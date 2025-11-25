<?php

namespace App\Filament\Resources\PenugasanAsistenFinals\Pages;

use App\Filament\Resources\PenugasanAsistenFinals\PenugasanAsistenFinalResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPenugasanAsistenFinals extends ListRecords
{
    protected static string $resource = PenugasanAsistenFinalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

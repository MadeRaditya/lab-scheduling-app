<?php

namespace App\Filament\Resources\AsistenSemesters\Pages;

use App\Filament\Resources\AsistenSemesters\AsistenSemesterResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAsistenSemesters extends ListRecords
{
    protected static string $resource = AsistenSemesterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

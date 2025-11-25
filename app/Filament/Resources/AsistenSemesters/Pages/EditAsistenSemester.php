<?php

namespace App\Filament\Resources\AsistenSemesters\Pages;

use App\Filament\Resources\AsistenSemesters\AsistenSemesterResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAsistenSemester extends EditRecord
{
    protected static string $resource = AsistenSemesterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

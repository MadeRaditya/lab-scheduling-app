<?php

namespace App\Filament\Resources\PenugasanAsistenFinals\Pages;

use App\Filament\Resources\PenugasanAsistenFinals\PenugasanAsistenFinalResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPenugasanAsistenFinal extends EditRecord
{
    protected static string $resource = PenugasanAsistenFinalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

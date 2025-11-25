<?php

namespace App\Filament\Resources\Matakuliahs\Pages;

use App\Filament\Resources\Matakuliahs\MatakuliahResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditMatakuliah extends EditRecord
{
    protected static string $resource = MatakuliahResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

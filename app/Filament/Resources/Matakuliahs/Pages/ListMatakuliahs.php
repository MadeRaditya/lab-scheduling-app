<?php

namespace App\Filament\Resources\Matakuliahs\Pages;

use App\Filament\Resources\Matakuliahs\MatakuliahResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMatakuliahs extends ListRecords
{
    protected static string $resource = MatakuliahResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            Action::make('upload')
                ->label('Upload CSV Matakuliah')
                ->color('success')
                ->url(MatakuliahResource::getUrl('upload'))
        ];
    }
}

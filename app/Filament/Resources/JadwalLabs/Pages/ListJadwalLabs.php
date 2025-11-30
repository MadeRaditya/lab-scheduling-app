<?php

namespace App\Filament\Resources\JadwalLabs\Pages;

use App\Filament\Resources\JadwalLabs\JadwalLabResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListJadwalLabs extends ListRecords
{
    protected static string $resource = JadwalLabResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            Action::make('upload')
                ->label('Upload Jadwal Lab')
                ->icon('heroicon-o-arrow-up-tray')
                ->url(JadwalLabResource::getUrl('upload'))
        ];
    }
}

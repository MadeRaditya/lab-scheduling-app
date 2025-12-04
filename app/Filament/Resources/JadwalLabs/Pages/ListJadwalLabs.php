<?php

namespace App\Filament\Resources\JadwalLabs\Pages;

use App\Filament\Resources\JadwalLabs\JadwalLabResource;
use App\Models\Semester;
use App\Services\GreedyScheduler;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use App\Enums\SemesterStatus;

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
                ->url(JadwalLabResource::getUrl('upload')),
            
            Action::make('generateJadwal')
                ->label('Generate Jadwal Final')
                ->icon('heroicon-o-cpu-chip')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Generate Jadwal Final')
                ->modalDescription('Proses ini akan MENGHAPUS jadwal final yang sudah ada pada semester yang dipilih dan membuat ulang berdasarkan algoritma greedy.')
                ->form([
                    Select::make('semester_id')
                        ->label('Pilih Semester')
                        ->options(Semester::where('status', SemesterStatus::Aktif)->pluck('nama_semester', 'id_semester'))
                        ->required()
                        ->default(fn() => Semester::where('status', SemesterStatus::Aktif)->value('id_semester'))
                ])
                ->action(function (array $data) {
                    $semesterId = $data['semester_id'];
                    
                    $service = new GreedyScheduler();
                    $result = $service->generate((int)$semesterId);

                    if ($result['status'] === 'success') {
                        Notification::make()
                            ->success()
                            ->title('Berhasil')
                            ->body($result['message'])
                            ->send();
                    } else {
                        Notification::make()
                            ->danger()
                            ->title('Gagal')
                            ->body($result['message'])
                            ->send();
                    }
                }),
        ];
    }
}
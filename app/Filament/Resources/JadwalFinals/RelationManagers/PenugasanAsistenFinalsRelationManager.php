<?php

namespace App\Filament\Resources\JadwalFinals\RelationManagers;

use App\Enums\PenugasanPeran;
use App\Models\Asisten;
use App\Models\JadwalFinal;
use App\Models\Kompetensi;
use App\Models\PenugasanAsistenFinal;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Utilities\Set as UtilitiesSet;

class PenugasanAsistenFinalsRelationManager extends RelationManager
{
    protected static string $relationship = 'penugasanAsistenFinals';

    protected static ?string $title = 'Asisten Bertugas';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('id_asisten')
                    ->label('Pilih Asisten')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->options(function (RelationManager $livewire, ?PenugasanAsistenFinal $record) {
                        /** @var JadwalFinal $jadwal */
                        $jadwal = $livewire->getOwnerRecord();

                        $busyAsistenIds = PenugasanAsistenFinal::whereHas('jadwalFinal', function ($q) use ($jadwal) {
                            $q->where('hari', $jadwal->hari)
                                ->where('id_final', '!=', $jadwal->id_final)
                                ->where(function ($qTime) use ($jadwal) {
                                    $qTime->where('jam_mulai', '<', $jadwal->jam_selesai)
                                        ->where('jam_selesai', '>', $jadwal->jam_mulai);
                                });
                        })->pluck('id_asisten')->toArray();

                        return Asisten::query()
                            ->where('status_aktif', true)
                            ->whereNotIn('id_asisten', $busyAsistenIds)
                            ->with(['matakuliahs', 'kompetensis', 'preferensiJadwalAsistens'])
                            ->get()
                            ->mapWithKeys(function ($asisten) use ($jadwal) {
                                $score = $this->calculateScore($asisten, $jadwal);

                                return [
                                    $asisten->id_asisten => "{$asisten->nama} (Skor: {$score})"
                                ];
                            })
                            ->sortByDesc(fn($label) => (int) filter_var($label, FILTER_SANITIZE_NUMBER_INT));
                    })
                    ->live()
                    ->afterStateUpdated(function ($state, UtilitiesSet $set, RelationManager $livewire) {
                        if (!$state) return;

                        $jadwal = $livewire->getOwnerRecord();
                        $asisten = Asisten::find($state);

                        if ($asisten && $jadwal) {
                            $score = $this->calculateScore($asisten, $jadwal);
                            $set('skor_kecocokan', $score);
                        }
                    })
                    ->rules([
                        function (RelationManager $livewire) {
                            return function (string $attribute, $value, \Closure $fail) use ($livewire) {
                                $jadwal = $livewire->getOwnerRecord();

                                $isBusy = PenugasanAsistenFinal::where('id_asisten', $value)
                                    ->whereHas('jadwalFinal', function ($q) use ($jadwal) {
                                        $q->where('hari', $jadwal->hari)
                                            ->where('id_final', '!=', $jadwal->id_final)
                                            ->where('jam_mulai', '<', $jadwal->jam_selesai)
                                            ->where('jam_selesai', '>', $jadwal->jam_mulai);
                                    })->exists();

                                if ($isBusy) {
                                    $fail('Asisten ini sudah memiliki jadwal lain di jam yang sama (Bentrok).');
                                }
                            };
                        },
                    ]),

                TextInput::make('skor_kecocokan')
                    ->label('Skor (Auto)')
                    ->required()
                    ->numeric()
                    ->readOnly()
                    ->default(0),

                Select::make('peran')
                    ->options(PenugasanPeran::class)
                    ->required()
                    ->default(PenugasanPeran::Utama),

                Select::make('status_kehadiran')
                    ->options(['Terjadwal' => 'Terjadwal', 'Hadir' => 'Hadir', 'Tidak Hadir' => 'Tidak Hadir', 'Izin' => 'Izin'])
                    ->required()
                    ->default('Terjadwal'),

                Textarea::make('catatan')
                    ->default(null)
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id_jadwal_final')
            ->columns([
                TextColumn::make('asisten.nama')
                    ->label('Nama Asisten')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('asisten.kompetensis.nama_kompetensi')
                    ->label('Skill')
                    ->badge()
                    ->limitList(2),

                TextColumn::make('skor_kecocokan')
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color(fn($state) => $state >= 80 ? 'success' : ($state >= 60 ? 'warning' : 'danger')),

                TextColumn::make('peran')
                    ->badge(),

                TextColumn::make('status_kehadiran')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Hadir' => 'success',
                        'Izin' => 'warning',
                        'Alpha', 'Tidak Hadir' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Tambah Asisten Manual')
                    ->modalWidth('lg')
                    ->after(function (RelationManager $livewire) {
                        $jadwal = $livewire->getOwnerRecord();
                        $jadwal->update([
                            'status' => 'Aktif',
                            'catatan' => 'Asisten ditambahkan secara manual.'
                        ]);
                    }),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()
                    ->after(function (RelationManager $livewire) {
                        $jadwal = $livewire->getOwnerRecord();
                        $jadwal->refresh();

                        if ($jadwal->penugasanAsistenFinals()->count() == 0) {
                            $jadwal->update([
                                'status' => 'Dibatalkan',
                                'catatan' => 'Asisten dihapus manual (Kosong).'
                            ]);
                        }
                    }),
            ])
            ->toolbarActions([
                DeleteBulkAction::make(),
            ]);
    }

    private function calculateScore($asisten, $jadwal)
    {
        $score = 50;

        $isStrict = $asisten->matakuliahs->contains('id', $jadwal->matakuliah_id);
        $isSoft = $asisten->matakuliahs->contains('nama_mk', $jadwal->matakuliah->nama_mk);

        $requiredSkillIds = $jadwal->matakuliah->kompetensis->pluck('id_kompetensi')->toArray();
        $isSkillMatched = $asisten->kompetensis->contains(fn($k) => in_array($k->id_kompetensi, $requiredSkillIds));

        if ($isStrict) $score += 20;
        elseif ($isSoft) $score += 15;
        elseif ($isSkillMatched) $score += 5;
        else $score -= 10;

        $pref = $asisten->preferensiJadwalAsistens
            ->where('hari', $jadwal->hari)
            ->filter(fn($p) => $p->jam_mulai < $jadwal->jam_selesai && $p->jam_selesai > $jadwal->jam_mulai)
            ->first();

        if ($pref) {
            $score += match ($pref->tingkat_preferensi) {
                'Sangat Suka' => 30,
                'Bisa' => 15,
                'Kurang Suka' => 5,
                default => 0,
            };
        }

        return $score;
    }
}

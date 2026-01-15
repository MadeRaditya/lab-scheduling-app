<?php

namespace App\Filament\Resources\Asistens\Pages;

use App\Filament\Resources\Asistens\AsistenResource;
use App\Models\Asisten;
use Filament\Actions\AttachAction; 
use Filament\Actions\DetachAction;
use Filament\Infolists\Components\ImageEntry as ComponentsImageEntry;
use Filament\Infolists\Components\TextEntry as ComponentsTextEntry;
use Filament\Schemas\Schema; 
use Filament\Schemas\Components\ImageEntry; 
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\TextEntry;
use Filament\Resources\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Schemas\Concerns\InteractsWithSchemas; 
use Filament\Schemas\Contracts\HasSchemas;        
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class KelolaKualifikasi extends Page implements HasSchemas, HasTable
{
    use InteractsWithSchemas;
    use InteractsWithTable;

    protected static string $resource = AsistenResource::class;

    public function getView(): string
    {
        return 'filament.resources.asistens.pages.kelola-kualifikasi';
    }

    protected static ?string $title = 'Verifikasi Kualifikasi Asisten';

    public Asisten $record;

    public function mount(Asisten $record)
    {
        $this->record = $record;
    }

    public function assistantInfolist(Schema $schema): Schema
    {
        return $schema
            ->record($this->record)
            ->components([ 
                Section::make('Kartu Identitas Asisten')
                    ->columns(4)
                    ->components([
                        ComponentsImageEntry::make('foto')
                            ->defaultImageUrl('https://ui-avatars.com/api/?name=' . urlencode($this->record->nama))
                            ->circular()
                            ->columnSpan(1),

                        Section::make()
                            ->columnSpan(3)
                            ->components([
                                ComponentsTextEntry::make('nama')
                                    ->size('lg') 
                                    ->weight('bold')
                                    ->label('Nama Lengkap'),
                                ComponentsTextEntry::make('npm') 
                                    ->label('NIM')
                                    ->copyable(),
                                ComponentsTextEntry::make('kompetensis.nama_kompetensi')
                                    ->label('Kompetensi Dasar')
                                    ->badge()
                                    ->color('success')
                                    ->listWithLineBreaks(),
                            ])
                    ])
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->relationship(fn() => $this->record->matakuliahs())
            ->heading('Daftar Mata Kuliah Verified')
            ->columns([
                TextColumn::make('kode_mk')->label('Kode')->searchable(),
                TextColumn::make('nama_mk')->label('Mata Kuliah')->searchable(),
                TextColumn::make('sks')->label('SKS'),
                TextColumn::make('semester.nama_semester')->label('Semester'),
            ])
            ->headerActions([
                AttachAction::make()
                    ->label('Tambah Kualifikasi')
                    ->recordTitleAttribute('nama_mk')

                    ->preloadRecordSelect()
                    ->recordSelectOptionsQuery(function (Builder $query) {
                        $asistenId = $this->record->id_asisten;
                        $kompetensiIds = DB::table('asisten_kompetensi')
                            ->where('id_asisten', $asistenId)
                            ->pluck('id_kompetensi');

                        return $query->whereHas('kompetensis', function ($q) use ($kompetensiIds) {
                            $q->whereIn('kompetensis.id_kompetensi', $kompetensiIds);
                        });
                    })
                    ->multiple(),
            ])
            ->actions([
                DetachAction::make()->label('Hapus'),
            ]);
    }
}

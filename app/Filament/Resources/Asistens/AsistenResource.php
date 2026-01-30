<?php

namespace App\Filament\Resources\Asistens;

use App\Filament\Resources\Asistens\Pages\CreateAsisten;
use App\Filament\Resources\Asistens\Pages\EditAsisten;
use App\Filament\Resources\Asistens\Pages\KelolaKualifikasi;
use App\Filament\Resources\Asistens\Pages\ListAsistens;
use App\Filament\Resources\Asistens\Pages\UploadAsisten;
use App\Filament\Resources\Asistens\RelationManagers\AsistenSemesterRelationManager;
use App\Filament\Resources\Asistens\RelationManagers\JadwalAsistenRelationManager;
use App\Filament\Resources\Asistens\RelationManagers\KendalaAsistensRelationManager;
use App\Filament\Resources\Asistens\RelationManagers\KompetensiRelationManager;
use App\Filament\Resources\Asistens\RelationManagers\MatakuliahsRelationManager;
use App\Filament\Resources\Asistens\RelationManagers\PreferensiJadwalAsistenRelationManager;
use App\Filament\Resources\Asistens\Schemas\AsistenForm;
use App\Filament\Resources\Asistens\Tables\AsistensTable;
use App\Models\Asisten;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class AsistenResource extends Resource
{
    protected static ?string $model = Asisten::class;

    protected static string | UnitEnum | null $navigationGroup  = 'Manajemen Asisten';
    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'nama';

    public static function form(Schema $schema): Schema
    {
        return AsistenForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AsistensTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            KompetensiRelationManager::class,
            JadwalAsistenRelationManager::class,
            KendalaAsistensRelationManager::class,
            PreferensiJadwalAsistenRelationManager::class,
            AsistenSemesterRelationManager::class,
            MatakuliahsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAsistens::route('/'),
            'create' => CreateAsisten::route('/create'),
            'edit' => EditAsisten::route('/{record}/edit'),
            'kualifikasi' => KelolaKualifikasi::route('/{record}/kualifikasi'),
            'upload' => UploadAsisten::route('/upload'),
        ];
    }
}

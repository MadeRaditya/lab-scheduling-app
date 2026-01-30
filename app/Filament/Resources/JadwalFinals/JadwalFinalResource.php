<?php

namespace App\Filament\Resources\JadwalFinals;

use App\Filament\Resources\JadwalFinals\Pages\CreateJadwalFinal;
use App\Filament\Resources\JadwalFinals\Pages\EditJadwalFinal;
use App\Filament\Resources\JadwalFinals\Pages\ListJadwalFinals;
use App\Filament\Resources\JadwalFinals\RelationManagers\PenugasanAsistenFinalsRelationManager;
use App\Filament\Resources\JadwalFinals\Schemas\JadwalFinalForm;
use App\Filament\Resources\JadwalFinals\Tables\JadwalFinalsTable;
use App\Models\JadwalFinal;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class JadwalFinalResource extends Resource
{
    protected static ?string $model = JadwalFinal::class;

    protected static string | UnitEnum | null $navigationGroup  = 'Penjadwalan';
    protected static ?int $navigationSort = 1;


    protected static ?string $recordTitleAttribute = 'hari';

    public static function getGloballySearchableAttributes(): array
    {
        return [
            'hari', 
            'matakuliah.nama_mk', 
            'matakuliah.dosen',   
            'ruangan.nama_ruangan',
        ];
    }

    public static function getGlobalSearchResultTitle(Model $record): string
    {
        return $record->matakuliah->nama_mk . ' - ' . $record->hari . ' (' . $record->jam_mulai . ')';
    }

    public static function form(Schema $schema): Schema
    {
        return JadwalFinalForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return JadwalFinalsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            PenugasanAsistenFinalsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListJadwalFinals::route('/'),
            'create' => CreateJadwalFinal::route('/create'),
            'edit' => EditJadwalFinal::route('/{record}/edit'),
        ];
    }
}

<?php

namespace App\Filament\Resources\PreferensiJadwalAsistens;

use App\Filament\Resources\PreferensiJadwalAsistens\Pages\CreatePreferensiJadwalAsisten;
use App\Filament\Resources\PreferensiJadwalAsistens\Pages\EditPreferensiJadwalAsisten;
use App\Filament\Resources\PreferensiJadwalAsistens\Pages\ListPreferensiJadwalAsistens;
use App\Filament\Resources\PreferensiJadwalAsistens\Schemas\PreferensiJadwalAsistenForm;
use App\Filament\Resources\PreferensiJadwalAsistens\Tables\PreferensiJadwalAsistensTable;
use App\Models\PreferensiJadwalAsisten;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class PreferensiJadwalAsistenResource extends Resource
{
    protected static ?string $model = PreferensiJadwalAsisten::class;

    protected static string|UnitEnum|null $navigationGroup = "Manajemen Asisten";
    protected static ?int $navigationSort = 5;

    protected static ?string $recordTitleAttribute = "hari";
    
    public static function getGloballySearchableAttributes(): array
    {
        return ['asisten.nama'];
    }

    public static function getGlobalSearchResultTitle(Model $record): string
    {
        return "Preferensi: {$record->asisten->nama} - {$record->hari} ({$record->jam_mulai})";
    }

    public static function form(Schema $schema): Schema
    {
        return PreferensiJadwalAsistenForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PreferensiJadwalAsistensTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
                //
            ];
    }

    public static function getPages(): array
    {
        return [
            "index" => ListPreferensiJadwalAsistens::route("/"),
            "create" => CreatePreferensiJadwalAsisten::route("/create"),
            "edit" => EditPreferensiJadwalAsisten::route("/{record}/edit"),
        ];
    }
}

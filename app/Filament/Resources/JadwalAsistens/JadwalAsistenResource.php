<?php

namespace App\Filament\Resources\JadwalAsistens;

use App\Filament\Resources\JadwalAsistens\Pages\CreateJadwalAsisten;
use App\Filament\Resources\JadwalAsistens\Pages\EditJadwalAsisten;
use App\Filament\Resources\JadwalAsistens\Pages\ListJadwalAsistens;
use App\Filament\Resources\JadwalAsistens\Schemas\JadwalAsistenForm;
use App\Filament\Resources\JadwalAsistens\Tables\JadwalAsistensTable;
use App\Models\JadwalAsisten;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class JadwalAsistenResource extends Resource
{
    protected static ?string $model = JadwalAsisten::class;

    protected static string|UnitEnum|null $navigationGroup = "Manajemen Asisten";
    protected static ?int $navigationSort = 3;

    protected static ?string $navigationLabel = "Jadwal KRS Asisten";

    protected static ?string $recordTitleAttribute = "hari";

    public static function getGloballySearchableAttributes(): array
    {
        return [
            'asisten.nama',
            'asisten.npm',
        ];
    }

    public static function getGlobalSearchResultTitle(Model $record): string
    {
        return "KRS {$record->asisten->nama} - ({$record->hari})";
    }

    public static function form(Schema $schema): Schema
    {
        return JadwalAsistenForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return JadwalAsistensTable::configure($table);
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
            "index" => ListJadwalAsistens::route("/"),
            "create" => CreateJadwalAsisten::route("/create"),
            "edit" => EditJadwalAsisten::route("/{record}/edit"),
        ];
    }
}

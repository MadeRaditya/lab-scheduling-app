<?php

namespace App\Filament\Resources\KendalaAsistens;

use App\Filament\Resources\KendalaAsistens\Pages\CreateKendalaAsisten;
use App\Filament\Resources\KendalaAsistens\Pages\EditKendalaAsisten;
use App\Filament\Resources\KendalaAsistens\Pages\ListKendalaAsistens;
use App\Filament\Resources\KendalaAsistens\Schemas\KendalaAsistenForm;
use App\Filament\Resources\KendalaAsistens\Tables\KendalaAsistensTable;
use App\Models\KendalaAsisten;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class KendalaAsistenResource extends Resource
{
    protected static ?string $model = KendalaAsisten::class;

    protected static string|UnitEnum|null $navigationGroup = "Manajemen Asisten";
    protected static ?int $navigationSort = 6;

    protected static ?string $recordTitleAttribute = "Kendala Asisten";

    public static function form(Schema $schema): Schema
    {
        return KendalaAsistenForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return KendalaAsistensTable::configure($table);
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
            "index" => ListKendalaAsistens::route("/"),
            "create" => CreateKendalaAsisten::route("/create"),
            "edit" => EditKendalaAsisten::route("/{record}/edit"),
        ];
    }
}

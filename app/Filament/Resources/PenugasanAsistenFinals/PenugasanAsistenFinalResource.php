<?php

namespace App\Filament\Resources\PenugasanAsistenFinals;

use App\Filament\Resources\PenugasanAsistenFinals\Pages\CreatePenugasanAsistenFinal;
use App\Filament\Resources\PenugasanAsistenFinals\Pages\EditPenugasanAsistenFinal;
use App\Filament\Resources\PenugasanAsistenFinals\Pages\ListPenugasanAsistenFinals;
use App\Filament\Resources\PenugasanAsistenFinals\Schemas\PenugasanAsistenFinalForm;
use App\Filament\Resources\PenugasanAsistenFinals\Tables\PenugasanAsistenFinalsTable;
use App\Models\PenugasanAsistenFinal;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class PenugasanAsistenFinalResource extends Resource
{
    protected static ?string $model = PenugasanAsistenFinal::class;

    protected static string|UnitEnum|null $navigationGroup = "Manajemen Asisten";
    protected static ?int $navigationSort = 4;

    protected static ?string $recordTitleAttribute = "Penugasan Asisten";

    public static function form(Schema $schema): Schema
    {
        return PenugasanAsistenFinalForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PenugasanAsistenFinalsTable::configure($table);
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
            "index" => ListPenugasanAsistenFinals::route("/"),
            "create" => CreatePenugasanAsistenFinal::route("/create"),
            "edit" => EditPenugasanAsistenFinal::route("/{record}/edit"),
        ];
    }
}

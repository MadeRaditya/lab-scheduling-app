<?php

namespace App\Filament\Resources\CalonAsistens;

use App\Filament\Resources\CalonAsistens\Pages\CreateCalonAsisten;
use App\Filament\Resources\CalonAsistens\Pages\EditCalonAsisten;
use App\Filament\Resources\CalonAsistens\Pages\ListCalonAsistens;
use App\Filament\Resources\CalonAsistens\Schemas\CalonAsistenForm;
use App\Filament\Resources\CalonAsistens\Tables\CalonAsistensTable;
use App\Models\CalonAsisten;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class CalonAsistenResource extends Resource
{
    protected static ?string $model = CalonAsisten::class;

    protected static string|UnitEnum|null $navigationGroup = "Manajemen Asisten";
    protected static ?int $navigationSort = 7;

    protected static ?string $recordTitleAttribute = 'nama';

    public static function form(Schema $schema): Schema
    {
        return CalonAsistenForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CalonAsistensTable::configure($table);
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
            'index' => ListCalonAsistens::route('/'),
            'create' => CreateCalonAsisten::route('/create'),
            'edit' => EditCalonAsisten::route('/{record}/edit'),
        ];
    }
}

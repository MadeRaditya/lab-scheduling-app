<?php

namespace App\Filament\Resources\Kompetensis;

use App\Filament\Resources\Kompetensis\Pages\CreateKompetensi;
use App\Filament\Resources\Kompetensis\Pages\EditKompetensi;
use App\Filament\Resources\Kompetensis\Pages\ListKompetensis;
use App\Filament\Resources\Kompetensis\Schemas\KompetensiForm;
use App\Filament\Resources\Kompetensis\Tables\KompetensisTable;
use App\Models\Kompetensi;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class KompetensiResource extends Resource
{
    protected static ?string $model = Kompetensi::class;

    protected static string | UnitEnum | null $navigationGroup  = 'Data Master';
    protected static ?int $navigationSort = 4;

    protected static ?string $recordTitleAttribute = 'nama_kompetensi';

    public static function form(Schema $schema): Schema
    {
        return KompetensiForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return KompetensisTable::configure($table);
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
            'index' => ListKompetensis::route('/'),
            'create' => CreateKompetensi::route('/create'),
            'edit' => EditKompetensi::route('/{record}/edit'),
        ];
    }
}

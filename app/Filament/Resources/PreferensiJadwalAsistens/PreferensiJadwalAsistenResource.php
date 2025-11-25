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

class PreferensiJadwalAsistenResource extends Resource
{
    protected static ?string $model = PreferensiJadwalAsisten::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Heart;

    protected static ?string $recordTitleAttribute = 'PreferensiJadwalAistenResource';

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
            'index' => ListPreferensiJadwalAsistens::route('/'),
            'create' => CreatePreferensiJadwalAsisten::route('/create'),
            'edit' => EditPreferensiJadwalAsisten::route('/{record}/edit'),
        ];
    }
}

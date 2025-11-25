<?php

namespace App\Filament\Resources\JadwalFinals;

use App\Filament\Resources\JadwalFinals\Pages\CreateJadwalFinal;
use App\Filament\Resources\JadwalFinals\Pages\EditJadwalFinal;
use App\Filament\Resources\JadwalFinals\Pages\ListJadwalFinals;
use App\Filament\Resources\JadwalFinals\Schemas\JadwalFinalForm;
use App\Filament\Resources\JadwalFinals\Tables\JadwalFinalsTable;
use App\Models\JadwalFinal;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class JadwalFinalResource extends Resource
{
    protected static ?string $model = JadwalFinal::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::ClipboardDocumentCheck;

    protected static ?string $recordTitleAttribute = 'Jadwal Final';

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
            //
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

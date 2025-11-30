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

class JadwalAsistenResource extends Resource
{
    protected static ?string $model = JadwalAsisten::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::CalendarDays;
    protected static ?string $navigationLabel = 'Jadwal KRS Asisten';

    protected static ?string $recordTitleAttribute = 'Jadwal_Asisten';

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
            'index' => ListJadwalAsistens::route('/'),
            'create' => CreateJadwalAsisten::route('/create'),
            'edit' => EditJadwalAsisten::route('/{record}/edit'),
        ];
    }
}

<?php

namespace App\Filament\Resources\JadwalLabs;

use App\Filament\Resources\JadwalLabs\Pages\CreateJadwalLab;
use App\Filament\Resources\JadwalLabs\Pages\EditJadwalLab;
use App\Filament\Resources\JadwalLabs\Pages\ListJadwalLabs;
use App\Filament\Resources\JadwalLabs\Pages\UploadJadwalLab;
use App\Filament\Resources\JadwalLabs\Schemas\JadwalLabForm;
use App\Filament\Resources\JadwalLabs\Tables\JadwalLabsTable;
use App\Models\JadwalLab;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class JadwalLabResource extends Resource
{
    protected static ?string $model = JadwalLab::class;

    protected static string | UnitEnum | null $navigationGroup  = 'Penjadwalan';
    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'Jadwal Laboratorium';

    public static function form(Schema $schema): Schema
    {
        return JadwalLabForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return JadwalLabsTable::configure($table);
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
            'index' => ListJadwalLabs::route('/'),
            'create' => CreateJadwalLab::route('/create'),
            'edit' => EditJadwalLab::route('/{record}/edit'),
            'upload' => UploadJadwalLab::route('/upload'),
        ];
    }
}

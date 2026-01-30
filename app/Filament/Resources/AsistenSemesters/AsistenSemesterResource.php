<?php

namespace App\Filament\Resources\AsistenSemesters;

use App\Filament\Resources\AsistenSemesters\Pages\CreateAsistenSemester;
use App\Filament\Resources\AsistenSemesters\Pages\EditAsistenSemester;
use App\Filament\Resources\AsistenSemesters\Pages\ListAsistenSemesters;
use App\Filament\Resources\AsistenSemesters\Schemas\AsistenSemesterForm;
use App\Filament\Resources\AsistenSemesters\Tables\AsistenSemestersTable;
use App\Models\AsistenSemester;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class AsistenSemesterResource extends Resource
{
    protected static ?string $model = AsistenSemester::class;

    protected static string | UnitEnum | null $navigationGroup  = 'Manajemen Asisten';
    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'id_asisten_semester';

    public static function getGloballySearchableAttributes(): array
    {
        return ['asisten.nama', 'asisten.npm'];
    }

    public static function getGlobalSearchResultTitle(Model $record): string
    {
        return $record->asisten->nama . ' (' . $record->semester->nama_semester . ')';
    }

    public static function form(Schema $schema): Schema
    {
        return AsistenSemesterForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AsistenSemestersTable::configure($table);
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
            'index' => ListAsistenSemesters::route('/'),
            'create' => CreateAsistenSemester::route('/create'),
            'edit' => EditAsistenSemester::route('/{record}/edit'),
        ];
    }
}

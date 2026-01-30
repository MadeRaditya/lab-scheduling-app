<?php

namespace App\Filament\Resources\JamOperasionalLabs;

use App\Filament\Resources\JamOperasionalLabs\Pages\CreateJamOperasionalLab;
use App\Filament\Resources\JamOperasionalLabs\Pages\EditJamOperasionalLab;
use App\Filament\Resources\JamOperasionalLabs\Pages\ListJamOperasionalLabs;
use App\Filament\Resources\JamOperasionalLabs\Schemas\JamOperasionalLabForm;
use App\Filament\Resources\JamOperasionalLabs\Tables\JamOperasionalLabsTable;
use App\Models\JamOperasionalLab;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class JamOperasionalLabResource extends Resource
{
    protected static ?string $model = JamOperasionalLab::class;

    protected static string | UnitEnum | null $navigationGroup  = 'Data Master';
    protected static ?int $navigationSort = 5;

    protected static ?string $recordTitleAttribute = 'hari';

    public static function form(Schema $schema): Schema
    {
        return JamOperasionalLabForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return JamOperasionalLabsTable::configure($table);
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
            'index' => ListJamOperasionalLabs::route('/'),
            'create' => CreateJamOperasionalLab::route('/create'),
            'edit' => EditJamOperasionalLab::route('/{record}/edit'),
        ];
    }
}

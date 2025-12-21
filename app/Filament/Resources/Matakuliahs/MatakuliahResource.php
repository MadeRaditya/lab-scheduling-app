<?php

namespace App\Filament\Resources\Matakuliahs;

use App\Filament\Resources\Matakuliahs\Pages\CreateMatakuliah;
use App\Filament\Resources\Matakuliahs\Pages\EditMatakuliah;
use App\Filament\Resources\Matakuliahs\Pages\ListMatakuliahs;
use App\Filament\Resources\Matakuliahs\Pages\UploadMatakuliah;
use App\Filament\Resources\Matakuliahs\RelationManagers\KompetensiRelationManager;
use App\Filament\Resources\Matakuliahs\Schemas\MatakuliahForm;
use App\Filament\Resources\Matakuliahs\Tables\MatakuliahsTable;
use App\Models\Matakuliah;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class MatakuliahResource extends Resource
{
    protected static ?string $model = Matakuliah::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::BookOpen;

    protected static ?string $recordTitleAttribute = 'nama_mk';

    public static function form(Schema $schema): Schema
    {
        return MatakuliahForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MatakuliahsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            KompetensiRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMatakuliahs::route('/'),
            'create' => CreateMatakuliah::route('/create'),
            'edit' => EditMatakuliah::route('/{record}/edit'),
            'upload' => UploadMatakuliah::route('/upload'),
        ];
    }
}

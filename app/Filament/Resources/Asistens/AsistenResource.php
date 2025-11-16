<?php

namespace App\Filament\Resources\Asistens;

use App\Filament\Resources\Asistens\Pages\CreateAsisten;
use App\Filament\Resources\Asistens\Pages\EditAsisten;
use App\Filament\Resources\Asistens\Pages\ListAsistens;
use App\Filament\Resources\Asistens\Schemas\AsistenForm;
use App\Filament\Resources\Asistens\Tables\AsistensTable;
use App\Models\Asisten;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class AsistenResource extends Resource
{
    protected static ?string $model = Asisten::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'Asisten Laboratorium';

    public static function form(Schema $schema): Schema
    {
        return AsistenForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AsistensTable::configure($table);
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
            'index' => ListAsistens::route('/'),
            'create' => CreateAsisten::route('/create'),
            'edit' => EditAsisten::route('/{record}/edit'),
        ];
    }
}

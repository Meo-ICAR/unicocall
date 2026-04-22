<?php

namespace App\Filament\Resources\COMPILANCE\SoftwareCategories;

use App\Filament\Resources\COMPILANCE\SoftwareCategories\Pages\CreateSoftwareCategory;
use App\Filament\Resources\COMPILANCE\SoftwareCategories\Pages\EditSoftwareCategory;
use App\Filament\Resources\COMPILANCE\SoftwareCategories\Pages\ListSoftwareCategories;
use App\Filament\Resources\COMPILANCE\SoftwareCategories\Schemas\SoftwareCategoryForm;
use App\Filament\Resources\COMPILANCE\SoftwareCategories\Tables\SoftwareCategoriesTable;
use App\Models\COMPILANCE\SoftwareCategory;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use BackedEnum;

class SoftwareCategoryResource extends Resource
{
    protected static ?string $model = SoftwareCategory::class;

    protected static bool $isScopedToTenant = false;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSquare2Stack;

    protected static ?string $recordTitleAttribute = 'name';

    protected static string|\UnitEnum|null $navigationGroup = 'Compliance';

    protected static ?int $navigationSort = 65;

    public static function form(Schema $schema): Schema
    {
        return SoftwareCategoryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SoftwareCategoriesTable::configure($table);
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
            'index' => ListSoftwareCategories::route('/'),
            'create' => CreateSoftwareCategory::route('/create'),
            'edit' => EditSoftwareCategory::route('/{record}/edit'),
        ];
    }
}

<?php

namespace App\Filament\Admin\Resources\Websites;

use App\Filament\Admin\Resources\Websites\Pages\CreateWebsite;
use App\Filament\Admin\Resources\Websites\Pages\EditWebsite;
use App\Filament\Admin\Resources\Websites\Pages\ListWebsites;
use App\Filament\Admin\Resources\Websites\Schemas\WebsiteForm;
use App\Filament\Admin\Resources\Websites\Tables\WebsitesTable;
use App\Models\Website;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use BackedEnum;

class WebsiteResource extends Resource
{
    protected static ?string $model = Website::class;
    protected static bool $shouldRegisterNavigation = false;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return WebsiteForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return WebsitesTable::configure($table);
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
            'index' => ListWebsites::route('/'),
            'create' => CreateWebsite::route('/create'),
            'edit' => EditWebsite::route('/{record}/edit'),
        ];
    }
}

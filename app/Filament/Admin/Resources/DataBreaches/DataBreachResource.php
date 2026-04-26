<?php

namespace App\Filament\Admin\Resources\DataBreaches;

use App\Filament\Admin\Resources\DataBreaches\Pages\CreateDataBreach;
use App\Filament\Admin\Resources\DataBreaches\Pages\EditDataBreach;
use App\Filament\Admin\Resources\DataBreaches\Pages\ListDataBreaches;
use App\Filament\Admin\Resources\DataBreaches\Schemas\DataBreachForm;
use App\Filament\Admin\Resources\DataBreaches\Tables\DataBreachesTable;
use App\Models\DataBreach;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class DataBreachResource extends Resource
{
    protected static ?string $model = DataBreach::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;
protected static string|\UnitEnum|null $navigationGroup = 'Privacy & GDPR';
    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return DataBreachForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DataBreachesTable::configure($table);
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
            'index' => ListDataBreaches::route('/'),
            'create' => CreateDataBreach::route('/create'),
            'edit' => EditDataBreach::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}

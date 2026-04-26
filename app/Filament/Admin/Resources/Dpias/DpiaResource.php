<?php

namespace App\Filament\Admin\Resources\Dpias;

use App\Filament\Admin\RelationManagers\DpiaItemsRelationManager;
use App\Filament\Admin\Resources\Dpias\Pages\CreateDpia;
use App\Filament\Admin\Resources\Dpias\Pages\EditDpia;
use App\Filament\Admin\Resources\Dpias\Pages\ListDpias;
use App\Filament\Admin\Resources\Dpias\Schemas\DpiaForm;
use App\Filament\Admin\Resources\Dpias\Tables\DpiasTable;
use App\Models\Dpia;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DpiaResource extends Resource
{
    protected static ?string $model = Dpia::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShieldExclamation;

    protected static ?string $navigationLabel = 'DPIA';

    protected static string|\UnitEnum|null $navigationGroup = 'Privacy & GDPR';

    protected static ?string $modelLabel = 'DPIA';

    protected static ?string $pluralModelLabel = 'DPIA';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return DpiaForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DpiasTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            DpiaItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDpias::route('/'),
            'create' => CreateDpia::route('/create'),
            'edit' => EditDpia::route('/{record}/edit'),
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

<?php

namespace App\Filament\Admin\Resources\RegistroTrattamentis;

use App\Filament\Admin\RelationManagers\SubappaltiClientiRelationManager;
use App\Filament\Admin\RelationManagers\SubappaltiDipendentiRelationManager;
use App\Filament\Admin\Resources\RegistroTrattamentis\Pages\CreateRegistroTrattamenti;
use App\Filament\Admin\Resources\RegistroTrattamentis\Pages\EditRegistroTrattamenti;
use App\Filament\Admin\Resources\RegistroTrattamentis\Pages\ListRegistroTrattamentis;
use App\Filament\Admin\Resources\RegistroTrattamentis\Schemas\RegistroTrattamentiForm;
use App\Filament\Admin\Resources\RegistroTrattamentis\Tables\RegistroTrattamentisTable;
use App\Models\RegistroTrattamenti;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use BackedEnum;

class RegistroTrattamentiResource extends Resource
{
    protected static ?string $model = RegistroTrattamenti::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return RegistroTrattamentiForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RegistroTrattamentisTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            SubappaltiClientiRelationManager::class,
            SubappaltiDipendentiRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRegistroTrattamentis::route('/'),
            'create' => CreateRegistroTrattamenti::route('/create'),
            'edit' => EditRegistroTrattamenti::route('/{record}/edit'),
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

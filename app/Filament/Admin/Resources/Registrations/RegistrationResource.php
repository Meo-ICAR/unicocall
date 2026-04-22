<?php

namespace App\Filament\Admin\Resources\Registrations;

use App\Filament\Admin\Resources\Registrations\Pages\CreateRegistration;
use App\Filament\Admin\Resources\Registrations\Pages\EditRegistration;
use App\Filament\Admin\Resources\Registrations\Pages\ListRegistrations;
use App\Filament\Admin\Resources\Registrations\Schemas\RegistrationForm;
use App\Filament\Admin\Resources\Registrations\Tables\RegistrationsTable;
use App\Models\Registration;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use BackedEnum;

class RegistrationResource extends Resource
{
    protected static ?string $model = Registration::class;
    protected static bool $shouldRegisterNavigation = false;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;
    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return RegistrationForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RegistrationsTable::configure($table);
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
            'index' => ListRegistrations::route('/'),
            'create' => CreateRegistration::route('/create'),
            'edit' => EditRegistration::route('/{record}/edit'),
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

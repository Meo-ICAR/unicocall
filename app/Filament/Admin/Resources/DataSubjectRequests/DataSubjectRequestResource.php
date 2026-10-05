<?php

namespace App\Filament\Admin\Resources\DataSubjectRequests;

use App\Filament\Admin\Resources\DataSubjectRequests\Pages\CreateDataSubjectRequest;
use App\Filament\Admin\Resources\DataSubjectRequests\Pages\EditDataSubjectRequest;
use App\Filament\Admin\Resources\DataSubjectRequests\Pages\ListDataSubjectRequests;
use App\Filament\Admin\Resources\DataSubjectRequests\Schemas\DataSubjectRequestForm;
use App\Filament\Admin\Resources\DataSubjectRequests\Tables\DataSubjectRequestsTable;
use App\Models\DataSubjectRequest;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DataSubjectRequestResource extends Resource
{
    protected static ?string $model = DataSubjectRequest::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedInboxArrowDown;

    protected static string|\UnitEnum|null $navigationGroup = 'Privacy & GDPR';

    protected static ?string $navigationLabel = 'Richieste Interessati';

    protected static ?string $modelLabel = 'Richiesta';

    protected static ?string $pluralModelLabel = 'Richieste Interessati (GDPR)';

    protected static ?string $recordTitleAttribute = 'requester_name';

    protected static ?int $navigationSort = 20;

    public static function form(Schema $schema): Schema
    {
        return DataSubjectRequestForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DataSubjectRequestsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListDataSubjectRequests::route('/'),
            'create' => CreateDataSubjectRequest::route('/create'),
            'edit'   => EditDataSubjectRequest::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([SoftDeletingScope::class]);
    }
}

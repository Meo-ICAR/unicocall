<?php

namespace App\Filament\Admin\Resources\TrainingRecords;

use App\Filament\Admin\Resources\TrainingRecords\Pages\CreateTrainingRecord;
use App\Filament\Admin\Resources\TrainingRecords\Pages\EditTrainingRecord;
use App\Filament\Admin\Resources\TrainingRecords\Pages\ListTrainingRecords;
use App\Filament\Admin\Resources\TrainingRecords\Schemas\TrainingRecordForm;
use App\Filament\Admin\Resources\TrainingRecords\Tables\TrainingRecordsTable;
use App\Models\TrainingRecord;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TrainingRecordResource extends Resource
{
    protected static ?string $model = TrainingRecord::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedAcademicCap;

    protected static string|\UnitEnum|null $navigationGroup = 'Privacy & GDPR';

    protected static ?string $navigationLabel = 'Registro Formazione';

    protected static ?string $modelLabel = 'Formazione';

    protected static ?string $pluralModelLabel = 'Registro Formazione';

    protected static ?string $recordTitleAttribute = 'course_title';

    protected static ?int $navigationSort = 30;

    public static function form(Schema $schema): Schema
    {
        return TrainingRecordForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TrainingRecordsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListTrainingRecords::route('/'),
            'create' => CreateTrainingRecord::route('/create'),
            'edit'   => EditTrainingRecord::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([SoftDeletingScope::class]);
    }
}

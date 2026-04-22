<?php

namespace App\Filament\Admin\RelationManagers;

use App\Filament\Admin\Resources\SoftwareApplications\Schemas\SoftwareApplicationForm;
use App\Filament\Admin\Resources\SoftwareApplications\Tables\SoftwareApplicationsTable;
use App\Models\SoftwareApplication;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Actions;
use Illuminate\Database\Eloquent\Builder;

class SoftwareApplicationsRelationManager extends RelationManager
{
    protected static string $relationship = 'softwareApplications';
    protected static ?string $title = 'Applicazioni Software';
    protected static ?string $model = SoftwareApplication::class;

    public function form(Schema $schema): Schema
    {
        return SoftwareApplicationForm::configure($schema);
    }

    public function table(Table $table): Table
    {
        return SoftwareApplicationsTable::configure($table)
            ->headerActions([
                Actions\CreateAction::make()
                    ->label('Nuova Applicazione'),
            ]);
    }
}

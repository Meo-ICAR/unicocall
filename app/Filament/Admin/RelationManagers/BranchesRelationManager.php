<?php

namespace App\Filament\Admin\RelationManagers;

use App\Filament\Admin\Resources\Branches\Schemas\BranchForm;
use App\Filament\Admin\Resources\Branches\Tables\BranchesTable;
use App\Models\Branch;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Actions;
use Illuminate\Database\Eloquent\Builder;

class BranchesRelationManager extends RelationManager
{
    protected static string $relationship = 'branches';
    protected static ?string $title = 'Sedi';
    protected static ?string $model = Branch::class;

    public function form(Schema $schema): Schema
    {
        return BranchForm::configure($schema);
    }

    public function table(Table $table): Table
    {
        return BranchesTable::configure($table)
            ->headerActions([
                Actions\CreateAction::make()
                    ->label('Nuova Sede')
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['company_id'] = $this->getOwnerRecord()->id;
                        return $data;
                    }),
            ])
            ->modifyQueryUsing(function (Builder $query) {
                // Ensure we only get branches for the current company
                $query->where('company_id', $this->getOwnerRecord()->id);
            });
    }
}

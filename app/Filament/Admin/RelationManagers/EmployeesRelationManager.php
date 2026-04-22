<?php

namespace App\Filament\Admin\RelationManagers;

use App\Filament\Admin\Resources\Employees\Schemas\EmployeeForm;
use App\Filament\Admin\Resources\Employees\Tables\EmployeesTable;
use App\Models\Employee;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Actions;
use Illuminate\Database\Eloquent\Builder;

class EmployeesRelationManager extends RelationManager
{
    protected static string $relationship = 'employees';
    protected static ?string $title = 'Dipendenti';
    protected static ?string $model = Employee::class;

    public function form(Schema $schema): Schema
    {
        return EmployeeForm::configure($schema);
    }

    public function table(Table $table): Table
    {
        return EmployeesTable::configure($table)
            ->headerActions([
                Actions\CreateAction::make()
                    ->label('Nuovo Dipendente'),
            ]);
    }
}

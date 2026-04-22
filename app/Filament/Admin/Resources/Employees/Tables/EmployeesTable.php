<?php

namespace App\Filament\Admin\Resources\Employees\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class EmployeesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('company_id')
                    ->searchable(),
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('role')
                    ->searchable(),
                TextColumn::make('cf')
                    ->searchable(),
                TextColumn::make('phone')
                    ->searchable(),
                TextColumn::make('department')
                    ->searchable(),
                TextColumn::make('hiring_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('termination_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('company_branch_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('coordinated_by_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('employee_types')
                    ->searchable(),
                TextColumn::make('supervisor_type')
                    ->searchable(),
                TextColumn::make('privacy_role')
                    ->searchable(),
                TextColumn::make('retention_period')
                    ->searchable(),
                TextColumn::make('extra_eu_transfer')
                    ->searchable(),
                TextColumn::make('privacy_data')
                    ->searchable(),
                IconColumn::make('is_structure')
                    ->boolean(),
                IconColumn::make('is_ghost')
                    ->boolean(),
                TextColumn::make('employee_type')
                    ->searchable(),
                TextColumn::make('employee_id')
                    ->searchable(),
                TextColumn::make('user_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('created_by')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('updated_by')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('deleted_by')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}

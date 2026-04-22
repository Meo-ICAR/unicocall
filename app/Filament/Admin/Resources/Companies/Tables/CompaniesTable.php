<?php

namespace App\Filament\Admin\Resources\Companies\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CompaniesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->searchable(),
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('vat_number')
                    ->searchable(),
                TextColumn::make('sponsor')
                    ->searchable(),
                TextColumn::make('company_type')
                    ->badge(),
                IconColumn::make('is_iso27001_certified')
                    ->boolean(),
                TextColumn::make('contact_email')
                    ->searchable(),
                TextColumn::make('dpo_email')
                    ->searchable(),
                TextColumn::make('user_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('smtp_host')
                    ->searchable(),
                TextColumn::make('smtp_port')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('smtp_encryption')
                    ->searchable(),
                IconColumn::make('smtp_enabled')
                    ->boolean(),
                IconColumn::make('smtp_verify_ssl')
                    ->boolean(),
                TextColumn::make('payment_frequency')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('payment')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('payment_last_date')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('payment_startup')
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

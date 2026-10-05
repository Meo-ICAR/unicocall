<?php

namespace App\Filament\Admin\Resources\Websites\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class WebsitesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('type')
                    ->searchable(),
                TextColumn::make('clienti_id')
                    ->numeric()
                    ->sortable(),
                IconColumn::make('is_active')
                    ->boolean(),
                TextColumn::make('domain')
                    ->searchable(),
                IconColumn::make('is_typical')
                    ->boolean(),
                TextColumn::make('privacy_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('transparency_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('privacy_prior_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('transparency_prior_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('url_privacy')
                    ->searchable(),
                TextColumn::make('url_cookies')
                    ->searchable(),
                IconColumn::make('is_footercompilant')
                    ->boolean(),
                TextColumn::make('url_transparency')
                    ->searchable(),
                IconColumn::make('is_iso27001_certified')
                    ->boolean(),
                TextColumn::make('websiteable_type')
                    ->searchable(),
                TextColumn::make('websiteable_id')
                    ->searchable(),
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

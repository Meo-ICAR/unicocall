<?php

namespace App\Filament\Admin\Resources\SoftwareApplications\Tables;

use App\Models\SoftwareApplication;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Columns;
use Filament\Tables\Table;
use Filament\Actions;
use Illuminate\Database\Eloquent\Builder;

class SoftwareApplicationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                Columns\TextColumn::make('name')
                    ->label('Nome')
                    ->searchable()
                    ->sortable(),
                Columns\TextColumn::make('provider_name')
                    ->label('Provider')
                    ->searchable()
                    ->sortable(),
                Columns\TextColumn::make('softwareCategory.name')
                    ->label('Categoria')
                    ->searchable()
                    ->sortable(),
                Columns\IconColumn::make('is_cloud')
                    ->label('Cloud')
                    ->boolean()
                    ->trueIcon('fas-cloud')
                    ->falseIcon('fas-server')
                    ->sortable(),
                Columns\TextColumn::make('website_url')
                    ->label('Sito Web')
                    ->url(fn($record) => $record->website_url)
                    ->openUrlInNewTab()
                    ->limit(30),
                Columns\TextColumn::make('wallet_balance')
                    ->label('Saldo')
                    ->money('EUR')
                    ->sortable(),
                Columns\TextColumn::make('created_at')
                    ->label('Creato')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('software_category_id')
                    ->label('Categoria')
                    ->relationship('softwareCategory', 'name')
                    ->searchable()
                    ->preload(),
                TernaryFilter::make('is_cloud')
                    ->label('Tipo')
                    ->trueLabel('Cloud')
                    ->falseLabel('On-premise')
                    ->placeholder('Tutti'),
            ])
            ->actions([
                Actions\EditAction::make(),
                Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Actions\DeleteBulkAction::make(),
            ]);
    }
}

<?php

namespace App\Filament\Admin\Resources\DataBreaches\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class DataBreachesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('discovered_at')
                    ->label('Data scoperta')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                TextColumn::make('occurred_at')
                    ->label('Data occorrenza')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                TextColumn::make('approximate_records_count')
                    ->label('N. record')
                    ->numeric()
                    ->sortable(),
                IconColumn::make('is_notifiable_to_authority')
                    ->label('Notif. autorità')
                    ->boolean(),
                IconColumn::make('is_notifiable_to_subjects')
                    ->label('Notif. interessati')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->label('Creato')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Aggiornato')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deleted_at')
                    ->label('Eliminato')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}

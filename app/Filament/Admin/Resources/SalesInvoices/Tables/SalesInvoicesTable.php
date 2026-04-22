<?php

namespace App\Filament\Admin\Resources\SalesInvoices\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class SalesInvoicesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->searchable(),
                TextColumn::make('numero')
                    ->searchable(),
                TextColumn::make('nome_file')
                    ->searchable(),
                TextColumn::make('id_sdi')
                    ->searchable(),
                TextColumn::make('data_invio')
                    ->date()
                    ->sortable(),
                TextColumn::make('data_documento')
                    ->date()
                    ->sortable(),
                TextColumn::make('tipo_documento')
                    ->searchable(),
                TextColumn::make('tipo_cliente')
                    ->searchable(),
                TextColumn::make('cliente')
                    ->searchable(),
                TextColumn::make('partita_iva')
                    ->searchable(),
                TextColumn::make('codice_fiscale')
                    ->searchable(),
                TextColumn::make('indirizzo_telematico')
                    ->searchable(),
                TextColumn::make('metodo_pagamento')
                    ->searchable(),
                TextColumn::make('totale_imponibile')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('totale_escluso_iva_n1')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('totale_non_soggetto_iva_n2')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('totale_non_imponibile_iva_n3')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('totale_esente_iva_n4')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('totale_regime_margine_iva_n5')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('totale_inversione_contabile_n6')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('totale_iva_assolta_altro_stato_ue_n7')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('totale_iva')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('totale_documento')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('netto_a_pagare')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('incassi')
                    ->searchable(),
                TextColumn::make('data_incasso')
                    ->date()
                    ->sortable(),
                TextColumn::make('stato')
                    ->searchable(),
                TextColumn::make('company.name')
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

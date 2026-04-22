<?php

namespace App\Filament\Admin\Resources\PurchaseInvoices\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PurchaseInvoiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('numero')
                    ->required(),
                TextInput::make('nome_file'),
                TextInput::make('id_sdi'),
                DatePicker::make('data_ricezione'),
                DatePicker::make('data_documento'),
                TextInput::make('tipo_documento'),
                TextInput::make('fornitore')
                    ->required(),
                TextInput::make('partita_iva'),
                TextInput::make('codice_fiscale'),
                TextInput::make('metodo_pagamento'),
                TextInput::make('totale_imponibile')
                    ->numeric(),
                TextInput::make('totale_escluso_iva_n1')
                    ->required()
                    ->numeric()
                    ->default(0.0),
                TextInput::make('totale_non_soggetto_iva_n2')
                    ->required()
                    ->numeric()
                    ->default(0.0),
                TextInput::make('totale_non_imponibile_iva_n3')
                    ->required()
                    ->numeric()
                    ->default(0.0),
                TextInput::make('totale_esente_iva_n4')
                    ->required()
                    ->numeric()
                    ->default(0.0),
                TextInput::make('totale_regime_margine_iva_n5')
                    ->required()
                    ->numeric()
                    ->default(0.0),
                TextInput::make('totale_inversione_contabile_n6')
                    ->required()
                    ->numeric()
                    ->default(0.0),
                TextInput::make('totale_iva_assolta_altro_stato_ue_n7')
                    ->required()
                    ->numeric()
                    ->default(0.0),
                TextInput::make('totale_iva')
                    ->required()
                    ->numeric()
                    ->default(0.0),
                TextInput::make('totale_documento')
                    ->required()
                    ->numeric(),
                TextInput::make('netto_a_pagare')
                    ->required()
                    ->numeric(),
                TextInput::make('pagamenti'),
                DatePicker::make('data_pagamento'),
                TextInput::make('stato')
                    ->required(),
                Select::make('company_id')
                    ->relationship('company', 'name'),
            ]);
    }
}

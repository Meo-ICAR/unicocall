<?php

namespace App\Filament\Admin\Resources\PurchaseInvoices\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;

class PurchaseInvoiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('PurchaseInvoiceTabs')
                    ->tabs([
                        // Informazioni Generali Tab
                        Tabs\Tab::make('Informazioni Generali')
                            ->icon('heroicon-o-document-text')
                            ->schema([
                                Section::make('Dati Fattura')
                                    ->description('Informazioni principali della fattura')
                                    ->schema([
                                        TextInput::make('numero')
                                            ->label('Numero Fattura')
                                            ->required()
                                            ->placeholder('Es. 123')
                                            ->helperText('Numero progressivo della fattura'),
                                        TextInput::make('nome_file')
                                            ->label('Nome File')
                                            ->placeholder('Es. Fattura_123.pdf')
                                            ->helperText('Nome del file originale della fattura'),
                                        TextInput::make('id_sdi')
                                            ->label('ID SDI')
                                            ->placeholder('Es. 5RUO82D')
                                            ->helperText('Identificativo del Sistema di Interscambio'),
                                        Select::make('tipo_documento')
                                            ->label('Tipo Documento')
                                            ->options([
                                                'TD01' => 'Fattura',
                                                'TD02' => 'Acconto su fattura',
                                                'TD03' => 'Acconto su parcella',
                                                'TD04' => 'Nota di credito',
                                                'TD05' => 'Nota di debito',
                                                'TD06' => 'Parcella',
                                            ])
                                            ->default('TD01')
                                            ->placeholder('Seleziona tipo documento')
                                            ->helperText('Tipo di documento fiscale'),
                                    ])
                                    ->columns(2),
                                Section::make('Date Documento')
                                    ->description('Date relative al documento')
                                    ->schema([
                                        DatePicker::make('data_ricezione')
                                            ->label('Data Ricezione')
                                            ->placeholder('Seleziona data di ricezione')
                                            ->helperText('Data in cui la fattura è stata ricevuta'),
                                        DatePicker::make('data_documento')
                                            ->label('Data Documento')
                                            ->required()
                                            ->placeholder('Seleziona data del documento')
                                            ->helperText('Data di emissione della fattura'),
                                    ])
                                    ->columns(2),
                            ]),
                        // Fornitore Tab
                        Tabs\Tab::make('Fornitore')
                            ->icon('heroicon-o-building-storefront')
                            ->schema([
                                Section::make('Dati Fornitore')
                                    ->description('Informazioni sul fornitore')
                                    ->schema([
                                        TextInput::make('fornitore')
                                            ->label('Ragione Sociale')
                                            ->required()
                                            ->placeholder('Es. Mario Rossi Srl')
                                            ->helperText('Nome completo del fornitore'),
                                        TextInput::make('partita_iva')
                                            ->label('Partita IVA')
                                            ->placeholder('Es. 12345678901')
                                            ->helperText('Partita IVA del fornitore (11 caratteri)'),
                                        TextInput::make('codice_fiscale')
                                            ->label('Codice Fiscale')
                                            ->placeholder('Es. RSSMRA85T10A562S')
                                            ->helperText('Codice fiscale del fornitore (16 caratteri)'),
                                        TextInput::make('metodo_pagamento')
                                            ->label('Metodo di Pagamento')
                                            ->placeholder('Es. Bonifico Bancario')
                                            ->helperText('Modalità di pagamento prevista'),
                                    ])
                                    ->columns(2),
                            ]),
                        // Importi Tab
                        Tabs\Tab::make('Importi')
                            ->icon('heroicon-o-currency-euro')
                            ->schema([
                                Section::make('Importi Base')
                                    ->description('Importi principali della fattura')
                                    ->schema([
                                        TextInput::make('totale_imponibile')
                                            ->label('Totale Imponibile')
                                            ->numeric()
                                            ->prefix('EUR')
                                            ->placeholder('0.00')
                                            ->step('0.01')
                                            ->helperText('Importo totale senza IVA'),
                                        TextInput::make('totale_iva')
                                            ->label('Totale IVA')
                                            ->required()
                                            ->numeric()
                                            ->prefix('EUR')
                                            ->default(0.0)
                                            ->placeholder('0.00')
                                            ->step('0.01')
                                            ->helperText("Importo totale dell'IVA"),
                                        TextInput::make('totale_documento')
                                            ->label('Totale Documento')
                                            ->required()
                                            ->numeric()
                                            ->prefix('EUR')
                                            ->placeholder('0.00')
                                            ->step('0.01')
                                            ->helperText('Importo totale del documento'),
                                        TextInput::make('netto_a_pagare')
                                            ->label('Netto a Pagare')
                                            ->required()
                                            ->numeric()
                                            ->prefix('EUR')
                                            ->placeholder('0.00')
                                            ->step('0.01')
                                            ->helperText('Importo netto da pagare'),
                                    ])
                                    ->columns(2),
                                Section::make('Dettaglio IVA (Natura Operazioni)')
                                    ->description('Dettaglio degli importi per natura operazione IVA')
                                    ->schema([
                                        TextInput::make('totale_escluso_iva_n1')
                                            ->label('Escluso IVA (N1)')
                                            ->required()
                                            ->numeric()
                                            ->prefix('EUR')
                                            ->default(0.0)
                                            ->placeholder('0.00')
                                            ->step('0.01')
                                            ->helperText('Operazioni escluse da IVA'),
                                        TextInput::make('totale_non_soggetto_iva_n2')
                                            ->label('Non Soggetto IVA (N2)')
                                            ->required()
                                            ->numeric()
                                            ->prefix('EUR')
                                            ->default(0.0)
                                            ->placeholder('0.00')
                                            ->step('0.01')
                                            ->helperText('Operazioni non soggette a IVA'),
                                        TextInput::make('totale_non_imponibile_iva_n3')
                                            ->label('Non Imponibile IVA (N3)')
                                            ->required()
                                            ->numeric()
                                            ->prefix('EUR')
                                            ->default(0.0)
                                            ->placeholder('0.00')
                                            ->step('0.01')
                                            ->helperText('Operazioni non imponibili IVA'),
                                        TextInput::make('totale_esente_iva_n4')
                                            ->label('Esente IVA (N4)')
                                            ->required()
                                            ->numeric()
                                            ->prefix('EUR')
                                            ->default(0.0)
                                            ->placeholder('0.00')
                                            ->step('0.01')
                                            ->helperText('Operazioni esenti da IVA'),
                                    ])
                                    ->columns(2),
                                Section::make('Altri Regimi IVA')
                                    ->description('Altri regimi IVA speciali')
                                    ->schema([
                                        TextInput::make('totale_regime_margine_iva_n5')
                                            ->label('Regime Margine (N5)')
                                            ->required()
                                            ->numeric()
                                            ->prefix('EUR')
                                            ->default(0.0)
                                            ->placeholder('0.00')
                                            ->step('0.01')
                                            ->helperText('Operazioni in regime di margine'),
                                        TextInput::make('totale_inversione_contabile_n6')
                                            ->label('Inversione Contabile (N6)')
                                            ->required()
                                            ->numeric()
                                            ->prefix('EUR')
                                            ->default(0.0)
                                            ->placeholder('0.00')
                                            ->step('0.01')
                                            ->helperText('Operazioni con inversione contabile'),
                                        TextInput::make('totale_iva_assolta_altro_stato_ue_n7')
                                            ->label('IVA Assolta Altro Stato UE (N7)')
                                            ->required()
                                            ->numeric()
                                            ->prefix('EUR')
                                            ->default(0.0)
                                            ->placeholder('0.00')
                                            ->step('0.01')
                                            ->helperText('IVA assolta in altro stato UE'),
                                    ])
                                    ->columns(3),
                            ]),
                        // Pagamenti Tab
                        Tabs\Tab::make('Pagamenti')
                            ->icon('heroicon-o-credit-card')
                            ->schema([
                                Section::make('Stato Pagamento')
                                    ->description('Informazioni sullo stato del pagamento')
                                    ->schema([
                                        Select::make('stato')
                                            ->label('Stato Pagamento')
                                            ->options([
                                                'Pagata' => 'Pagata',
                                                'Non pagata' => 'Non pagata',
                                                'Parzialmente pagata' => 'Parzialmente pagata',
                                            ])
                                            ->required()
                                            ->placeholder('Seleziona stato pagamento')
                                            ->helperText('Stato attuale del pagamento'),
                                        DatePicker::make('data_pagamento')
                                            ->label('Data Pagamento')
                                            ->placeholder('Seleziona data pagamento')
                                            ->helperText('Data in cui è stato effettuato il pagamento'),
                                        TextInput::make('pagamenti')
                                            ->label('Dettaglio Pagamenti')
                                            ->placeholder('Es. Bonifico del 15/01/2024')
                                            ->helperText('Note dettagliate sui pagamenti effettuati'),
                                    ])
                                    ->columns(2),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}

<?php

namespace App\Filament\Admin\Resources\DataSubjectRequests\Schemas;

use App\Models\Client;
use App\Models\DataSubjectRequest;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class DataSubjectRequestForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('dsr_tabs')
                    ->tabs([

                        Tab::make('Richiedente')
                            ->icon('heroicon-o-user')
                            ->schema([
                                Section::make('Collegamento anagrafica')
                                    ->description('Se il richiedente è già presente come cliente, collegalo per tracciabilità')
                                    ->schema([
                                        Select::make('client_id')
                                            ->label('Cliente / Lead esistente')
                                            ->placeholder('Cerca per nome o email...')
                                            ->searchable()
                                            ->nullable()
                                            ->getSearchResultsUsing(fn(string $search) =>
                                                Client::query()
                                                    ->where(fn($q) => $q
                                                        ->where('name', 'like', "%{$search}%")
                                                        ->orWhere('email', 'like', "%{$search}%")
                                                        ->orWhere('first_name', 'like', "%{$search}%")
                                                    )
                                                    ->limit(30)
                                                    ->get()
                                                    ->mapWithKeys(fn($c) => [
                                                        $c->id => trim("{$c->first_name} {$c->name}") . ($c->email ? " <{$c->email}>" : ''),
                                                    ])
                                                    ->toArray()
                                            )
                                            ->getOptionLabelUsing(fn($value) => optional(Client::find($value))->name ?? $value)
                                            ->helperText('Opzionale — lascia vuoto per soggetti non presenti in anagrafica')
                                            ->columnSpanFull(),
                                    ]),

                                Section::make('Dati del richiedente')
                                    ->columns(2)
                                    ->schema([
                                        TextInput::make('requester_name')
                                            ->label('Nome e Cognome / Ragione Sociale')
                                            ->required()
                                            ->maxLength(255)
                                            ->placeholder('Es. Mario Rossi')
                                            ->columnSpanFull(),

                                        TextInput::make('requester_email')
                                            ->label('Email')
                                            ->email()
                                            ->maxLength(255)
                                            ->placeholder('mario.rossi@email.it'),

                                        TextInput::make('requester_phone')
                                            ->label('Telefono')
                                            ->tel()
                                            ->maxLength(30)
                                            ->placeholder('+39 333 1234567'),
                                    ]),

                                Section::make('Verifica identità')
                                    ->description('Art. 12 par. 6 GDPR — il titolare può richiedere informazioni aggiuntive per verificare l\'identità')
                                    ->columns(2)
                                    ->schema([
                                        Toggle::make('identity_verified')
                                            ->label('Identità verificata')
                                            ->helperText('Spunta dopo aver verificato l\'identità del richiedente')
                                            ->live()
                                            ->default(false),

                                        Select::make('identity_verification_method')
                                            ->label('Metodo di verifica')
                                            ->options(DataSubjectRequest::getVerificationMethodOptions())
                                            ->native(false)
                                            ->nullable()
                                            ->visible(fn(Get $get) => $get('identity_verified')),
                                    ]),
                            ]),

                        Tab::make('Richiesta')
                            ->icon('heroicon-o-document-text')
                            ->schema([
                                Section::make('Tipo e canale')
                                    ->columns(2)
                                    ->schema([
                                        Select::make('request_type')
                                            ->label('Tipo di richiesta')
                                            ->required()
                                            ->options(DataSubjectRequest::getRequestTypeOptions())
                                            ->native(false)
                                            ->helperText('Seleziona l\'articolo GDPR applicabile'),

                                        Select::make('channel')
                                            ->label('Canale di ricezione')
                                            ->required()
                                            ->options(DataSubjectRequest::getChannelOptions())
                                            ->native(false)
                                            ->default('email'),
                                    ]),

                                Section::make('Descrizione')
                                    ->schema([
                                        Textarea::make('request_description')
                                            ->label('Descrizione della richiesta')
                                            ->required()
                                            ->rows(4)
                                            ->placeholder('Descrivi in dettaglio cosa richiede l\'interessato...')
                                            ->columnSpanFull(),
                                    ]),
                            ]),

                        Tab::make('Gestione & Scadenze')
                            ->icon('heroicon-o-clock')
                            ->schema([
                                Section::make('Date')
                                    ->columns(2)
                                    ->description('La scadenza viene calcolata automaticamente a 30 giorni dalla ricezione (Art. 12 par. 3 GDPR)')
                                    ->schema([
                                        DatePicker::make('received_at')
                                            ->label('Data ricezione')
                                            ->required()
                                            ->displayFormat('d/m/Y')
                                            ->default(now())
                                            ->live()
                                            ->afterStateUpdated(function ($state, callable $set) {
                                                if ($state) {
                                                    $set('deadline_at', \Carbon\Carbon::parse($state)->addDays(30)->format('Y-m-d'));
                                                }
                                            }),

                                        DatePicker::make('deadline_at')
                                            ->label('Scadenza (30 gg)')
                                            ->required()
                                            ->displayFormat('d/m/Y')
                                            ->helperText('Calcolata automaticamente — modificabile se necessario'),

                                        DatePicker::make('extended_until')
                                            ->label('Proroga fino al')
                                            ->displayFormat('d/m/Y')
                                            ->nullable()
                                            ->helperText('Art. 12 par. 3 — proroga di ulteriori 2 mesi per richieste complesse'),

                                        DatePicker::make('completed_at')
                                            ->label('Data evasione')
                                            ->displayFormat('d/m/Y')
                                            ->nullable(),
                                    ]),

                                Section::make('Stato e risposta')
                                    ->schema([
                                        Select::make('status')
                                            ->label('Stato')
                                            ->required()
                                            ->options(DataSubjectRequest::getStatusOptions())
                                            ->native(false)
                                            ->default('received')
                                            ->live(),

                                        Textarea::make('response_notes')
                                            ->label('Note sulla risposta')
                                            ->rows(3)
                                            ->placeholder('Descrivi come è stata gestita la richiesta...')
                                            ->columnSpanFull(),

                                        Textarea::make('rejection_reason')
                                            ->label('Motivazione del rifiuto')
                                            ->rows(3)
                                            ->placeholder('Indica la motivazione giuridica del rifiuto (es. Art. 12 par. 5 — richiesta manifestamente infondata)...')
                                            ->visible(fn(Get $get) => $get('status') === 'rejected')
                                            ->columnSpanFull(),
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}

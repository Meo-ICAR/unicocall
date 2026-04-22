<?php

namespace App\Filament\Admin\Resources\Companies\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class CompanyForm
{
    public static function configure(Schema $schema): Schema
    {
        $isSuperAdmin = Auth::check() && Auth::user()->is_super_admin;

        return $schema
            ->components([
                Tabs::make('CompanyTabs')
                    ->tabs([
                        // Informazioni Generali Tab
                        Tabs\Tab::make('Informazioni Generali')
                            ->icon('heroicon-o-building-office')
                            ->schema([
                                Section::make('Dati Aziendali')
                                    ->description("Informazioni principali dell'azienda")
                                    ->schema([
                                        TextInput::make('name')
                                            ->label('Nome Azienda')
                                            ->required()
                                            ->maxLength(255)
                                            ->placeholder('Es. Hassisto Srl')
                                            ->helperText("Nome legale dell'azienda"),
                                        TextInput::make('vat_number')
                                            ->label('Partita IVA')
                                            ->maxLength(20)
                                            ->placeholder('Es. 09006331210')
                                            ->unique(ignorable: fn($record) => $record)
                                            ->helperText("Partita IVA dell'azienda (11 o 16 caratteri)"),
                                        Select::make('company_type')
                                            ->label('Tipo Azienda')
                                            ->options([
                                                'mediatore' => 'Mediatore',
                                                'call center' => 'Call Center',
                                                'hotel' => 'Hotel',
                                                'sw house' => 'Software House',
                                            ])
                                            ->default('sw house')
                                            ->required()
                                            ->disabled(!$isSuperAdmin)
                                            ->helperText("Tipo di attività principale dell'azienda"),
                                        Toggle::make('is_iso27001_certified')
                                            ->label('Certificato ISO 27001')
                                            ->helperText("Indica se l'azienda è certificata secondo lo standard ISO 27001")
                                            ->inline(false),
                                    ])
                                    ->columns(2),
                                Section::make('Contatti Principali')
                                    ->description("Informazioni di contatto dell'azienda")
                                    ->schema([
                                        TextInput::make('contact_email')
                                            ->label('Email Contatto')
                                            ->email()
                                            ->placeholder('es. info@azienda.com')
                                            ->helperText('Email principale per contatti aziendali'),
                                        TextInput::make('dpo_email')
                                            ->label('Email DPO')
                                            ->email()
                                            ->placeholder('es. dpo@azienda.com')
                                            ->helperText('Email del Data Protection Officer per GDPR'),
                                    ])
                                    ->columns(2),
                            ]),
                        // Personalizzazione Tab
                        Tabs\Tab::make('Personalizzazione')
                            ->icon('heroicon-o-paint-brush')
                            ->schema([
                                Section::make('Aspetto Grafico')
                                    ->description("Personalizzazione dell'interfaccia web")
                                    ->schema([
                                        Textarea::make('page_header')
                                            ->label('Intestazione Pagina')
                                            ->rows(3)
                                            ->placeholder("Inserisci testo per l'intestazione delle pagine...")
                                            ->helperText("Testo visualizzato nell'intestazione delle pagine web")
                                            ->columnSpanFull(),
                                        Textarea::make('page_footer')
                                            ->label('Piè di pagina')
                                            ->rows(3)
                                            ->placeholder('Inserisci testo per il piè di pagina...')
                                            ->helperText('Testo visualizzato nel piè di pagina delle pagine web')
                                            ->columnSpanFull(),
                                    ])
                                    ->columns(1),
                            ]),
                        // Configurazione Email Tab (solo per superadmin)
                        Tabs\Tab::make('Configurazione Email')
                            ->icon('heroicon-o-envelope')
                            ->visible($isSuperAdmin)
                            ->schema([
                                Section::make('Impostazioni SMTP')
                                    ->description('Configurazione del server SMTP per invio email')
                                    ->schema([
                                        TextInput::make('smtp_host')
                                            ->label('Host SMTP')
                                            ->placeholder('Es. smtp.gmail.com')
                                            ->helperText('Server SMTP per invio email'),
                                        TextInput::make('smtp_port')
                                            ->label('Porta SMTP')
                                            ->numeric()
                                            ->default(587)
                                            ->placeholder('Es. 587')
                                            ->helperText('Porta del server SMTP (solitamente 587 per TLS, 465 per SSL)'),
                                        TextInput::make('smtp_encryption')
                                            ->label('Crittografia SMTP')
                                            ->placeholder('Es. tls')
                                            ->helperText('Tipo di crittografia (tls, ssl, o lascia vuoto per nessuna)'),
                                        Toggle::make('smtp_enabled')
                                            ->label('Abilita SMTP')
                                            ->helperText('Abilita invio email tramite configurazione SMTP')
                                            ->inline(false),
                                        Toggle::make('smtp_verify_ssl')
                                            ->label('Verifica SSL SMTP')
                                            ->helperText('Verifica certificato SSL del server SMTP')
                                            ->inline(false),
                                    ])
                                    ->columns(2),
                            ]),
                        // Pagamenti Tab (solo per superadmin)
                        Tabs\Tab::make('Pagamenti')
                            ->icon('heroicon-o-banknotes')
                            ->visible($isSuperAdmin)
                            ->schema([
                                Section::make('Configurazione Pagamenti')
                                    ->description('Impostazioni per i pagamenti del servizio')
                                    ->schema([
                                        TextInput::make('payment_frequency')
                                            ->label('Frequenza Pagamento')
                                            ->placeholder('Es. mensile')
                                            ->helperText('Frequenza dei pagamenti (mensile, annuale, trimestrale, etc.)'),
                                        TextInput::make('payment')
                                            ->label('Importo Pagamento')
                                            ->numeric()
                                            ->prefix('EUR')
                                            ->placeholder('Es. 2500')
                                            ->step('0.01')
                                            ->helperText('Importo del pagamento periodico'),
                                        TextInput::make('payment_startup')
                                            ->label('Costo Attivazione')
                                            ->numeric()
                                            ->prefix('EUR')
                                            ->placeholder('Es. 500')
                                            ->step('0.01')
                                            ->helperText('Costo una tantum per attivazione servizio'),
                                    ])
                                    ->columns(3),
                                Section::make('Stato Pagamenti')
                                    ->description('Informazioni sullo stato dei pagamenti')
                                    ->schema([
                                        DateTimePicker::make('payment_last_date')
                                            ->label('Data Ultimo Pagamento')
                                            ->disabled(true)
                                            ->helperText("Data dell'ultimo pagamento effettuato (sola lettura)")
                                            ->placeholder('Nessun pagamento registrato'),
                                    ])
                                    ->columns(1),
                            ]),
                        // Avanzato Tab (solo per superadmin)
                        Tabs\Tab::make('Avanzato')
                            ->icon('heroicon-o-cog-6-tooth')
                            ->visible($isSuperAdmin)
                            ->schema([
                                Section::make('Impostazioni Avanzate')
                                    ->description('Configurazioni avanzate del sistema')
                                    ->schema([
                                        TextInput::make('sponsor')
                                            ->label('Sponsor')
                                            ->placeholder('Es. Azienda Madre Srl')
                                            ->helperText('Nome dello sponsor o azienda madre'),
                                        Select::make('user_id')
                                            ->relationship('companyAdminUser', 'name')
                                            ->label('Utente Admin')
                                            ->placeholder('Seleziona utente')
                                            ->helperText('Utente admin di questa azienda')
                                            ->searchable()
                                            ->preload(),
                                    ])
                                    ->columns(2),
                                Section::make('Note Interne')
                                    ->description("Note e commenti interni sull'azienda")
                                    ->schema([
                                        Textarea::make('notes')
                                            ->label('Note Interne')
                                            ->rows(4)
                                            ->placeholder("Inserisci note interne sull'azienda...")
                                            ->helperText('Note visibili solo agli amministratori')
                                            ->columnSpanFull(),
                                    ])
                                    ->columns(1),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}

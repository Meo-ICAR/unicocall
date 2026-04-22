<?php

namespace App\Filament\Admin\Resources\Employees\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class EmployeeForm
{
    public static function configure(Schema $schema): Schema
    {
        $isSuperAdmin = Auth::check() && Auth::user()->is_super_admin;

        return $schema
            ->components([
                Tabs::make('EmployeeTabs')
                    ->tabs([
                        // Anagrafica Tab
                        Tabs\Tab::make('Anagrafica')
                            ->icon('heroicon-o-user')
                            ->schema([
                                Section::make('Informazioni Personali')
                                    ->description('Dati anagrafici del dipendente')
                                    ->schema([
                                        TextInput::make('first_name')
                                            ->label('Nome')
                                            ->required()
                                            ->maxLength(255)
                                            ->placeholder('Es. Mario'),
                                        TextInput::make('last_name')
                                            ->label('Cognome')
                                            ->required()
                                            ->maxLength(255)
                                            ->placeholder('Es. Rossi'),
                                        TextInput::make('fiscal_code')
                                            ->label('Codice Fiscale')
                                            ->maxLength(16)
                                            ->placeholder('Es. RSSMRA85T10A562S')
                                            ->helperText('16 caratteri alfanumerici'),
                                        TextInput::make('phone')
                                            ->label('Telefono')
                                            ->tel()
                                            ->placeholder('Es. +39 333 1234567'),
                                        TextInput::make('email')
                                            ->label('Email')
                                            ->email()
                                            ->placeholder('Es. mario.rossi@azienda.com'),
                                    ])
                                    ->columns(2),
                                Section::make('Dati di Nascita')
                                    ->description('Informazioni sulla nascita del dipendente')
                                    ->schema([
                                        DatePicker::make('birth_date')
                                            ->label('Data di Nascita')
                                            ->placeholder('Seleziona data di nascita'),
                                        TextInput::make('birth_place')
                                            ->label('Luogo di Nascita')
                                            ->maxLength(255)
                                            ->placeholder('Es. Milano'),
                                        TextInput::make('birth_province')
                                            ->label('Provincia di Nascita')
                                            ->maxLength(2)
                                            ->placeholder('Es. MI'),
                                        Select::make('gender')
                                            ->label('Genere')
                                            ->options([
                                                'M' => 'Maschio',
                                                'F' => 'Femmina',
                                                'Altro' => 'Altro',
                                            ])
                                            ->placeholder('Seleziona genere'),
                                    ])
                                    ->columns(2),
                                Section::make('Indirizzo')
                                    ->description('Indirizzo di residenza del dipendente')
                                    ->schema([
                                        TextInput::make('address')
                                            ->label('Indirizzo')
                                            ->maxLength(255)
                                            ->placeholder('Es. Via Roma 1'),
                                        TextInput::make('city')
                                            ->label('Città')
                                            ->maxLength(255)
                                            ->placeholder('Es. Milano'),
                                        TextInput::make('province')
                                            ->label('Provincia')
                                            ->maxLength(2)
                                            ->placeholder('Es. MI'),
                                        TextInput::make('postal_code')
                                            ->label('CAP')
                                            ->maxLength(5)
                                            ->placeholder('Es. 20121'),
                                        TextInput::make('country')
                                            ->label('Paese')
                                            ->maxLength(255)
                                            ->default('Italia')
                                            ->placeholder('Es. Italia'),
                                    ])
                                    ->columns(2),
                            ]),
                        // Lavorativo Tab
                        Tabs\Tab::make('Lavorativo')
                            ->icon('heroicon-o-briefcase')
                            ->schema([
                                Section::make('Informazioni Lavorative')
                                    ->description('Dettagli sulla posizione lavorativa')
                                    ->schema([
                                        TextInput::make('role')
                                            ->label('Ruolo')
                                            ->required()
                                            ->maxLength(255)
                                            ->placeholder('Es. Sviluppatore PHP'),
                                        TextInput::make('department')
                                            ->label('Dipartimento')
                                            ->maxLength(255)
                                            ->placeholder('Es. Sviluppo'),
                                        Select::make('employment_type')
                                            ->label('Tipo di Impiego')
                                            ->options([
                                                'full_time' => 'Tempo Pieno',
                                                'part_time' => 'Tempo Parziale',
                                                'contract' => 'Contratto a Progetto',
                                                'internship' => 'Tirocinio',
                                                'apprenticeship' => 'Apprendistato',
                                            ])
                                            ->placeholder('Seleziona tipo di impiego'),
                                        TextInput::make('salary')
                                            ->label('Stipendio')
                                            ->numeric()
                                            ->prefix('EUR')
                                            ->placeholder('Es. 2500')
                                            ->helperText('Stipendio mensile lordo'),
                                    ])
                                    ->columns(2),
                                Section::make('Date Lavorative')
                                    ->description('Periodo di impiego')
                                    ->schema([
                                        DatePicker::make('hiring_date')
                                            ->label('Data Assunzione')
                                            ->required()
                                            ->placeholder('Seleziona data di assunzione'),
                                        DatePicker::make('termination_date')
                                            ->label('Data Cessazione')
                                            ->placeholder('Seleziona data di cessazione (se applicabile)')
                                            ->helperText('Lasciare vuoto se ancora attivo'),
                                    ])
                                    ->columns(2),
                                Section::make('Gerarchia')
                                    ->description('Struttura organizzativa')
                                    ->schema([
                                        Select::make('company_branch_id')
                                            ->label('Sede di Lavoro')
                                            ->relationship('branch', 'name')
                                            ->placeholder('Seleziona sede')
                                            ->helperText('Sede dove il dipendente lavora'),
                                        Select::make('coordinated_by_id')
                                            ->label('Supervisore Diretto')
                                            ->relationship('supervisor', 'full_name')
                                            ->placeholder('Seleziona supervisore')
                                            ->helperText('Manager a cui riporta il dipendente'),
                                        Toggle::make('is_supervisor')
                                            ->label('È Supervisore')
                                            ->helperText('Se selezionato, questo dipendente può supervisionare altri dipendenti'),
                                    ])
                                    ->columns(2),
                            ]),
                        // Privacy Tab
                        Tabs\Tab::make('Privacy')
                            ->icon('heroicon-o-shield-check')
                            ->schema([
                                Section::make('Conformità GDPR')
                                    ->description('Gestione della privacy e dati personali')
                                    ->schema([
                                        Toggle::make('is_structure')
                                            ->label('Personale Struttura')
                                            ->helperText("Indica se è personale di struttura dell'azienda"),
                                        Toggle::make('is_ghost')
                                            ->label('Personale Fantasma')
                                            ->helperText('Indica se è un dipendente fantasma per scopi contabili'),
                                        Textarea::make('privacy_role')
                                            ->label('Ruolo Privacy')
                                            ->rows(3)
                                            ->placeholder('Descrizione del ruolo ai fini della privacy')
                                            ->helperText('Descrizione delle attività di trattamento dati'),
                                    ])
                                    ->columns(1),
                                Section::make('Trattamento Dati')
                                    ->description('Dettagli sul trattamento dei dati personali')
                                    ->schema([
                                        Textarea::make('purpose')
                                            ->label('Finalità del Trattamento')
                                            ->rows(3)
                                            ->placeholder('Finalità per cui i dati vengono trattati')
                                            ->columnSpanFull(),
                                        Textarea::make('data_subjects')
                                            ->label('Interessati')
                                            ->rows(3)
                                            ->placeholder('Categorie di interessati al trattamento')
                                            ->columnSpanFull(),
                                        Textarea::make('data_categories')
                                            ->label('Categorie di Dati')
                                            ->rows(3)
                                            ->placeholder('Categorie di dati personali trattati')
                                            ->columnSpanFull(),
                                        TextInput::make('retention_period')
                                            ->label('Periodo di Conservazione')
                                            ->placeholder('Es. 10 anni')
                                            ->helperText('Periodo di conservazione dei dati'),
                                        Textarea::make('security_measures')
                                            ->label('Misure di Sicurezza')
                                            ->rows(3)
                                            ->placeholder('Misure di sicurezza implementate')
                                            ->columnSpanFull(),
                                    ])
                                    ->columns(1),
                            ]),
                        // Avanzato Tab (solo per superadmin)
                        Tabs\Tab::make('Avanzato')
                            ->icon('heroicon-o-cog')
                            ->visible($isSuperAdmin)
                            ->schema([
                                Section::make('Impostazioni Sistema')
                                    ->description('Impostazioni avanzate (solo per amministratori)')
                                    ->schema([
                                        TextInput::make('employee_id')
                                            ->label('ID Dipendente')
                                            ->maxLength(50)
                                            ->placeholder('ID univoco dipendente')
                                            ->helperText('Identificativo univoco del dipendente'),
                                        Select::make('user_id')
                                            ->label('Utente Sistema')
                                            ->relationship('user', 'email')
                                            ->placeholder('Seleziona utente sistema')
                                            ->helperText('Utente del sistema associato al dipendente'),
                                        TextInput::make('nationality')
                                            ->label('Nazionalità')
                                            ->maxLength(255)
                                            ->placeholder('Es. Italiana'),
                                        Select::make('locale')
                                            ->label('Lingua')
                                            ->options([
                                                'it' => 'Italiano',
                                                'en' => 'English',
                                                'fr' => 'Français',
                                                'de' => 'Deutsch',
                                            ])
                                            ->default('it')
                                            ->placeholder('Seleziona lingua'),
                                        TextInput::make('timezone')
                                            ->label('Fuso Orario')
                                            ->maxLength(50)
                                            ->default('Europe/Rome')
                                            ->placeholder('Es. Europe/Rome'),
                                    ])
                                    ->columns(2),
                                Section::make('Note Interne')
                                    ->description('Note e commenti interni')
                                    ->schema([
                                        Textarea::make('notes')
                                            ->label('Note Interne')
                                            ->rows(5)
                                            ->placeholder('Note interne sul dipendente')
                                            ->columnSpanFull(),
                                    ])
                                    ->columns(1),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}

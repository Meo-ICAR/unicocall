<?php

namespace App\Filament\Admin\Resources\Companies\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class CompanyForm
{
    public static function configure(Schema $schema): Schema
    {
        $isSuperAdmin = Auth::check() && Auth::user()->is_super_admin;

        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nome Azienda')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('Es. Hassisto Srl'),
                TextInput::make('vat_number')
                    ->label('Partita IVA')
                    ->maxLength(20)
                    ->placeholder('Es. 09006331210')
                    ->unique(ignorable: fn($record) => $record),
                Select::make('company_type')
                    ->label('Tipo Azienda')
                    ->options([
                        'mediatore' => 'Mediatore',
                        'call center' => 'Call center',
                        'hotel' => 'Hotel',
                        'sw house' => 'Sw house',
                    ])
                    ->default('sw house')
                    ->required()
                    ->disabled(!$isSuperAdmin),
                Toggle::make('is_iso27001_certified')
                    ->label('Certificato ISO 27001')
                    ->helperText("Indica se l'azienda è certificata secondo lo standard ISO 27001"),
                TextInput::make('contact_email')
                    ->label('Email Contatto')
                    ->email()
                    ->helperText('Email principale per contatti aziendali'),
                TextInput::make('dpo_email')
                    ->label('Email DPO')
                    ->email()
                    ->helperText('Email del Data Protection Officer'),
                Textarea::make('page_header')
                    ->label('Intestazione Pagina')
                    ->columnSpanFull()
                    ->rows(3)
                    ->helperText("Testo visualizzato nell'intestazione delle pagine"),
                Textarea::make('page_footer')
                    ->label('Piè di pagina')
                    ->columnSpanFull()
                    ->rows(3)
                    ->helperText('Testo visualizzato nel piè di pagina'),
                TextInput::make('sponsor')
                    ->label('Sponsor')
                    ->helperText('Nome dello sponsor o azienda madre')
                    ->hidden(!$isSuperAdmin),
                TextInput::make('user_id')
                    ->label('ID Utente Referente')
                    ->helperText("ID dell'utente di riferimento per questa azienda")
                    ->numeric()
                    ->hidden(!$isSuperAdmin)
                    ->dehydrated(false),
                TextInput::make('smtp_host')
                    ->label('Host SMTP')
                    ->helperText('Server SMTP per invio email')
                    ->hidden(!$isSuperAdmin),
                TextInput::make('smtp_port')
                    ->label('Porta SMTP')
                    ->helperText('Porta del server SMTP')
                    ->numeric()
                    ->default(587)
                    ->hidden(!$isSuperAdmin),
                TextInput::make('smtp_encryption')
                    ->label('Crittografia SMTP')
                    ->helperText('Tipo di crittografia (TLS/SSL)')
                    ->placeholder('Es. tls')
                    ->hidden(!$isSuperAdmin),
                Toggle::make('smtp_enabled')
                    ->label('Abilita SMTP')
                    ->helperText('Abilita invio email tramite SMTP')
                    ->hidden(!$isSuperAdmin),
                Toggle::make('smtp_verify_ssl')
                    ->label('Verifica SSL SMTP')
                    ->helperText('Verifica certificato SSL del server SMTP')
                    ->hidden(!$isSuperAdmin),
                TextInput::make('payment_frequency')
                    ->label('Frequenza Pagamento')
                    ->helperText('Frequenza dei pagamenti (mensile, annuale, etc.)')
                    ->placeholder('Es. mensile')
                    ->hidden(!$isSuperAdmin),
                TextInput::make('payment')
                    ->label('Importo Pagamento')
                    ->helperText('Importo del pagamento')
                    ->numeric()
                    ->prefix('EUR')
                    ->hidden(!$isSuperAdmin),
                DateTimePicker::make('payment_last_date')
                    ->label('Data Ultimo Pagamento')
                    ->helperText("Data dell'ultimo pagamento effettuato")
                    ->disabled(true)
                    ->hidden(!$isSuperAdmin),
                TextInput::make('payment_startup')
                    ->label('Costo Attivazione')
                    ->helperText('Costo una tantum per attivazione servizio')
                    ->numeric()
                    ->prefix('EUR')
                    ->hidden(!$isSuperAdmin),
            ]);
    }
}

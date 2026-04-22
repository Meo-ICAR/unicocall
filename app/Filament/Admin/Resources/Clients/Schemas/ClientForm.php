<?php

namespace App\Filament\Admin\Resources\Clients\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;

class ClientForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('ClientDetails')
                    ->tabs([
                        Tabs\Tab::make('Dati Anagrafici')
                            ->schema([
                                TextInput::make('name')
                                    ->required(),
                                Toggle::make('is_person')->default(true)->live(),
                                TextInput::make('first_name')->visible(fn(Get $get): bool => $get('is_person'))->required(),
                                TextInput::make('tax_code')->visible(fn(Get $get): bool => $get('is_person'))->required(),
                                TextInput::make('vat_number')->visible(fn(Get $get): bool => !$get('is_person'))->required(),
                                Select::make('client_type_id')
                                    ->relationship('clientType', 'name'),
                                TextInput::make('email')
                                    ->label('Email address')
                                    ->email(),
                                TextInput::make('phone')
                                    ->tel(),
                                Select::make('leadsource_id')
                                    ->relationship('leadsource', 'name'),
                                TextInput::make('status')
                                    ->required()
                                    ->default('raccolta_dati'),
                                DateTimePicker::make('acquired_at'),
                                DateTimePicker::make('contract_signed_at'),
                            ])
                            ->columns(2),
                        Tabs\Tab::make('Privacy & Consensi')
                            ->schema([
                                Toggle::make('privacy_consent')->required(),
                                DateTimePicker::make('general_consent_at'),
                                DateTimePicker::make('privacy_policy_read_at'),
                                DateTimePicker::make('consent_special_categories_at'),
                                DateTimePicker::make('consent_sic_at'),
                                DateTimePicker::make('consent_marketing_at'),
                                DateTimePicker::make('consent_profiling_at'),
                                TextInput::make('privacy_contact_email')->email(),
                                TextInput::make('dpo_email')->email(),
                                TextInput::make('privacy_role'),
                                Textarea::make('purpose')->columnSpanFull(),
                                Textarea::make('data_subjects')->columnSpanFull(),
                                Textarea::make('data_categories')->columnSpanFull(),
                                TextInput::make('retention_period'),
                                TextInput::make('extra_eu_transfer'),
                                Textarea::make('security_measures')->columnSpanFull(),
                            ])
                            ->columns(2),
                        Tabs\Tab::make('Documenti')
                            ->schema([
                                SpatieMediaLibraryFileUpload::make('documents')
                                    ->collection('documents')
                                    ->multiple()
                                    ->reorderable()
                                    ->openable()
                                    ->columnSpanFull(),
                            ]),
                        Tabs\Tab::make('Avanzate & Audit')
                            ->schema([
                                Toggle::make('is_company_consultant')->default(false),
                                Toggle::make('is_lead')->default(false),
                                Toggle::make('is_structure')->default(false),
                                Toggle::make('is_regulatory')->default(false),
                                Toggle::make('is_ghost')->default(false),
                                Toggle::make('is_sales')->default(true),
                                Toggle::make('is_pep')->default(false),
                                Toggle::make('is_sanctioned')->default(false),
                                Toggle::make('is_remote_interaction')->default(false),
                                Toggle::make('is_requiredApprovation')->default(false),
                                Toggle::make('is_approved')->default(true),
                                Toggle::make('is_anonymous')->default(false),
                                Toggle::make('is_client')->default(true),
                                Toggle::make('is_consultant_gdpr')->default(false),
                                Toggle::make('is_art108')->default(false),
                                DateTimePicker::make('blacklist_at'),
                                TextInput::make('blacklisted_by'),
                                TextInput::make('salary')->numeric(),
                                TextInput::make('salary_quote')->numeric(),
                            ])
                            ->columns(3),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}

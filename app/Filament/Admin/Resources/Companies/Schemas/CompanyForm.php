<?php

namespace App\Filament\Admin\Resources\Companies\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class CompanyForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('vat_number'),
                TextInput::make('sponsor'),
                Select::make('company_type')
                    ->options([
            'mediatore' => 'Mediatore',
            'call center' => 'Call center',
            'hotel' => 'Hotel',
            'sw house' => 'Sw house',
        ]),
                Toggle::make('is_iso27001_certified')
                    ->required(),
                TextInput::make('contact_email')
                    ->email(),
                TextInput::make('dpo_email')
                    ->email(),
                Textarea::make('page_header')
                    ->columnSpanFull(),
                Textarea::make('page_footer')
                    ->columnSpanFull(),
                TextInput::make('user_id')
                    ->required()
                    ->numeric(),
                TextInput::make('smtp_host'),
                TextInput::make('smtp_port')
                    ->numeric(),
                TextInput::make('smtp_encryption'),
                Toggle::make('smtp_enabled')
                    ->required(),
                Toggle::make('smtp_verify_ssl')
                    ->required(),
                TextInput::make('payment_frequency')
                    ->numeric(),
                TextInput::make('payment')
                    ->numeric(),
                DateTimePicker::make('payment_last_date'),
                TextInput::make('payment_startup')
                    ->numeric(),
            ]);
    }
}

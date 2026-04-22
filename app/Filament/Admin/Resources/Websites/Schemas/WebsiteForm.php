<?php

namespace App\Filament\Admin\Resources\Websites\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class WebsiteForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('type'),
                TextInput::make('clienti_id')
                    ->numeric(),
                Toggle::make('is_active')
                    ->required(),
                TextInput::make('domain')
                    ->required(),
                Toggle::make('is_typical')
                    ->required(),
                DatePicker::make('privacy_date'),
                DatePicker::make('transparency_date'),
                DatePicker::make('privacy_prior_date'),
                DatePicker::make('transparency_prior_date'),
                TextInput::make('url_privacy'),
                TextInput::make('url_cookies'),
                Toggle::make('is_footercompilant')
                    ->required(),
                TextInput::make('url_transparency'),
                Toggle::make('is_iso27001_certified')
                    ->required(),
                TextInput::make('websiteable_type'),
                TextInput::make('websiteable_id'),
            ]);
    }
}

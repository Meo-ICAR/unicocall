<?php

namespace App\Filament\Admin\Resources\SoftwareApplications\Schemas;

use App\Models\SoftwareApplication;
use Filament\Schemas\Schema;
use Filament\Forms\Components;
use Illuminate\Database\Eloquent\Builder;

class SoftwareApplicationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Components\TextInput::make('name')
                    ->label('Nome Applicazione')
                    ->required()
                    ->maxLength(255),
                    
                Components\TextInput::make('provider_name')
                    ->label('Provider')
                    ->required()
                    ->maxLength(255),
                    
                Components\Select::make('software_category_id')
                    ->label('Categoria')
                    ->relationship('softwareCategory', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                    
                Components\TextInput::make('website_url')
                    ->label('Sito Web')
                    ->url()
                    ->maxLength(500),
                    
                Components\TextInput::make('api_url')
                    ->label('URL API')
                    ->url()
                    ->maxLength(500),
                    
                Components\Textarea::make('api_parameters')
                    ->label('Parametri API')
                    ->rows(3),
                    
                Components\Toggle::make('is_cloud')
                    ->label('Servizio Cloud')
                    ->default(true),
                    
                Components\TextInput::make('apikey')
                    ->label('API Key')
                    ->password()
                    ->maxLength(255),
                    
                Components\TextInput::make('wallet_balance')
                    ->label('Saldo Wallet')
                    ->numeric()
                    ->step(0.01)
                    ->prefix('€'),
            ]);
    }
}

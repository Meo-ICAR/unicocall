<?php

namespace App\Filament\Admin\Resources\DataBreaches\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class DataBreachForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                DateTimePicker::make('discovered_at')
                    ->label('Data scoperta')
                    ->required(),
                DateTimePicker::make('occurred_at')
                    ->label('Data occorrenza')
                    ->required(),
                Textarea::make('description')
                    ->label('Descrizione')
                    ->required()
                    ->columnSpanFull(),
                Textarea::make('nature_of_breach')
                    ->label('Natura violazione')
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('approximate_records_count')
                    ->label('Numero record approssimativo')
                    ->required()
                    ->numeric(),
                Toggle::make('is_notifiable_to_authority')
                    ->label("Notificabile all'autorità")
                    ->required(),
                Toggle::make('is_notifiable_to_subjects')
                    ->label('Notificabile agli interessati')
                    ->required(),
                Textarea::make('mitigation_actions')
                    ->label('Azioni di mitigazione')
                    ->required()
                    ->columnSpanFull(),
            ]);
    }
}

<?php

namespace App\Filament\Admin\Resources\RegistroTrattamentis\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms;

class RegistroTrattamentiForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\TextInput::make('name')
                    ->label('Nome Registro Trattamenti')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('Es. Registro Trattamenti Dati Personali 2025')
                    ->columnSpanFull(),
                Forms\Components\DateTimePicker::make('approved_at')
                    ->label('Data Approvazione')
                    ->displayFormat('d/m/Y H:i')
                    ->placeholder('Seleziona data e ora di approvazione')
                    ->helperText('Lascia vuoto se non ancora approvato')
                    ->columnSpanFull(),
            ]);
    }
}

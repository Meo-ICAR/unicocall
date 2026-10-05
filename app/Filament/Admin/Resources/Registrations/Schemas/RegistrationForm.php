<?php

namespace App\Filament\Admin\Resources\Registrations\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class RegistrationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->default('IVA'),
                TextInput::make('registrable_type')
                    ->required(),
                TextInput::make('registrable_id')
                    ->required(),
                TextInput::make('code'),
                TextInput::make('code_internal'),
                TextInput::make('description'),
                DateTimePicker::make('start_at'),
                DateTimePicker::make('end_at'),
                TextInput::make('reason'),
            ]);
    }
}

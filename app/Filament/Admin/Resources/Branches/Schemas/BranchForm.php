<?php

namespace App\Filament\Admin\Resources\Branches\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class BranchForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('branch_type'),
                TextInput::make('branch_id'),
                Toggle::make('is_main_office')
                    ->required(),
                TextInput::make('manager_first_name'),
                TextInput::make('manager_last_name'),
                TextInput::make('manager_tax_code'),
            ]);
    }
}

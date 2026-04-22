<?php

namespace App\Filament\Resources\COMPILANCE\SoftwareCategories\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class SoftwareCategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name'),
                TextInput::make('code'),
                TextInput::make('description'),
            ]);
    }
}

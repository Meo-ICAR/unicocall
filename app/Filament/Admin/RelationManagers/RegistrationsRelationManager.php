<?php

namespace App\Filament\Admin\RelationManagers;

use App\Filament\Admin\Resources\Registrations\Schemas\RegistrationForm;
use App\Filament\Admin\Resources\Registrations\Tables\RegistrationsTable;
use App\Models\Registration;
use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Actions;
use Illuminate\Database\Eloquent\Builder;

class RegistrationsRelationManager extends RelationManager
{
    protected static string $relationship = 'registrations';
    protected static ?string $title = 'Registrazioni';
    protected static ?string $model = Registration::class;

    public function form(Schema $schema): Schema
    {
        return RegistrationForm::configure($schema);
    }

    public function table(Table $table): Table
    {
        return RegistrationsTable::configure($table)
            ->headerActions([
                Actions\CreateAction::make()
                    ->label('Nuova Registrazione'),
            ]);
    }
}

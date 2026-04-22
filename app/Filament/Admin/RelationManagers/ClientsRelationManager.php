<?php

namespace App\Filament\Admin\RelationManagers;

use App\Filament\Admin\Resources\Clients\Schemas\ClientForm;
use App\Filament\Admin\Resources\Clients\Tables\ClientsTable;
use App\Models\Client;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Actions;
use Illuminate\Database\Eloquent\Builder;

class ClientsRelationManager extends RelationManager
{
    protected static string $relationship = 'clients';
    protected static ?string $title = 'Clienti';
    protected static ?string $model = Client::class;

    public function form(Schema $schema): Schema
    {
        return ClientForm::configure($schema);
    }

    public function table(Table $table): Table
    {
        return ClientsTable::configure($table)
            ->headerActions([
                Actions\CreateAction::make()
                    ->label('Nuovo Cliente'),
            ]);
    }
}

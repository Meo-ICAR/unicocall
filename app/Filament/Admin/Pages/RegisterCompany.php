<?php

namespace App\Filament\Admin\Pages;

use App\Models\Company;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Tenancy\RegisterTenant;
use Filament\Schemas\Schema;

class RegisterCompany extends RegisterTenant
{
    public static function getLabel(): string
    {
        return 'Registra Azienda';
    }

    public static function canAccess(): bool
    {
        // Only show registration if user doesn't have any companies
        return auth()->user()->companies()->doesntExist();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nome Azienda')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('Es. Hassisto Srl'),
                TextInput::make('vat_number')
                    ->label('Partita IVA')
                    ->maxLength(20)
                    ->placeholder('Es. 09006331210')
                    ->unique(Company::class, 'vat_number', ignoreRecord: true),
            ]);
    }

    protected function handleRegistration(array $data): Company
    {
        $company = Company::create(array_merge($data, [
            'company_type' => 'sw house',  // Default company type
            'user_id' => auth()->id(),  // Set the current user as the company user
        ]));

        // Attach the current user as admin
        $company->users()->attach(auth()->user()->id, ['role' => 'admin']);

        return $company;
    }
}

<?php

namespace App\Filament\Admin\Resources\Companies;

use App\Filament\Admin\RelationManagers\AddressesRelationManager;
use App\Filament\Admin\RelationManagers\BranchesRelationManager;
use App\Filament\Admin\RelationManagers\ClientsRelationManager;
use App\Filament\Admin\RelationManagers\EmployeesRelationManager;
use App\Filament\Admin\RelationManagers\RegistrationsRelationManager;
use App\Filament\Admin\RelationManagers\RegistroTrattamentiItemRelationManager;
use App\Filament\Admin\Resources\Companies\Pages\CreateCompany;
use App\Filament\Admin\Resources\Companies\Pages\EditCompany;
use App\Filament\Admin\Resources\Companies\Pages\ListCompanies;
use App\Filament\Admin\Resources\Companies\Schemas\CompanyForm;
use App\Filament\Admin\Resources\Companies\Tables\CompaniesTable;
use App\Models\Company;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use BackedEnum;

class CompanyResource extends Resource
{
    protected static ?string $model = Company::class;

    protected static bool $isScopedToTenant = false;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return CompanyForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CompaniesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            SoftwareApplicationsRelationManager::class,  // applicazioni software
            WebsitesRelationManager::class,  // siti web
            BranchesRelationManager::class,  // sedi
            EmployeesRelationManager::class,  // incaricati del trattamento
            ClientsRelationManager::class,  //   responsabili del trattamento
            RegistroTrattamentiItemRelationManager::class,  // registro trattamenti
            RegistrationsRelationManager::class,  // altri codici es OAM
            AddressesRelationManager::class,  // indirizzi
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCompanies::route('/'),
            'create' => CreateCompany::route('/create'),
            'edit' => EditCompany::route('/{record}/edit'),
        ];
    }
}

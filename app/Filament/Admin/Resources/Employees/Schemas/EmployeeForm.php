<?php

namespace App\Filament\Admin\Resources\Employees\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class EmployeeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('company_id'),
                TextInput::make('name'),
                TextInput::make('role'),
                TextInput::make('cf'),
                TextInput::make('phone')
                    ->tel(),
                TextInput::make('department'),
                DatePicker::make('hiring_date'),
                DatePicker::make('termination_date'),
                TextInput::make('company_branch_id')
                    ->numeric(),
                TextInput::make('coordinated_by_id')
                    ->numeric(),
                TextInput::make('employee_types')
                    ->required()
                    ->default('dipendente'),
                TextInput::make('supervisor_type')
                    ->required()
                    ->default('no'),
                TextInput::make('privacy_role'),
                Textarea::make('purpose')
                    ->columnSpanFull(),
                Textarea::make('data_subjects')
                    ->columnSpanFull(),
                Textarea::make('data_categories')
                    ->columnSpanFull(),
                TextInput::make('retention_period'),
                TextInput::make('extra_eu_transfer'),
                Textarea::make('security_measures')
                    ->columnSpanFull(),
                TextInput::make('privacy_data'),
                Toggle::make('is_structure')
                    ->required(),
                Toggle::make('is_ghost')
                    ->required(),
                TextInput::make('employee_type'),
                TextInput::make('employee_id'),
                TextInput::make('user_id')
                    ->numeric(),
                TextInput::make('created_by')
                    ->numeric(),
                TextInput::make('updated_by')
                    ->numeric(),
                TextInput::make('deleted_by')
                    ->numeric(),
            ]);
    }
}

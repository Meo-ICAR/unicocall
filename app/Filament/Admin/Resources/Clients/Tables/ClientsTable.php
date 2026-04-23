<?php

namespace App\Filament\Admin\Resources\Clients\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ClientsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('name')
            ->columns([
                TextColumn::make('company.name')
                    ->searchable(),
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('first_name')
                    ->searchable(),
                TextColumn::make('tax_code')
                    ->searchable(),
                TextColumn::make('vat_number')
                    ->searchable(),
                TextColumn::make('email')
                    ->label('Email address')
                    ->searchable(),
                TextColumn::make('leadsource.name')
                    ->label('Lead Source')
                    ->searchable(),
                TextColumn::make('servizio')
                    ->label('Servizio')
                    ->searchable(),
                TextColumn::make('nomina')
                    ->label('Nomina')
                    ->searchable(),
                TextColumn::make('nomina_at')
                    ->label('Data Nomina')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('dpo_email')
                    ->searchable(),
                TextColumn::make('privacy_policy_url')
                    ->searchable(),
                TextColumn::make('contract_signed_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('phone')
                    ->searchable(),
                IconColumn::make('is_person')
                    ->boolean(),
                IconColumn::make('is_company_consultant')
                    ->boolean(),
                IconColumn::make('is_lead')
                    ->boolean(),
                TextColumn::make('status')
                    ->searchable(),
                TextColumn::make('leadsource.name')
                    ->searchable(),
                TextColumn::make('acquired_at')
                    ->dateTime()
                    ->sortable(),
                IconColumn::make('is_structure')
                    ->boolean(),
                IconColumn::make('is_regulatory')
                    ->boolean(),
                IconColumn::make('is_ghost')
                    ->boolean(),
                IconColumn::make('is_sales')
                    ->boolean(),
                IconColumn::make('is_pep')
                    ->boolean(),
                IconColumn::make('is_sanctioned')
                    ->boolean(),
                IconColumn::make('is_remote_interaction')
                    ->boolean(),
                IconColumn::make('is_requiredApprovation')
                    ->boolean(),
                IconColumn::make('is_approved')
                    ->boolean(),
                IconColumn::make('is_anonymous')
                    ->boolean(),
                IconColumn::make('is_client')
                    ->boolean(),
                TextColumn::make('general_consent_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('privacy_policy_read_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('consent_special_categories_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('consent_sic_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('consent_marketing_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('consent_profiling_at')
                    ->dateTime()
                    ->sortable(),
                IconColumn::make('is_consultant_gdpr')
                    ->boolean(),
                TextColumn::make('privacy_contact_email')
                    ->searchable(),
                TextColumn::make('privacy_role')
                    ->searchable(),
                TextColumn::make('retention_period')
                    ->searchable(),
                TextColumn::make('extra_eu_transfer')
                    ->searchable(),
                TextColumn::make('privacy_data')
                    ->searchable(),
                TextColumn::make('clientType.name')
                    ->searchable(),
                IconColumn::make('privacy_consent')
                    ->boolean(),
                TextColumn::make('blacklist_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('blacklisted_by')
                    ->searchable(),
                TextColumn::make('salary')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('salary_quote')
                    ->numeric()
                    ->sortable(),
                IconColumn::make('is_art108')
                    ->boolean(),
                TextColumn::make('user_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('created_by')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('updated_by')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('deleted_by')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}

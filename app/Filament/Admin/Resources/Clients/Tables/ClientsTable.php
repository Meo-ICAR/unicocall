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
                TextColumn::make('name')
                    ->label('Nome')
                    ->searchable(),
                TextColumn::make('first_name')
                    ->label('Nome')
                    ->searchable(),
                TextColumn::make('tax_code')
                    ->label('Codice fiscale')
                    ->searchable(),
                TextColumn::make('vat_number')
                    ->label('Partita IVA')
                    ->searchable(),
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),
                TextColumn::make('leadsource.name')
                    ->label('Origine lead')
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
                    ->label('Email DPO')
                    ->searchable(),
                TextColumn::make('privacy_policy_url')
                    ->label('URL privacy')
                    ->searchable(),
                TextColumn::make('contract_signed_at')
                    ->label('Firma contratto')
                    ->dateTime('d/m/Y')
                    ->sortable(),
                TextColumn::make('phone')
                    ->label('Telefono')
                    ->searchable(),
                IconColumn::make('is_person')
                    ->label('Persona')
                    ->boolean(),
                IconColumn::make('is_company_consultant')
                    ->label('Consulente')
                    ->boolean(),
                IconColumn::make('is_lead')
                    ->label('Lead')
                    ->boolean(),
                TextColumn::make('status')
                    ->label('Stato')
                    ->searchable(),
                TextColumn::make('leadsource.name')
                    ->label('Origine')
                    ->searchable(),
                TextColumn::make('acquired_at')
                    ->label('Acquisizione')
                    ->dateTime('d/m/Y')
                    ->sortable(),
                IconColumn::make('is_structure')
                    ->label('Struttura')
                    ->boolean(),
                IconColumn::make('is_regulatory')
                    ->label('Regolamentare')
                    ->boolean(),
                IconColumn::make('is_ghost')
                    ->label('Ghost')
                    ->boolean(),
                IconColumn::make('is_sales')
                    ->label('Vendite')
                    ->boolean(),
                IconColumn::make('is_pep')
                    ->label('PEP')
                    ->boolean(),
                IconColumn::make('is_sanctioned')
                    ->label('Sanzionato')
                    ->boolean(),
                IconColumn::make('is_remote_interaction')
                    ->label('Remoto')
                    ->boolean(),
                IconColumn::make('is_requiredApprovation')
                    ->label('Req. approv.')
                    ->boolean(),
                IconColumn::make('is_approved')
                    ->label('Approvato')
                    ->boolean(),
                IconColumn::make('is_anonymous')
                    ->label('Anonimo')
                    ->boolean(),
                IconColumn::make('is_client')
                    ->label('Cliente')
                    ->boolean(),
                TextColumn::make('general_consent_at')
                    ->label('Consenso generale')
                    ->dateTime('d/m/Y')
                    ->sortable(),
                TextColumn::make('privacy_policy_read_at')
                    ->label('Lettura privacy')
                    ->dateTime('d/m/Y')
                    ->sortable(),
                TextColumn::make('consent_special_categories_at')
                    ->label('Consenso categorie speciali')
                    ->dateTime('d/m/Y')
                    ->sortable(),
                TextColumn::make('consent_sic_at')
                    ->label('Consenso SIC')
                    ->dateTime('d/m/Y')
                    ->sortable(),
                TextColumn::make('consent_marketing_at')
                    ->label('Consenso marketing')
                    ->dateTime('d/m/Y')
                    ->sortable(),
                TextColumn::make('consent_profiling_at')
                    ->label('Consenso profilazione')
                    ->dateTime('d/m/Y')
                    ->sortable(),
                IconColumn::make('is_consultant_gdpr')
                    ->label('Consulente GDPR')
                    ->boolean(),
                TextColumn::make('privacy_contact_email')
                    ->label('Email contatto privacy')
                    ->searchable(),
                TextColumn::make('privacy_role')
                    ->label('Ruolo privacy')
                    ->searchable(),
                TextColumn::make('retention_period')
                    ->label('Periodo conservazione')
                    ->searchable(),
                TextColumn::make('extra_eu_transfer')
                    ->label('Trasferimento extra-UE')
                    ->searchable(),
                TextColumn::make('privacy_data')
                    ->label('Dati privacy')
                    ->searchable(),
                TextColumn::make('clientType.name')
                    ->label('Tipo cliente')
                    ->searchable(),
                IconColumn::make('privacy_consent')
                    ->label('Consenso privacy')
                    ->boolean(),
                TextColumn::make('blacklist_at')
                    ->label('Data blacklist')
                    ->dateTime('d/m/Y')
                    ->sortable(),
                TextColumn::make('blacklisted_by')
                    ->label('Blacklist da')
                    ->searchable(),
                TextColumn::make('salary')
                    ->label('Stipendio')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('salary_quote')
                    ->label('Quota stipendio')
                    ->numeric()
                    ->sortable(),
                IconColumn::make('is_art108')
                    ->label('Art. 108')
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
                    ->label('Creato')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Aggiornato')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deleted_at')
                    ->label('Eliminato')
                    ->dateTime('d/m/Y H:i')
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

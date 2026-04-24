<?php

namespace App\Filament\Admin\RelationManagers;

use App\Models\Client;
use App\Models\Company;
use App\Models\Subappalti;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Actions;
use Filament\Forms;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;

class SubappaltiClientToClientRelationManager extends RelationManager
{
    protected static string $relationship = 'subappaltiClientToClient';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informazioni Subappalto Client-to-Client')
                    ->description('Dettagli del subappalto tra clienti')
                    ->components([
                        Forms\Components\Select::make('sub_id')
                            ->label('Cliente Destinatario')
                            ->relationship('sub', 'name')
                            ->options(function () {
                                // Ottieni il client corrente dal contesto del RelationManager
                                $currentClient = $this->getOwnerRecord();
                                $currentCompanyId = $currentClient?->company_id;

                                if (!$currentCompanyId) {
                                    return [];
                                }

                                return Client::where('company_id', $currentCompanyId)
                                    ->where('is_company', true)
                                    ->where('id', '!=', $currentClient?->id)
                                    ->get()
                                    ->mapWithKeys(function ($client) {
                                        $clientType = $client->client_type?->name ?? 'Nessun tipo';
                                        $displayName = "{$client->name} ({$clientType})";
                                        return [$client->id => $displayName];
                                    })
                                    ->toArray();
                            })
                            ->searchable()
                            ->preload()
                            ->required()
                            ->placeholder('Seleziona il cliente destinatario della stessa azienda')
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('name')
                            ->label('Nome Subappalto')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Es. Condivisione dati tra clienti'),
                        Forms\Components\TextInput::make('role')
                            ->label('Ruolo')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Es. Intermediario, Subappaltatore, Partner'),
                        Forms\Components\TextInput::make('servizio')
                            ->label('Servizio')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Es. Intermediazione dati, Servizi condivisi, etc.'),
                    ])
                    ->columns(3),
                Section::make('Dati Trattati e Istruzioni')
                    ->description('Categorie dati e istruzioni operative per subappalto client-to-client')
                    ->components([
                        Forms\Components\Select::make('categoria_dati')
                            ->label('Categorie Dati Condivise')
                            ->multiple()
                            ->options([
                                'dati_anagrafici' => 'Dati Anagrafici Base',
                                'dati_contatto' => 'Dati di Contatto',
                                'dati_finanziari' => 'Dati Finanziari',
                                'dati_contrattuali' => 'Dati Contrattuali',
                                'dati_commerciali' => 'Dati Commerciali',
                                'dati_tecnici' => 'Dati Tecnici',
                                'dati_logistici' => 'Dati Logistici',
                                'dati_marketing' => 'Dati Marketing',
                                'dati_analitici' => 'Dati Analitici',
                                'dati_comportamentali' => 'Dati Comportamentali',
                                'dati_geolocalizzazione' => 'Dati Geolocalizzazione',
                                'dati_demografici' => 'Dati Demografici',
                            ])
                            ->placeholder('Seleziona le categorie di dati condivise tra clienti')
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('istruzioni')
                            ->label('Istruzioni Operative')
                            ->rows(3)
                            ->placeholder('Inserisci le istruzioni operative per la condivisione dati tra clienti')
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('description')
                            ->label('Descrizione Dettagliata')
                            ->rows(2)
                            ->placeholder('Descrizione completa del subappalto e delle attività di condivisione dati')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                Section::make('Nomina e Compliance')
                    ->description('Informazioni di nomina e conformità GDPR per condivisione dati')
                    ->components([
                        Forms\Components\Select::make('nomina')
                            ->label('Tipo Nomina')
                            ->options(Subappalti::getNominations())
                            ->placeholder('Seleziona il tipo di nomina per la condivisione dati')
                            ->columnSpanFull(),
                        Forms\Components\DateTimePicker::make('nomina_at')
                            ->label('Data e Ora Nomina')
                            ->displayFormat('d/m/Y H:i')
                            ->placeholder('Seleziona data e ora della nomina')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->modifyQueryUsing(fn(Builder $query) => $query
                ->where('originator_type', 'client')
                ->where('sub_type', 'client'))
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nome Subappalto')
                    ->searchable()
                    ->sortable()
                    ->limit(50)
                    ->tooltip(fn($record) => $record->name),
                Tables\Columns\TextColumn::make('role')
                    ->label('Ruolo')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color(fn($record) => match ($record->role) {
                        'Intermediario' => 'info',
                        'Subappaltatore' => 'success',
                        'Partner' => 'warning',
                        'Mandatario' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('servizio')
                    ->label('Servizio')
                    ->searchable()
                    ->limit(40)
                    ->tooltip(fn($record) => $record->servizio),
                Tables\Columns\TextColumn::make('sub.name')
                    ->label('Cliente Destinatario')
                    ->searchable()
                    ->sortable()
                    ->getStateUsing(fn($record) => $record->sub?->name ?? 'N/A'),
                Tables\Columns\TextColumn::make('categorie_dati_list')
                    ->label('Categorie Dati')
                    ->limit(30)
                    ->tooltip(fn($record) => $record->categorie_dati_list),
                Tables\Columns\TextColumn::make('nomina')
                    ->label('Nomina')
                    ->searchable()
                    ->badge()
                    ->color(fn($record) => match ($record->nomina) {
                        'DPO' => 'danger',
                        'Amministratore di Sistema' => 'warning',
                        'Responsabile del Trattamento' => 'info',
                        'Incaricato del Trattamento' => 'success',
                        default => 'gray',
                    }),
                Tables\Columns\IconColumn::make('has_nomination')
                    ->label('Nominato')
                    ->boolean()
                    ->trueIcon('fas-check-circle')
                    ->falseIcon('fas-times-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
                Tables\Columns\TextColumn::make('formatted_nomina_at')
                    ->label('Data Nomina')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creato il')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('role')
                    ->label('Ruolo')
                    ->options([
                        'Intermediario' => 'Intermediario',
                        'Subappaltatore' => 'Subappaltatore',
                        'Partner' => 'Partner',
                        'Mandatario' => 'Mandatario',
                        'Consulente' => 'Consulente',
                    ]),
                Tables\Filters\SelectFilter::make('nomina')
                    ->label('Tipo Nomina')
                    ->options(Subappalti::getNominations()),
                Tables\Filters\Filter::make('has_nomination')
                    ->label('Con Nomina')
                    ->query(fn(Builder $query): Builder => $query->whereNotNull('nomina_at'))
                    ->toggle(),
                Tables\Filters\Filter::make('without_nomination')
                    ->label('Senza Nomina')
                    ->query(fn(Builder $query): Builder => $query->whereNull('nomina_at'))
                    ->toggle(),
                Tables\Filters\Filter::make('has_data_categories')
                    ->label('Con Categorie Dati')
                    ->query(fn(Builder $query): Builder => $query->whereNotNull('categoria_dati'))
                    ->toggle(),
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label('Dal')
                            ->placeholder('Seleziona data inizio'),
                        Forms\Components\DatePicker::make('created_until')
                            ->label('Al')
                            ->placeholder('Seleziona data fine'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
            ])
            ->headerActions([
                Actions\CreateAction::make()
                    ->label('Nuovo Subappalto Client-to-Client')
                    ->icon('fas-plus')
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['originator_type'] = 'client';
                        $data['sub_type'] = 'App\Models\Client';
                        return $data;
                    }),
            ])
            ->actions([
                Actions\ViewAction::make()
                    ->label('Visualizza')
                    ->icon('fas-eye'),
                Actions\EditAction::make()
                    ->label('Modifica')
                    ->icon('fas-edit'),
                Actions\DeleteAction::make()
                    ->label('Elimina')
                    ->icon('fas-trash')
                    ->requiresConfirmation()
                    ->modalHeading('Conferma Eliminazione')
                    ->modalDescription('Sei sicuro di voler eliminare questo subappalto client-to-client? Questa azione è irreversibile.')
                    ->modalSubmitActionLabel('Sì, elimina')
                    ->modalCancelActionLabel('Annulla'),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make()
                        ->label('Elimina Selezionati')
                        ->icon('fas-trash')
                        ->requiresConfirmation()
                        ->modalHeading('Conferma Eliminazione Multipla')
                        ->modalDescription('Sei sicuro di voler eliminare i subappalti client-to-client selezionati? Questa azione è irreversibile.')
                        ->modalSubmitActionLabel('Sì, elimina')
                        ->modalCancelActionLabel('Annulla'),
                ]),
            ])
            ->emptyStateActions([
                Actions\CreateAction::make()
                    ->label('Crea Primo Subappalto Client-to-Client')
                    ->icon('fas-plus')
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['originator_type'] = 'client';
                        $data['sub_type'] = 'App\Models\Client';
                        return $data;
                    }),
            ])
            ->emptyStateDescription('Nessun subappalto tra clienti trovato. Crea il primo subappalto per iniziare.')
            ->emptyStateHeading('Nessun Subappalto Client-to-Client');
    }
}

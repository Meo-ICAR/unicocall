<?php

namespace App\Filament\Admin\RelationManagers;

use App\Models\Subappalti;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Actions;
use Filament\Forms;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;

class SubappaltiDipendentiRelationManager extends RelationManager
{
    protected static string $relationship = 'subappaltiDipendenti';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informazioni Subappalto Dipendente')
                    ->description('Dettagli del subappalto con dipendenti')
                    ->components([
                        Forms\Components\TextInput::make('name')
                            ->label('Nome Subappalto')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Es. Gestione dati dipendenti'),
                        Forms\Components\TextInput::make('role')
                            ->label('Ruolo')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Es. HR Manager, Amministratore, etc.'),
                        Forms\Components\TextInput::make('servizio')
                            ->label('Servizio')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Es. Amministrazione personale, payroll, etc.'),
                    ])
                    ->columns(3),
                Section::make('Dati Trattati e Istruzioni')
                    ->description('Categorie dati e istruzioni operative per dipendenti')
                    ->components([
                        Forms\Components\Select::make('categoria_dati')
                            ->label('Categorie Dati Dipendenti')
                            ->multiple()
                            ->options([
                                'dati_anagrafici' => 'Dati Anagrafici (nome, cognome, CF)',
                                'dati_contatto' => 'Dati di Contatto (email, telefono, indirizzo)',
                                'dati_professionali' => 'Dati Professionali (qualifica, mansioni)',
                                'dati_finanziari' => 'Dati Finanziari (stipendio, IBAN)',
                                'dati_sanitari' => 'Dati Sanitari (certificati medici)',
                                'dati_giudiziari' => 'Dati Giudiziari (casellario)',
                                'dati_biometrici' => 'Dati Biometrici (impronte, riconoscimento)',
                                'dati_presenze' => 'Dati Presenze (timbrature, assenze)',
                                'dati_valutazione' => 'Dati Valutazione (performance, KPI)',
                                'dati_formazione' => 'Dati Formazione (corsi, certificazioni)',
                                'dati_familiari' => 'Dati Familiari (coniuge, figli)',
                                'dati_emergenza' => 'Dati Emergenza (contatti, patologie)',
                            ])
                            ->placeholder('Seleziona le categorie di dati trattate')
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('istruzioni')
                            ->label('Istruzioni Operative')
                            ->rows(3)
                            ->placeholder('Inserisci le istruzioni operative per il trattamento dei dati dipendenti')
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('description')
                            ->label('Descrizione Dettagliata')
                            ->rows(2)
                            ->placeholder('Descrizione completa del subappalto e delle attività svolte con i dati dipendenti')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                Section::make('Nomina e Compliance')
                    ->description('Informazioni di nomina e conformità GDPR')
                    ->components([
                        Forms\Components\Select::make('nomina')
                            ->label('Tipo Nomina')
                            ->options(Subappalti::getNominations())
                            ->placeholder('Seleziona il tipo di nomina per il trattamento dati dipendenti')
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
                ->where('originator_type', 'company')
                ->where('sub_type', 'employee'))
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
                        'HR Manager' => 'info',
                        'Amministratore' => 'success',
                        'Payroll Specialist' => 'warning',
                        'Responsabile HR' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('servizio')
                    ->label('Servizio')
                    ->searchable()
                    ->limit(40)
                    ->tooltip(fn($record) => $record->servizio),
                Tables\Columns\TextColumn::make('sub.name')
                    ->label('Dipendente')
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
                        'HR Manager' => 'HR Manager',
                        'Amministratore' => 'Amministratore',
                        'Payroll Specialist' => 'Payroll Specialist',
                        'Responsabile HR' => 'Responsabile HR',
                        'Consulente del Lavoro' => 'Consulente del Lavoro',
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
                    ->label('Nuovo Subappalto Dipendente')
                    ->icon('fas-plus')
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['originator_type'] = 'company';
                        $data['sub_type'] = 'employee';
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
                    ->modalDescription('Sei sicuro di voler eliminare questo subappalto dipendente? Questa azione è irreversibile.')
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
                        ->modalDescription('Sei sicuro di voler eliminare i subappalti dipendenti selezionati? Questa azione è irreversibile.')
                        ->modalSubmitActionLabel('Sì, elimina')
                        ->modalCancelActionLabel('Annulla'),
                ]),
            ])
            ->emptyStateActions([
                Actions\CreateAction::make()
                    ->label('Crea Primo Subappalto Dipendente')
                    ->icon('fas-plus')
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['originator_type'] = 'company';
                        $data['sub_type'] = 'employee';
                        return $data;
                    }),
            ])
            ->emptyStateDescription('Nessun subappalto con dipendenti trovato. Crea il primo subappalto per iniziare.')
            ->emptyStateHeading('Nessun Subappalto Dipendente');
    }
}

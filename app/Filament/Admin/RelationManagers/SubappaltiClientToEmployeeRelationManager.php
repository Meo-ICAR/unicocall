<?php

namespace App\Filament\Admin\RelationManagers;

use App\Models\Employee;
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

class SubappaltiClientToEmployeeRelationManager extends RelationManager
{
    protected static string $relationship = 'subappaltiClientToEmployee';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informazioni Subappalto Client-to-Employee')
                    ->description('Dettagli del subappalto tra cliente e dipendenti')
                    ->components([
                        Forms\Components\Select::make('sub_id')
                            ->label('Dipendente')
                            ->relationship('sub', 'name')
                            ->options(function () {
                                return Employee::with('company')
                                    ->get()
                                    ->mapWithKeys(function ($employee) {
                                        $companyName = $employee->company?->name ?? 'Nessuna azienda';
                                        $displayName = "{$employee->name} ({$companyName})";
                                        return [$employee->id => $displayName];
                                    })
                                    ->toArray();
                            })
                            ->searchable()
                            ->preload()
                            ->required()
                            ->placeholder('Seleziona il dipendente da associare al cliente')
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('name')
                            ->label('Nome Subappalto')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Es. Gestione dipendenti per cliente'),
                        Forms\Components\TextInput::make('role')
                            ->label('Ruolo')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Es. HR Esterno, Consulente, Formatore'),
                        Forms\Components\TextInput::make('servizio')
                            ->label('Servizio')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Es. Somministrazione personale, Formazione, etc.'),
                    ])
                    ->columns(3),
                Section::make('Dati Trattati e Istruzioni')
                    ->description('Categorie dati e istruzioni operative per gestione dipendenti cliente')
                    ->components([
                        Forms\Components\Select::make('categoria_dati')
                            ->label('Categorie Dati Dipendenti Cliente')
                            ->multiple()
                            ->options([
                                'dati_anagrafici' => 'Dati Anagrafici (nome, cognome, CF)',
                                'dati_contatto' => 'Dati di Contatto (email, telefono, indirizzo)',
                                'dati_professionali' => 'Dati Professionali (qualifica, mansioni, contratto)',
                                'dati_finanziari' => 'Dati Finanziari (stipendio, IBAN, benefit)',
                                'dati_orari' => 'Dati Orari (turni, presenze, assenze)',
                                'dati_valutazione' => 'Dati Valutazione (performance, KPI, feedback)',
                                'dati_formazione' => 'Dati Formazione (corsi, certificati, competenze)',
                                'dati_sanitari' => 'Dati Sanitari (visite mediche, infortuni)',
                                'dati_disciplinari' => 'Dati Disciplinari (richiami, provvedimenti)',
                                'dati_familiari' => 'Dati Familiari (coniuge, figli, emergenze)',
                                'dati_previdenziali' => 'Dati Previdenziali (INPS, fondi pensione)',
                                'dati_sicurezza' => 'Dati Sicurezza (DPI, formazione sicurezza)',
                            ])
                            ->placeholder('Seleziona le categorie di dati trattate')
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('istruzioni')
                            ->label('Istruzioni Operative')
                            ->rows(3)
                            ->placeholder('Inserisci le istruzioni operative per la gestione dei dipendenti del cliente')
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('description')
                            ->label('Descrizione Dettagliata')
                            ->rows(2)
                            ->placeholder('Descrizione completa del subappalto e delle attività di gestione dipendenti')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                Section::make('Nomina e Compliance')
                    ->description('Informazioni di nomina e conformità GDPR per gestione dipendenti')
                    ->components([
                        Forms\Components\Select::make('nomina')
                            ->label('Tipo Nomina')
                            ->options(Subappalti::getNominations())
                            ->placeholder('Seleziona il tipo di nomina per la gestione dati dipendenti')
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
                        'HR Esterno' => 'info',
                        'Consulente' => 'success',
                        'Formatore' => 'warning',
                        'Payroll Esterno' => 'danger',
                        'Responsabile HR' => 'primary',
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
                        'HR Esterno' => 'HR Esterno',
                        'Consulente' => 'Consulente',
                        'Formatore' => 'Formatore',
                        'Payroll Esterno' => 'Payroll Esterno',
                        'Responsabile HR' => 'Responsabile HR',
                        'Tutor Lavoro' => 'Tutor Lavoro',
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
                    ->label('Nuovo Subappalto Client-to-Employee')
                    ->icon('fas-plus')
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['originator_type'] = 'client';
                        $data['sub_type'] = 'App\Models\Employee';
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
                    ->modalDescription('Sei sicuro di voler eliminare questo subappalto client-to-employee? Questa azione è irreversibile.')
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
                        ->modalDescription('Sei sicuro di voler eliminare i subappalti client-to-employee selezionati? Questa azione è irreversibile.')
                        ->modalSubmitActionLabel('Sì, elimina')
                        ->modalCancelActionLabel('Annulla'),
                ]),
            ])
            ->emptyStateActions([
                Actions\CreateAction::make()
                    ->label('Crea Primo Subappalto Client-to-Employee')
                    ->icon('fas-plus')
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['originator_type'] = 'client';
                        $data['sub_type'] = 'App\Models\Employee';
                        return $data;
                    }),
            ])
            ->emptyStateDescription('Nessun subappalto tra cliente e dipendenti trovato. Crea il primo subappalto per iniziare.')
            ->emptyStateHeading('Nessun Subappalto Client-to-Employee');
    }
}

<?php

namespace App\Filament\Admin\RelationManagers;

use App\Models\PrivacyDataType;
use App\Models\PrivacyLegalBasis;
use App\Models\PrivacySecurity;
use App\Models\RegistroTrattamentiItem;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Actions;
use Filament\Forms;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RegistroTrattamentiItemRelationManager extends RelationManager
{
    protected static string $relationship = 'registroTrattamentiItems';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informazioni Trattamento')
                    ->description('Dettagli del trattamento dati GDPR')
                    ->components([
                        Forms\Components\Textarea::make('Attivita')
                            ->label('Attività di Trattamento')
                            ->required()
                            ->rows(3)
                            ->placeholder('Descrivi le attività di trattamento svolte')
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('Finalita')
                            ->label('Finalità del Trattamento')
                            ->required()
                            ->rows(3)
                            ->placeholder('Descrivi le finalità del trattamento')
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('Interessati')
                            ->label('Categorie di Interessati')
                            ->required()
                            ->rows(2)
                            ->placeholder('Es. Clienti, dipendenti, fornitori, etc.')
                            ->columnSpanFull(),
                        Select::make('Dati')
                            ->label('Categorie di Dati')
                            ->required()
                            ->multiple()
                            ->options(PrivacyDataType::getGroupedByCategory())
                            ->searchable()
                            ->getSearchResultsUsing(function (string $search) {
                                return PrivacyDataType::search($search)
                                    ->limit(50)
                                    ->pluck('name', 'slug')
                                    ->toArray();
                            })
                            ->getOptionLabelUsing(function (string $value) {
                                $dataType = PrivacyDataType::findBySlug($value);
                                return $dataType ? "[{$dataType->category_label}] {$dataType->name}" : $value;
                            })
                            ->placeholder('Seleziona le categorie di dati trattati')
                            ->helperText('Scegli tra i tipi di dati previsti dal GDPR')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                Section::make('Base Giuridica e Destinatari')
                    ->description('Informazioni legali e destinatari dei dati')
                    ->components([
                        Forms\Components\Select::make('Giuridica')
                            ->label('Base Giuridica')
                            ->required()
                            ->options(PrivacyLegalBasis::getOptions())
                            ->searchable()
                            ->getSearchResultsUsing(function (string $search) {
                                return PrivacyLegalBasis::where('name', 'like', "%{$search}%")
                                    ->orWhere('reference_article', 'like', "%{$search}%")
                                    ->limit(50)
                                    ->pluck('name', 'name')
                                    ->toArray();
                            })
                            ->getOptionLabelUsing(function (string $value) {
                                $basis = PrivacyLegalBasis::where('name', $value)->first();
                                return $basis ? "{$basis->name} - {$basis->reference_article}" : $value;
                            })
                            ->placeholder('Seleziona la base giuridica GDPR')
                            ->helperText('Scegli tra le basi legali previste dal GDPR')
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('Destinatari')
                            ->label('Destinatari dei Dati')
                            ->rows(2)
                            ->placeholder('Es. Amministratore di sistema, DPO, responsabili esterni, etc.')
                            ->columnSpanFull(),
                        Forms\Components\Toggle::make('extraEU')
                            ->label('Trasferimento Extra-UE')
                            ->helperText("Se selezionato, indica un trasferimento dati fuori dall'Unione Europea")
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                Section::make('Conservazione e Sicurezza')
                    ->description('Periodi di conservazione e misure di sicurezza')
                    ->components([
                        Forms\Components\Textarea::make('Conservazione')
                            ->label('Periodo di Conservazione')
                            ->required()
                            ->rows(2)
                            ->placeholder('Es. 10 anni, fino alla cessazione del rapporto, etc.')
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('Sicurezza')
                            ->label('Misure di Sicurezza')
                            ->required()
                            ->rows(3)
                            ->placeholder('Descrivi le misure tecniche e organizzative adottate')
                            ->helperText('Inserisci manualmente la descrizione dettagliata delle misure di sicurezza implementate')
                            ->columnSpanFull(),
                        Forms\Components\Select::make('misure_sicurezza_predefinite')
                            ->label('Misure di Sicurezza Predefinite')
                            ->multiple()
                            ->options(PrivacySecurity::all()->mapWithKeys(function ($measure) {
                                return [$measure->id => "[{$measure->getTypeLabel()}] {$measure->name}"];
                            })->toArray())
                            ->searchable()
                            ->getSearchResultsUsing(function (string $search) {
                                return PrivacySecurity::where('name', 'like', "%{$search}%")
                                    ->orWhere('description', 'like', "%{$search}%")
                                    ->limit(50)
                                    ->get()
                                    ->mapWithKeys(function ($measure) {
                                        return [$measure->id => "[{$measure->getTypeLabel()}] {$measure->name}"];
                                    })
                                    ->toArray();
                            })
                            ->getOptionLabelUsing(function ($value) {
                                $measure = PrivacySecurity::find($value);
                                return $measure ? "[{$measure->getTypeLabel()}] {$measure->name}" : $value;
                            })
                            ->placeholder('Seleziona misure predefinite (opzionale)')
                            ->helperText('Seleziona misure standard come riferimento o integrazione')
                            ->columnSpanFull(),
                    ])
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('Attivita')
            ->columns([
                Tables\Columns\TextColumn::make('Attivita')
                    ->label('Attività')
                    ->searchable()
                    ->limit(50)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        return $column->getState();
                    }),
                Tables\Columns\TextColumn::make('Finalita')
                    ->label('Finalità')
                    ->searchable()
                    ->limit(50)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        return $column->getState();
                    }),
                Tables\Columns\TextColumn::make('Interessati')
                    ->label('Interessati')
                    ->searchable()
                    ->limit(30),
                Tables\Columns\TextColumn::make('Dati')
                    ->label('Dati')
                    ->searchable()
                    ->limit(30),
                Tables\Columns\IconColumn::make('extraEU')
                    ->label('Extra-UE')
                    ->boolean()
                    ->trueIcon('fas-globe')
                    ->falseIcon('fas-lock')
                    ->trueColor('warning')
                    ->falseColor('success'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creato il')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Aggiornato il')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('extraEU')
                    ->label('Trasferimento Extra-UE')
                    ->options([
                        '1' => 'Sì',
                        '0' => 'No',
                    ]),
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
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['created_from'] ?? null) {
                            $indicators['created_from'] = 'Dal: ' . $data['created_from'];
                        }
                        if ($data['created_until'] ?? null) {
                            $indicators['created_until'] = 'Al: ' . $data['created_until'];
                        }
                        return $indicators;
                    }),
            ])
            ->headerActions([
                Actions\CreateAction::make()
                    ->label('Nuovo Trattamento')
                    ->icon('fas-plus'),
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
                    ->modalDescription('Sei sicuro di voler eliminare questo trattamento? Questa azione è irreversibile.')
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
                        ->modalDescription('Sei sicuro di voler eliminare i trattamenti selezionati? Questa azione è irreversibile.')
                        ->modalSubmitActionLabel('Sì, elimina')
                        ->modalCancelActionLabel('Annulla'),
                ]),
            ])
            ->emptyStateActions([
                Actions\CreateAction::make()
                    ->label('Crea Primo Trattamento')
                    ->icon('fas-plus'),
            ])
            ->emptyStateDescription('Nessun trattamento dati trovato. Crea il primo trattamento per iniziare.')
            ->emptyStateHeading('Nessun Trattamento Dati');
    }
}

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
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Actions;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;

class RegistroTrattamentiItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'registroTrattamentiItems';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Dettagli Trattamento')
                    ->description('Informazioni specifiche del trattamento dati')
                    ->components([
                        Forms\Components\Textarea::make('Attivita')
                            ->label('Attività di Trattamento')
                            ->required()
                            ->rows(2)
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('Finalita')
                            ->label('Finalità')
                            ->required()
                            ->rows(2)
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('Interessati')
                            ->label('Interessati')
                            ->required()
                            ->rows(1)
                            ->columnSpanFull(),
                        Select::make('Dati')
                            ->label('Categorie Dati')
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
                Section::make('Aspetti Legali')
                    ->description('Base giuridica e destinatari')
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
                            ->label('Destinatari')
                            ->rows(1)
                            ->columnSpanFull(),
                        Forms\Components\Toggle::make('extraEU')
                            ->label('Trasferimento Extra-UE')
                            ->helperText('Indica se ci sono trasferimenti dati fuori UE'),
                    ])
                    ->columns(2),
                Section::make('Conservazione e Sicurezza')
                    ->description('Periodi e misure di sicurezza')
                    ->components([
                        Forms\Components\Textarea::make('Conservazione')
                            ->label('Periodo Conservazione')
                            ->required()
                            ->rows(1)
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('Sicurezza')
                            ->label('Misure Sicurezza')
                            ->required()
                            ->rows(2)
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
                                        return [$measure->id => "[{$measure->getTypeLabel()}] {$measure->name}";
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
                    ->columns(2),
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
                    ->limit(40)
                    ->tooltip(fn($record) => $record->Attivita),
                Tables\Columns\TextColumn::make('Finalita')
                    ->label('Finalità')
                    ->searchable()
                    ->limit(40)
                    ->tooltip(fn($record) => $record->Finalita),
                Tables\Columns\TextColumn::make('Interessati')
                    ->label('Interessati')
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
                    ->label('Creato')
                    ->dateTime('d/m/Y')
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
            ])
            ->headerActions([
                Actions\CreateAction::make()
                    ->label('Nuovo Item')
                    ->icon('fas-plus'),
            ])
            ->actions([
                Actions\EditAction::make()
                    ->label('Modifica')
                    ->icon('fas-edit'),
                Actions\DeleteAction::make()
                    ->label('Elimina')
                    ->icon('fas-trash'),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}

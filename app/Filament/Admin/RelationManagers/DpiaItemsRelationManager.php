<?php

namespace App\Filament\Admin\RelationManagers;

use App\Models\DpiaItem;
use App\Models\DpiaImpact;
use App\Models\DpiaRisk;
use App\Models\PrivacySecurity;
use Filament\Actions;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class DpiaItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'dpiaItems';

    protected static ?string $title = 'Analisi dei Rischi';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Suggerimenti dal catalogo')
                    ->description('Usa questi valori come riferimento — non vengono salvati come ID')
                    ->collapsed()
                    ->schema([
                        Placeholder::make('risk_catalog')
                            ->label('Fonti di rischio disponibili')
                            ->content(function () {
                                $risks = DpiaRisk::query()->orderBy('name')->get();
                                if ($risks->isEmpty()) return 'Nessuna fonte nel catalogo.';
                                return $risks->map(fn($r) => "• {$r->name}" . ($r->description ? " — {$r->description}" : ''))->join("\n");
                            })
                            ->columnSpan(1),

                        Placeholder::make('impact_catalog')
                            ->label('Impatti disponibili')
                            ->content(function () {
                                $impacts = DpiaImpact::query()->orderBy('name')->get();
                                if ($impacts->isEmpty()) return 'Nessun impatto nel catalogo.';
                                return $impacts->map(fn($i) => "• {$i->name}" . ($i->description ? " — {$i->description}" : ''))->join("\n");
                            })
                            ->columnSpan(1),
                    ])
                    ->columns(2),

                Section::make('Identificazione del rischio')
                    ->columns(2)
                    ->schema([
                        TextInput::make('risk_source')
                            ->label('Fonte di rischio')
                            ->required()
                            ->placeholder('Es. Attacco hacker, Errore umano...')
                            ->datalist(DpiaItem::getRiskSourceOptions())
                            ->helperText('Digita liberamente o scegli dal suggerimento')
                            ->columnSpanFull(),

                        TextInput::make('potential_impact')
                            ->label('Impatto potenziale')
                            ->required()
                            ->placeholder('Es. Perdita di riservatezza, Danno reputazionale...')
                            ->datalist(DpiaItem::getPotentialImpactOptions())
                            ->helperText('Digita liberamente o scegli dal suggerimento')
                            ->columnSpanFull(),
                    ]),

                Section::make('Valutazione')
                    ->columns(3)
                    ->schema([
                        Select::make('probability')
                            ->label('Probabilità')
                            ->required()
                            ->options(DpiaItem::getProbabilityOptions())
                            ->native(false),

                        Select::make('severity')
                            ->label('Gravità')
                            ->required()
                            ->options(DpiaItem::getSeverityOptions())
                            ->native(false),

                        TextInput::make('inherent_risk_score')
                            ->label('Rischio inerente (P×G)')
                            ->numeric()
                            ->readOnly()
                            ->helperText('Calcolato automaticamente'),
                    ]),

                Section::make('Mitigazione')
                    ->columns(2)
                    ->schema([
                        Select::make('privacy_security_id')
                            ->label('Misura di mitigazione')
                            ->placeholder('Seleziona dal catalogo (opzionale)')
                            ->options(
                                PrivacySecurity::all()
                                    ->mapWithKeys(fn($m) => [$m->id => "[{$m->getTypeLabel()}] {$m->name}"])
                                    ->toArray()
                            )
                            ->searchable()
                            ->nullable()
                            ->helperText('Collega una misura di sicurezza esistente come riferimento'),

                        TextInput::make('residual_risk_score')
                            ->label('Rischio residuo')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(25)
                            ->helperText('Punteggio dopo applicazione della misura'),
                    ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('risk_source')
            ->columns([
                Tables\Columns\TextColumn::make('risk_source')
                    ->label('Fonte di rischio')
                    ->searchable()
                    ->limit(35)
                    ->tooltip(fn($record) => $record->risk_source),

                Tables\Columns\TextColumn::make('potential_impact')
                    ->label('Impatto')
                    ->searchable()
                    ->limit(35)
                    ->tooltip(fn($record) => $record->potential_impact),

                Tables\Columns\TextColumn::make('probability')
                    ->label('P')
                    ->formatStateUsing(fn($state) => DpiaItem::getProbabilityOptions()[$state] ?? $state)
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('severity')
                    ->label('G')
                    ->formatStateUsing(fn($state) => DpiaItem::getSeverityOptions()[$state] ?? $state)
                    ->badge()
                    ->color('warning'),

                Tables\Columns\TextColumn::make('inherent_risk_score')
                    ->label('Rischio inerente')
                    ->badge()
                    ->color(fn($record) => $record->risk_color),

                Tables\Columns\TextColumn::make('privacySecurity.name')
                    ->label('Misura')
                    ->limit(30)
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('residual_risk_score')
                    ->label('Rischio residuo')
                    ->badge()
                    ->color(fn($record) => $record->residual_risk_color),
            ])
            ->headerActions([
                Actions\CreateAction::make()
                    ->label('Aggiungi rischio')
                    ->mutateFormDataUsing(function (array $data): array {
                        // Auto-calcola rischio inerente se non impostato
                        if (empty($data['inherent_risk_score']) && !empty($data['probability']) && !empty($data['severity'])) {
                            $data['inherent_risk_score'] = $data['probability'] * $data['severity'];
                        }
                        if (empty($data['residual_risk_score'])) {
                            $data['residual_risk_score'] = $data['inherent_risk_score'] ?? 0;
                        }
                        return $data;
                    }),
            ])
            ->actions([
                Actions\EditAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        if (!empty($data['probability']) && !empty($data['severity'])) {
                            $data['inherent_risk_score'] = $data['probability'] * $data['severity'];
                        }
                        return $data;
                    }),
                Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}

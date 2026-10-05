<?php

namespace App\Filament\Admin\RelationManagers;

use App\Models\AuditFinding;
use App\Models\Remediation;
use Filament\Actions;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class AuditFindingsRelationManager extends RelationManager
{
    protected static string $relationship = 'findings';

    protected static ?string $title = 'Rilievi e Misure Correttive';

    public function form(Schema $schema): Schema
    {
        return $schema->components([

            Section::make('Rilievo')
                ->columns(2)
                ->schema([
                    TextInput::make('title')
                        ->label('Titolo del rilievo')
                        ->required()
                        ->maxLength(255)
                        ->columnSpanFull(),

                    Textarea::make('description')
                        ->label('Descrizione dell\'anomalia')
                        ->required()
                        ->rows(3)
                        ->columnSpanFull(),

                    Select::make('severity')
                        ->label('Gravità')
                        ->required()
                        ->options(AuditFinding::getSeverityOptions())
                        ->native(false)
                        ->default('minor'),

                    Select::make('status')
                        ->label('Stato')
                        ->required()
                        ->options(AuditFinding::getStatusOptions())
                        ->native(false)
                        ->default('open')
                        ->live(),
                ]),

            Section::make('Approfondimento')
                ->columns(2)
                ->schema([
                    Toggle::make('requires_investigation')
                        ->label('Richiede approfondimento')
                        ->default(false)
                        ->live()
                        ->columnSpanFull(),

                    Textarea::make('investigation_notes')
                        ->label('Note approfondimento')
                        ->rows(2)
                        ->visible(fn(Get $get) => $get('requires_investigation'))
                        ->columnSpanFull(),

                    DatePicker::make('investigation_deadline')
                        ->label('Scadenza approfondimento')
                        ->displayFormat('d/m/Y')
                        ->visible(fn(Get $get) => $get('requires_investigation')),
                ]),

            Section::make('Misura correttiva')
                ->columns(2)
                ->schema([
                    Toggle::make('requires_corrective_action')
                        ->label('Richiede misura correttiva')
                        ->default(true)
                        ->live()
                        ->columnSpanFull(),

                    // Suggerimento dal catalogo — mostra testo, NON salva l'id come FK obbligatoria
                    Select::make('remediation_id')
                        ->label('Misura suggerita dal catalogo')
                        ->placeholder('Cerca nel catalogo rimedi...')
                        ->searchable()
                        ->nullable()
                        ->visible(fn(Get $get) => $get('requires_corrective_action'))
                        ->options(function () {
                            return Remediation::query()
                                ->orderBy('remediation_type')
                                ->orderBy('name')
                                ->get()
                                ->mapWithKeys(fn($r) => [
                                    $r->id => "[{$r->remediation_type}] {$r->name}"
                                        . ($r->timeframe_desc ? " — {$r->timeframe_desc}" : ''),
                                ])
                                ->toArray();
                        })
                        ->live()
                        ->afterStateUpdated(function ($state, callable $set) {
                            if ($state) {
                                $rem = Remediation::find($state);
                                if ($rem) {
                                    // Pre-compila la descrizione con il testo del catalogo
                                    $set('corrective_action_description', $rem->description);
                                    // Pre-compila la scadenza se timeframe_hours disponibile
                                    if ($rem->timeframe_hours) {
                                        $set('corrective_action_deadline',
                                            now()->addHours($rem->timeframe_hours)->format('Y-m-d')
                                        );
                                    }
                                }
                            }
                        })
                        ->helperText('Seleziona per pre-compilare la descrizione — puoi modificarla liberamente')
                        ->columnSpanFull(),

                    // Mostra la descrizione del rimedio selezionato come riferimento
                    Placeholder::make('remediation_preview')
                        ->label('Descrizione dal catalogo')
                        ->content(function (Get $get) {
                            $id = $get('remediation_id');
                            if (!$id) return 'Seleziona un rimedio dal catalogo per vedere la descrizione.';
                            $rem = Remediation::find($id);
                            return $rem
                                ? "📋 {$rem->name}\n⏱ {$rem->timeframe_desc}\n\n{$rem->description}"
                                : '—';
                        })
                        ->visible(fn(Get $get) => $get('requires_corrective_action') && filled($get('remediation_id')))
                        ->columnSpanFull(),

                    Textarea::make('corrective_action_description')
                        ->label('Descrizione misura correttiva')
                        ->rows(4)
                        ->placeholder('Descrivi la misura correttiva da adottare...')
                        ->helperText('Testo libero — pre-compilato dal catalogo se selezionato sopra')
                        ->visible(fn(Get $get) => $get('requires_corrective_action'))
                        ->columnSpanFull(),

                    DatePicker::make('corrective_action_deadline')
                        ->label('Scadenza misura correttiva')
                        ->displayFormat('d/m/Y')
                        ->visible(fn(Get $get) => $get('requires_corrective_action')),
                ]),

            Section::make('Risoluzione')
                ->columns(2)
                ->visible(fn(Get $get) => in_array($get('status'), ['resolved', 'accepted_risk', 'closed']))
                ->schema([
                    DatePicker::make('resolved_at')
                        ->label('Data risoluzione')
                        ->displayFormat('d/m/Y'),

                    Textarea::make('resolution_notes')
                        ->label('Note di chiusura')
                        ->rows(3)
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->defaultSort('severity', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('severity')
                    ->label('Gravità')
                    ->badge()
                    ->formatStateUsing(fn($state) => AuditFinding::getSeverityOptions()[$state] ?? $state)
                    ->color(fn($state) => match ($state) {
                        'observation' => 'gray',
                        'minor'       => 'info',
                        'major'       => 'warning',
                        'critical'    => 'danger',
                        default       => 'gray',
                    }),

                Tables\Columns\TextColumn::make('title')
                    ->label('Rilievo')
                    ->searchable()
                    ->limit(45)
                    ->tooltip(fn($record) => $record->title),

                Tables\Columns\TextColumn::make('status')
                    ->label('Stato')
                    ->badge()
                    ->formatStateUsing(fn($state) => AuditFinding::getStatusOptions()[$state] ?? $state)
                    ->color(fn($state) => match ($state) {
                        'open'          => 'danger',
                        'in_progress'   => 'warning',
                        'resolved'      => 'success',
                        'accepted_risk' => 'gray',
                        'closed'        => 'success',
                        default         => 'gray',
                    }),

                Tables\Columns\TextColumn::make('remediation.name')
                    ->label('Rimedio catalogo')
                    ->limit(35)
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('corrective_action_deadline')
                    ->label('Scadenza')
                    ->date('d/m/Y')
                    ->color(fn($record) => $record->is_overdue ? 'danger' : null)
                    ->placeholder('—'),

                Tables\Columns\IconColumn::make('requires_investigation')
                    ->label('Approfond.')
                    ->boolean()
                    ->trueColor('warning')
                    ->falseColor('gray'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('severity')
                    ->label('Gravità')
                    ->options(AuditFinding::getSeverityOptions()),

                Tables\Filters\SelectFilter::make('status')
                    ->label('Stato')
                    ->options(AuditFinding::getStatusOptions()),
            ])
            ->headerActions([
                Actions\CreateAction::make()
                    ->label('Aggiungi rilievo')
                    ->mutateFormDataUsing(function (array $data): array {
                        if (function_exists('filament') && filament()->getTenant()) {
                            $data['company_id'] = filament()->getTenant()->id;
                        }
                        return $data;
                    }),
            ])
            ->actions([
                Actions\EditAction::make(),
                Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}

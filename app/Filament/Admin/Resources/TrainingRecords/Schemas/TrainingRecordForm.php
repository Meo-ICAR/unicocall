<?php

namespace App\Filament\Admin\Resources\TrainingRecords\Schemas;

use App\Models\Client;
use App\Models\Employee;
use App\Models\TrainingRecord;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class TrainingRecordForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Tabs::make('training_tabs')->tabs([

                Tab::make('Partecipante & Normativa')
                    ->icon('heroicon-o-user-group')
                    ->schema([
                        Section::make('Partecipante')
                            ->columns(2)
                            ->schema([
                                Select::make('trainable_type')
                                    ->label('Tipo partecipante')
                                    ->required()
                                    ->options(TrainingRecord::getTrainableTypeOptions())
                                    ->native(false)
                                    ->live()
                                    ->afterStateUpdated(fn(callable $set) => $set('trainable_id', null)),

                                Select::make('trainable_id')
                                    ->label('Partecipante')
                                    ->required()
                                    ->searchable()
                                    ->visible(fn(Get $get) => filled($get('trainable_type')))
                                    ->options(function (Get $get) {
                                        return match ($get('trainable_type')) {
                                            Employee::class => Employee::query()
                                                ->orderBy('name')
                                                ->pluck('name', 'id')
                                                ->toArray(),
                                            Client::class => Client::query()
                                                ->orderBy('name')
                                                ->get()
                                                ->mapWithKeys(fn($c) => [
                                                    $c->id => trim("{$c->first_name} {$c->name}"),
                                                ])
                                                ->toArray(),
                                            default => [],
                                        };
                                    })
                                    ->getSearchResultsUsing(function (string $search, Get $get) {
                                        return match ($get('trainable_type')) {
                                            Employee::class => Employee::query()
                                                ->where('name', 'like', "%{$search}%")
                                                ->limit(30)->pluck('name', 'id')->toArray(),
                                            Client::class => Client::query()
                                                ->where(fn($q) => $q
                                                    ->where('name', 'like', "%{$search}%")
                                                    ->orWhere('first_name', 'like', "%{$search}%")
                                                )
                                                ->limit(30)->get()
                                                ->mapWithKeys(fn($c) => [$c->id => trim("{$c->first_name} {$c->name}")])
                                                ->toArray(),
                                            default => [],
                                        };
                                    }),
                            ]),

                        Section::make('Quadro normativo')
                            ->schema([
                                Select::make('regulatory_framework')
                                    ->label('Normativa di riferimento')
                                    ->required()
                                    ->options(TrainingRecord::getRegulatoryFrameworkOptions())
                                    ->native(false)
                                    ->columnSpanFull(),
                            ]),
                    ]),

                Tab::make('Corso')
                    ->icon('heroicon-o-academic-cap')
                    ->schema([
                        Section::make('Dettagli corso')
                            ->columns(2)
                            ->schema([
                                TextInput::make('course_title')
                                    ->label('Titolo del corso')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Es. Formazione GDPR per operatori call center')
                                    ->columnSpanFull(),

                                Textarea::make('course_description')
                                    ->label('Descrizione / programma')
                                    ->rows(3)
                                    ->placeholder('Argomenti trattati, obiettivi formativi...')
                                    ->columnSpanFull(),

                                TextInput::make('provider')
                                    ->label('Ente erogatore')
                                    ->maxLength(255)
                                    ->placeholder('Es. OAM, IVASS, Ente di formazione...'),

                                TextInput::make('trainer')
                                    ->label('Docente / Formatore')
                                    ->maxLength(255)
                                    ->placeholder('Nome del docente'),

                                Select::make('delivery_mode')
                                    ->label('Modalità di erogazione')
                                    ->required()
                                    ->options(TrainingRecord::getDeliveryModeOptions())
                                    ->native(false)
                                    ->default('in_person'),

                                TextInput::make('hours')
                                    ->label('Ore di formazione')
                                    ->numeric()
                                    ->minValue(0)
                                    ->step(0.5)
                                    ->suffix('h')
                                    ->default(0),
                            ]),
                    ]),

                Tab::make('Date & Esito')
                    ->icon('heroicon-o-check-circle')
                    ->schema([
                        Section::make('Date')
                            ->columns(2)
                            ->schema([
                                DatePicker::make('training_date')
                                    ->label('Data formazione')
                                    ->required()
                                    ->displayFormat('d/m/Y')
                                    ->default(now()),

                                DatePicker::make('expiry_date')
                                    ->label('Scadenza validità')
                                    ->displayFormat('d/m/Y')
                                    ->nullable()
                                    ->helperText('Es. OAM: 30h ogni anno; IVASS: 15h ogni anno'),
                            ]),

                        Section::make('Esito')
                            ->columns(2)
                            ->schema([
                                Select::make('outcome')
                                    ->label('Esito')
                                    ->required()
                                    ->options(TrainingRecord::getOutcomeOptions())
                                    ->native(false)
                                    ->default('attended'),

                                TextInput::make('score')
                                    ->label('Punteggio / Voto')
                                    ->numeric()
                                    ->minValue(0)
                                    ->maxValue(100)
                                    ->nullable()
                                    ->placeholder('Es. 85'),

                                Toggle::make('certificate_issued')
                                    ->label('Attestato rilasciato')
                                    ->default(false)
                                    ->live(),

                                TextInput::make('certificate_number')
                                    ->label('Numero attestato')
                                    ->maxLength(100)
                                    ->nullable()
                                    ->visible(fn(Get $get) => $get('certificate_issued'))
                                    ->placeholder('Es. ATT-2026-001'),
                            ]),

                        Section::make('Note')
                            ->schema([
                                Textarea::make('notes')
                                    ->label('Note aggiuntive')
                                    ->rows(3)
                                    ->columnSpanFull(),
                            ]),
                    ]),

            ])->columnSpanFull(),
        ]);
    }

    /**
     * Schema ridotto per i RelationManager (senza selezione partecipante)
     */
    public static function configureForRelation(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Normativa & Corso')
                ->columns(2)
                ->schema([
                    Select::make('regulatory_framework')
                        ->label('Normativa di riferimento')
                        ->required()
                        ->options(TrainingRecord::getRegulatoryFrameworkOptions())
                        ->native(false)
                        ->columnSpanFull(),

                    TextInput::make('course_title')
                        ->label('Titolo del corso')
                        ->required()
                        ->maxLength(255)
                        ->columnSpanFull(),

                    TextInput::make('provider')
                        ->label('Ente erogatore')
                        ->maxLength(255),

                    TextInput::make('trainer')
                        ->label('Docente')
                        ->maxLength(255),

                    Select::make('delivery_mode')
                        ->label('Modalità')
                        ->required()
                        ->options(TrainingRecord::getDeliveryModeOptions())
                        ->native(false)
                        ->default('in_person'),

                    TextInput::make('hours')
                        ->label('Ore')
                        ->numeric()
                        ->step(0.5)
                        ->suffix('h')
                        ->default(0),
                ]),

            Section::make('Date & Esito')
                ->columns(2)
                ->schema([
                    DatePicker::make('training_date')
                        ->label('Data formazione')
                        ->required()
                        ->displayFormat('d/m/Y')
                        ->default(now()),

                    DatePicker::make('expiry_date')
                        ->label('Scadenza validità')
                        ->displayFormat('d/m/Y')
                        ->nullable(),

                    Select::make('outcome')
                        ->label('Esito')
                        ->required()
                        ->options(TrainingRecord::getOutcomeOptions())
                        ->native(false)
                        ->default('attended'),

                    TextInput::make('score')
                        ->label('Punteggio')
                        ->numeric()
                        ->nullable(),

                    Toggle::make('certificate_issued')
                        ->label('Attestato rilasciato')
                        ->default(false)
                        ->live(),

                    TextInput::make('certificate_number')
                        ->label('N° attestato')
                        ->nullable()
                        ->visible(fn(Get $get) => $get('certificate_issued')),

                    Textarea::make('notes')
                        ->label('Note')
                        ->rows(2)
                        ->columnSpanFull(),
                ]),
        ]);
    }
}

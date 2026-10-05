<?php

namespace App\Filament\Admin\Resources\Dpias\Schemas;

use App\Models\Dpia;
use App\Models\DpiaImpact;
use App\Models\DpiaItem;
use App\Models\DpiaRisk;
use App\Models\PrivacySecurity;
use App\Models\RegistroTrattamentiItem;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class DpiaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('dpia_tabs')
                    ->tabs([
                        Tab::make('Generale')
                            ->icon('heroicon-o-document-text')
                            ->schema([
                                Section::make('Identificazione')
                                    ->columns(2)
                                    ->schema([
                                        TextInput::make('name')
                                            ->label('Nome DPIA')
                                            ->required()
                                            ->maxLength(255)
                                            ->placeholder('Es. DPIA - Trattamento dati clienti call center')
                                            ->columnSpanFull(),

                                        Select::make('registro_trattamenti_item_id')
                                            ->label('Trattamento di riferimento')
                                            ->required()
                                            ->searchable()
                                            ->getSearchResultsUsing(fn(string $search) =>
                                                RegistroTrattamentiItem::query()
                                                    ->where('Attivita', 'like', "%{$search}%")
                                                    ->limit(50)
                                                    ->get()
                                                    ->mapWithKeys(fn($i) => [$i->id => substr($i->Attivita, 0, 80)])
                                                    ->toArray()
                                            )
                                            ->getOptionLabelUsing(fn($value) => optional(RegistroTrattamentiItem::find($value))->attivita_summary ?? $value)
                                            ->helperText('Collega questa DPIA a un trattamento del Registro Art. 30')
                                            ->columnSpanFull(),

                                        Select::make('status')
                                            ->label('Stato')
                                            ->options(\App\Models\Dpia::getStatusOptions())
                                            ->default('draft')
                                            ->required()
                                            ->native(false),

                                        DatePicker::make('next_review_date')
                                            ->label('Prossima revisione')
                                            ->displayFormat('d/m/Y')
                                            ->helperText('Lascia vuoto — verrà impostata automaticamente al completamento'),
                                    ]),

                                Section::make('Descrizione del trattamento')
                                    ->schema([
                                        Textarea::make('description_of_processing')
                                            ->label('Descrizione del trattamento')
                                            ->required()
                                            ->rows(4)
                                            ->placeholder('Descrivi in dettaglio il trattamento: natura, ambito, contesto e finalità...')
                                            ->columnSpanFull(),

                                        Textarea::make('necessity_assessment')
                                            ->label('Valutazione di necessità e proporzionalità')
                                            ->required()
                                            ->rows(4)
                                            ->placeholder('Spiega perché il trattamento è necessario e proporzionato rispetto alle finalità perseguite...')
                                            ->columnSpanFull(),

                                        Toggle::make('is_necessary')
                                            ->label('Il trattamento è necessario')
                                            ->helperText('Il trattamento è strettamente necessario per la finalità dichiarata')
                                            ->default(true),

                                        Toggle::make('is_proportional')
                                            ->label('Il trattamento è proporzionale')
                                            ->helperText('I dati trattati sono proporzionati rispetto alla finalità')
                                            ->default(true),
                                    ]),
                            ]),

                        Tab::make('Analisi dei Rischi')
                            ->icon('heroicon-o-shield-exclamation')
                            ->schema([
                                Section::make('Fonti di rischio')
                                    ->description('Le tabelle di lookup suggeriscono le voci standard — inserisci il testo liberamente nel campo')
                                    ->schema([
                                        Placeholder::make('risk_hint')
                                            ->label('')
                                            ->content(function () {
                                                $risks = DpiaRisk::query()->orderBy('name')->get();
                                                if ($risks->isEmpty()) {
                                                    return 'Nessuna fonte di rischio predefinita disponibile. Usa i campi liberi sotto.';
                                                }
                                                $grouped = $risks->groupBy(fn($r) => $r->category ?? 'Altro');
                                                $lines = ['Fonti di rischio suggerite dal catalogo:'];
                                                foreach ($grouped as $cat => $items) {
                                                    $catLabel = \App\Models\DpiaRisk::getCategories()[$cat] ?? ucfirst($cat);
                                                    $lines[] = "— {$catLabel}: " . $items->pluck('name')->join(', ');
                                                }
                                                return implode("\n", $lines);
                                            })
                                            ->columnSpanFull(),
                                    ]),

                                Section::make('Impatti potenziali')
                                    ->description('Le tabelle di lookup suggeriscono le voci standard — inserisci il testo liberamente nel campo')
                                    ->schema([
                                        Placeholder::make('impact_hint')
                                            ->label('')
                                            ->content(function () {
                                                $impacts = DpiaImpact::query()->orderBy('name')->get();
                                                if ($impacts->isEmpty()) {
                                                    return 'Nessun impatto predefinito disponibile. Usa i campi liberi sotto.';
                                                }
                                                $lines = ['Impatti suggeriti dal catalogo:'];
                                                foreach ($impacts as $impact) {
                                                    $lines[] = "• {$impact->name}" . ($impact->description ? " — {$impact->description}" : '');
                                                }
                                                return implode("\n", $lines);
                                            })
                                            ->columnSpanFull(),
                                    ]),

                                Section::make('Voci di rischio')
                                    ->description('Aggiungi una riga per ogni rischio identificato. Usa i suggerimenti sopra come riferimento.')
                                    ->schema([
                                        Repeater::make('dpiaItems')
                                            ->label('')
                                            ->relationship('dpiaItems')
                                            ->schema([
                                                TextInput::make('risk_source')
                                                    ->label('Fonte di rischio')
                                                    ->required()
                                                    ->placeholder('Es. Attacco hacker, Errore umano...')
                                                    ->datalist(DpiaItem::getRiskSourceOptions())
                                                    ->columnSpan(2),

                                                TextInput::make('potential_impact')
                                                    ->label('Impatto potenziale')
                                                    ->required()
                                                    ->placeholder('Es. Perdita di riservatezza...')
                                                    ->datalist(DpiaItem::getPotentialImpactOptions())
                                                    ->columnSpan(2),

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
                                                    ->placeholder('Calcolato automaticamente'),

                                                Select::make('privacy_security_id')
                                                    ->label('Misura di mitigazione')
                                                    ->placeholder('Seleziona misura (opzionale)')
                                                    ->options(
                                                        PrivacySecurity::all()
                                                            ->mapWithKeys(fn($m) => [$m->id => "[{$m->getTypeLabel()}] {$m->name}"])
                                                            ->toArray()
                                                    )
                                                    ->searchable()
                                                    ->nullable()
                                                    ->helperText('Scegli dal catalogo misure di sicurezza'),

                                                TextInput::make('residual_risk_score')
                                                    ->label('Rischio residuo')
                                                    ->numeric()
                                                    ->placeholder('Inserisci dopo mitigazione'),
                                            ])
                                            ->columns(4)
                                            ->addActionLabel('Aggiungi rischio')
                                            ->reorderable()
                                            ->collapsible()
                                            ->itemLabel(fn(array $state): ?string =>
                                                ($state['risk_source'] ?? null)
                                                    ? ($state['risk_source'] . ' → ' . ($state['potential_impact'] ?? ''))
                                                    : null
                                            )
                                            ->columnSpanFull(),
                                    ]),
                            ]),

                        Tab::make('Parere DPO & Conclusioni')
                            ->icon('heroicon-o-check-badge')
                            ->schema([
                                Section::make('Parere del DPO')
                                    ->schema([
                                        Textarea::make('dpo_opinion')
                                            ->label('Parere DPO')
                                            ->rows(5)
                                            ->placeholder('Inserisci il parere obbligatorio del Responsabile della Protezione dei Dati...')
                                            ->helperText('Art. 35 par. 2 GDPR — il DPO deve essere consultato nella conduzione della DPIA')
                                            ->columnSpanFull(),
                                    ]),

                                Section::make('Completamento')
                                    ->columns(2)
                                    ->schema([
                                        DatePicker::make('completion_date')
                                            ->label('Data completamento')
                                            ->displayFormat('d/m/Y'),

                                        DatePicker::make('next_review_date')
                                            ->label('Prossima revisione')
                                            ->displayFormat('d/m/Y')
                                            ->helperText('Di default +1 anno dalla data di completamento'),
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}

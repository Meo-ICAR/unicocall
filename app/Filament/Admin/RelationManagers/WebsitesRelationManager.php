<?php

namespace App\Filament\RelationManagers;

use App\Models\COMPILANCE\Website;
// use App\Services\TransparencyScanService;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class WebsitesRelationManager extends RelationManager
{
    protected static string $relationship = 'websites';
    protected static ?string $title = 'Siti Web';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('URL Sito')
                    ->required()
                    ->url()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function ($state, callable $set) {
                        // Estrai il dominio dall'URL e rimuovi www.
                        if ($state) {
                            $domain = parse_url($state, PHP_URL_HOST);
                            // Rimuovi www. se presente
                            $domain = preg_replace('/^www\./', '', $domain ?: '');
                            $set('domain', $domain);
                        } else {
                            $set('domain', '');
                        }
                    }),
                Toggle::make('is_active')
                    ->label('Attivo')
                    ->default(true),
                Textarea::make('description')
                    ->label('Descrizione')
                    ->maxLength(500)
                    ->columnSpanFull(),
                Select::make('type')
                    ->label('Tipo Sito')
                    ->options([
                        'corporate' => 'Sito Corporate',
                        'institutional' => 'Sito Istituzionale',
                        'portal' => 'Portale',
                        'social' => 'Sito Social',
                        'landing' => 'Landing page',
                        'ecommerce' => 'E-commerce',
                        'blog' => 'Blog',
                        'other' => 'Altro',
                    ])
                    ->default('corporate'),
                TextInput::make('domain')
                    ->label('Dominio')
                    ->helperText("Dominio estratto automaticamente dall'URL")
                    ->readOnly()
                    ->maxLength(255),
                Toggle::make('requires_transparency')
                    ->label('Richiede Trasparenza')
                    ->default(false)
                    ->live()
                    ->helperText('Se true, il sito verrà scansionato per documenti di trasparenza'),
                TextInput::make('url_transparency')
                    ->visible(fn($get) => $get('requires_transparency') === true)
                    ->label('URL Trasparenza')
                    ->url()
                    ->maxLength(255)
                    ->helperText('URL della pagina trasparenza se diversa dal sito principale'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')
                    ->label('URL sito')
                    ->searchable()
                    ->limit(40)
                    ->copyable()
                    ->copyMessage('URL copiato!')
                    ->copyMessageDuration(1500),
                TextColumn::make('url_transparency')
                    ->label('URL Trasparenza')
                    ->searchable()
                    ->limit(40)
                    ->placeholder('Non impostato')
                    ->url(fn($record) => $record->url_transparency)
                    ->openUrlInNewTab(),
                TextColumn::make('type')
                    ->label('Tipo')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'corporate' => 'primary',
                        'institutional' => 'success',
                        'portal' => 'info',
                        'ecommerce' => 'warning',
                        'blog' => 'secondary',
                        'other' => 'gray',
                        default => 'gray',
                    }),
                IconColumn::make('is_active')
                    ->label('Attivo')
                    ->boolean(),
                IconColumn::make('requires_transparency')
                    ->label('Richiede Trasparenza')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->label('Creato')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('Tipo Sito')
                    ->options([
                        'corporate' => 'Sito Corporate',
                        'institutional' => 'Sito Istituzionale',
                        'portal' => 'Portale',
                        'ecommerce' => 'E-commerce',
                        'blog' => 'Blog',
                        'other' => 'Altro',
                    ]),
                TernaryFilter::make('is_active')
                    ->label('Attivo'),
                TernaryFilter::make('requires_transparency')
                    ->label('Richiede Trasparenza'),
                Filter::make('has_transparency_url')
                    ->label('Ha URL Trasparenza')
                    ->query(fn($query) => $query->whereNotNull('url_transparency')),
            ])
            ->headerActions([
                CreateAction::make(),
                Action::make('run_transparency_scan')
                    ->label('Scansione Trasparenza')
                    ->icon('heroicon-o-magnifying-glass')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('Scansione Trasparenza')
                    ->modalDescription('Esegui la scansione dei siti web per trovare documenti di trasparenza')
                    ->modalSubmitActionLabel('Avvia Scansione')
                    ->action(function () {
                        $ownerRecord = $this->getOwnerRecord();
                        $scanService = new TransparencyScanService();

                        try {
                            // Get company ID based on owner type
                            $companyId = $this->getCompanyIdFromOwner($ownerRecord);

                            if (!$companyId) {
                                Notification::make()
                                    ->title('Errore Scansione')
                                    ->body("Impossibile determinare l'azienda associata")
                                    ->danger()
                                    ->send();
                                return;
                            }

                            $results = $scanService->scanForCompany($companyId);

                            $message = "Scansione completata!\n"
                                . "Siti processati: {$results['processed_websites']}\n"
                                . "Pagine trasparenza trovate: {$results['found_transparency_pages']}\n"
                                . "Documenti estratti: {$results['extracted_documents']}";

                            Notification::make()
                                ->title('Scansione Trasparenza Completata')
                                ->body($message)
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Errore Scansione')
                                ->body('Si è verificato un errore durante la scansione: ' . $e->getMessage())
                                ->danger()
                                ->send();
                        }
                    })
                    ->visible(function () {
                        $ownerRecord = $this->getOwnerRecord();
                        return $ownerRecord && $ownerRecord->websites()->exists();
                    }),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
                Action::make('test_url')
                    ->label('Test URL')
                    ->icon('heroicon-o-globe-alt')
                    ->color('info')
                    ->action(function (Website $record) {
                        try {
                            $scanService = new TransparencyScanService();
                            $ownerRecord = $this->getOwnerRecord();

                            // Get company ID from owner
                            $companyId = $this->getCompanyIdFromOwner($ownerRecord);

                            if (!$companyId) {
                                Notification::make()
                                    ->title('Errore Scansione')
                                    ->body("Impossibile determinare l'azienda associata")
                                    ->danger()
                                    ->send();
                                return;
                            }

                            // Scan specific website
                            $results = $scanService->scanSingleWebsite($record);

                            $message = "Scansione completata per {$record->name}!\n"
                                . 'Pagina trasparenza trovata: ' . ($results['found_transparency_page'] ? 'Sì' : 'No') . "\n"
                                . "Documenti estratti: {$results['documents_created']}";

                            Notification::make()
                                ->title('Scansione URL Completata')
                                ->body($message)
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Errore Scansione URL')
                                ->body('Si è verificato un errore durante la scansione: ' . $e->getMessage())
                                ->danger()
                                ->send();
                        }
                    })
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    /**
     * Get company ID from various owner types
     */
    private function getCompanyIdFromOwner($ownerRecord): ?string
    {
        return match ($ownerRecord::class) {
            'App\Models\PROFORMA\Company' => $ownerRecord->id,
            'App\Models\Agent' => $ownerRecord->id,
            'App\Models\Client' => $ownerRecord->id,
            'App\Models\Principal' => $ownerRecord->id,
            default => null
        };
    }

    /**
     * Get websites that have transparency URL
     */
    private function getWebsitesWithTransparency($ownerRecord)
    {
        $websites = $ownerRecord->websites();

        if (method_exists($websites, 'whereNotNull')) {
            return $websites->whereNotNull('url_transparency')->get();
        }

        return collect();
    }
}

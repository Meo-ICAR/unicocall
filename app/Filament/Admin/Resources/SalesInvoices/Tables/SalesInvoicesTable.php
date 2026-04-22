<?php

namespace App\Filament\Admin\Resources\SalesInvoices\Tables;

use App\Models\Company;
use App\Services\SalesInvoiceImportService;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class SalesInvoicesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('cliente')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('data_documento')
                    ->date()
                    ->sortable(),
                TextColumn::make('totale_iva')
                    ->money('EUR')
                    ->alignRight()
                    ->sortable(),
                TextColumn::make('totale_documento')
                    ->money('EUR')
                    ->alignRight()
                    ->sortable(),
                TextColumn::make('netto_a_pagare')
                    ->money('EUR')
                    ->alignRight()
                    ->sortable(),
                TextColumn::make('partita_iva')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('numero')
                    ->searchable(),
                TextColumn::make('nome_file'),
                TextColumn::make('id_sdi'),
                TextColumn::make('data_invio')
                    ->date()
                    ->sortable(),
                TextColumn::make('tipo_documento')
                    ->searchable(),
                TextColumn::make('tipo_cliente')
                    ->searchable(),
                TextColumn::make('codice_fiscale')
                    ->searchable(),
                TextColumn::make('indirizzo_telematico')
                    ->searchable(),
                TextColumn::make('metodo_pagamento')
                    ->searchable(),
                TextColumn::make('totale_imponibile')
                    ->money('EUR')
                    ->alignRight()
                    ->sortable(),
                TextColumn::make('totale_escluso_iva_n1')
                    ->money('EUR')
                    ->alignRight()
                    ->sortable(),
                TextColumn::make('totale_non_soggetto_iva_n2')
                    ->money('EUR')
                    ->alignRight()
                    ->sortable(),
                TextColumn::make('totale_non_imponibile_iva_n3')
                    ->money('EUR')
                    ->alignRight()
                    ->sortable(),
                TextColumn::make('totale_esente_iva_n4')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('totale_regime_margine_iva_n5')
                    ->money('EUR')
                    ->alignRight()
                    ->sortable(),
                TextColumn::make('totale_inversione_contabile_n6')
                    ->money('EUR')
                    ->alignRight()
                    ->sortable(),
                TextColumn::make('totale_iva_assolta_altro_stato_ue_n7')
                    ->money('EUR')
                    ->alignRight()
                    ->sortable(),
                TextColumn::make('incassi')
                    ->money('EUR')
                    ->alignRight()
                    ->searchable(),
                TextColumn::make('data_incasso')
                    ->date()
                    ->sortable(),
                TextColumn::make('stato')
                    ->searchable(),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
                Action::make('import_companies')
                    ->label('Importa Vendite')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->form([
                        FileUpload::make('excel_file')
                            ->label('File Excel')
                            ->required()
                            ->acceptedFileTypes(['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.ms-excel'])
                            ->helperText('Carica un file Excel con le fatture da importare')
                            //      ->directory('temp')
                            ->disk('local'),
                    ])
                    ->action(function (array $data) {
                        try {
                            $user = auth()->user();

                            // Only allow super admin to import companies
                            if (!$user->is_super_admin) {
                                Notification::make()
                                    ->danger()
                                    ->title('Accesso Negato')
                                    ->body('Non hai i permessi per eseguire questa operazione.')
                                    ->send();
                                return;
                            }

                            // Check if file exists and is valid
                            if (!isset($data['excel_file']) || empty($data['excel_file'])) {
                                Notification::make()
                                    ->danger()
                                    ->title('Errore File')
                                    ->body('File non trovato o non valido.')
                                    ->send();
                                return;
                            }

                            // FileUpload returns a string path when using disk/directory
                            $filePath = $data['excel_file'];
                            $fullPath = storage_path('app/private/' . basename($filePath));

                            // Get current company ID for import
                            $currentCompany = auth()->user()->current_company_id;
                            if (!$currentCompany) {
                                Notification::make()
                                    ->danger()
                                    ->title('Errore Azienda')
                                    ->body('Nessuna azienda corrente impostata.')
                                    ->send();
                                return;
                            }

                            // Run the import service for sales invoices
                            $importService = new SalesInvoiceImportService();
                            $result = $importService->importSalesInvoices($fullPath, $currentCompany);

                            // Clean up temporary file
                            unlink($fullPath);

                            if ($result['success']) {
                                Notification::make()
                                    ->success()
                                    ->title('Importazione Completata')
                                    ->body("Importate {$result['imported']} fatture attive. Saltate {$result['skipped']} righe.")
                                    ->send();
                            } else {
                                Notification::make()
                                    ->danger()
                                    ->title('Errore Importazione')
                                    ->body($result['error'] ?? "Errore durante l'importazione")
                                    ->send();
                            }

                            if (!empty($result['errors'])) {
                                foreach ($result['errors'] as $error) {
                                    Notification::make()
                                        ->warning()
                                        ->title('Attenzione')
                                        ->body($error)
                                        ->send();
                                }
                            }
                        } catch (\Exception $e) {
                            Notification::make()
                                ->danger()
                                ->title('Errore')
                                ->body('Si è verificato un errore: ' . $e->getMessage())
                                ->send();
                        }
                    }),

                /*
                 * ->visible(function () {
                 *     $user = Auth::user();
                 *     $firstCompany = Company::first();
                 *
                 *     // Only show for super admins in the first company
                 *     return $user &&
                 *         $user->is_super_admin &&
                 *         $firstCompany &&
                 *         $user->current_company_id === $firstCompany->id;
                 * })
                 */
            ]);
    }
}

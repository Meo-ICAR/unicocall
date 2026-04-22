<?php

namespace App\Filament\Admin\Resources\Companies\Tables;

use App\Models\Company;
use App\Services\ExcelImportService;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class CompaniesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('name')
            ->columns([
                TextColumn::make('name')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('vat_number')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('company_type')
                    ->sortable()
                    ->badge(),
                TextColumn::make('companyAdminUser.name')
                    ->label('Admin User')
                    ->searchable(),
                TextColumn::make('contact_email')
                    ->searchable(),
                TextColumn::make('dpo_email')
                    ->searchable(),
                IconColumn::make('is_iso27001_certified')
                    ->boolean(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
                Action::make('import_companies')
                    ->label('Importa Aziende')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->form([
                        FileUpload::make('excel_file')
                            ->label('File Excel')
                            ->required()
                            ->acceptedFileTypes(['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.ms-excel'])
                            ->helperText('Carica un file Excel con le aziende da importare')
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

                            // Run the import service without companyId (imports as new companies)
                            $importService = new ExcelImportService();
                            $result = $importService->importCompanies($fullPath);

                            // Clean up temporary file
                            unlink($fullPath);

                            if ($result['success']) {
                                Notification::make()
                                    ->success()
                                    ->title('Importazione Completata')
                                    ->body("Importate {$result['imported']} aziende. Saltate {$result['skipped']} righe.")
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

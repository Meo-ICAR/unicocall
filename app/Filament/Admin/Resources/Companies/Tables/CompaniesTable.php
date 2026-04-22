<?php

namespace App\Filament\Admin\Resources\Companies\Tables;

use App\Models\Company;
use App\Services\ExcelImportService;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class CompaniesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->searchable(),
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('vat_number')
                    ->searchable(),
                TextColumn::make('sponsor')
                    ->searchable(),
                TextColumn::make('company_type')
                    ->badge(),
                IconColumn::make('is_iso27001_certified')
                    ->boolean(),
                TextColumn::make('contact_email')
                    ->searchable(),
                TextColumn::make('dpo_email')
                    ->searchable(),
                TextColumn::make('user_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('smtp_host')
                    ->searchable(),
                TextColumn::make('smtp_port')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('smtp_encryption')
                    ->searchable(),
                IconColumn::make('smtp_enabled')
                    ->boolean(),
                IconColumn::make('smtp_verify_ssl')
                    ->boolean(),
                TextColumn::make('payment_frequency')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('payment')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('payment_last_date')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('payment_startup')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
                        \Filament\Forms\Components\FileUpload::make('excel_file')
                            ->label('File Excel')
                            ->required()
                            ->acceptedFileTypes(['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.ms-excel'])
                            ->helperText('Carica un file Excel con le aziende da importare'),
                    ])
                    ->action(function (array $data) {
                        try {
                            $user = Auth::user();
                            $firstCompany = Company::first();

                            // Check if user is super admin and current company is the first company
                            if (!$user->is_super_admin || $user->current_company_id !== $firstCompany->id) {
                                \Filament\Notifications\Notification::make()
                                    ->danger()
                                    ->title('Accesso Negato')
                                    ->body('Non hai i permessi per eseguire questa operazione.')
                                    ->send();
                                return;
                            }

                            // Store the uploaded file temporarily
                            $filePath = $data['excel_file']->store('temp', 'local');
                            $fullPath = storage_path('app/' . $filePath);

                            // Run the import service without companyId (imports as new companies)
                            $importService = new ExcelImportService();
                            $result = $importService->importCompanies($fullPath);

                            // Clean up temporary file
                            unlink($fullPath);

                            if ($result['success']) {
                                \Filament\Notifications\Notification::make()
                                    ->success()
                                    ->title('Importazione Completata')
                                    ->body("Importate {$result['imported']} aziende. Saltate {$result['skipped']} righe.")
                                    ->send();
                            } else {
                                \Filament\Notifications\Notification::make()
                                    ->danger()
                                    ->title('Errore Importazione')
                                    ->body($result['error'] ?? "Errore durante l'importazione")
                                    ->send();
                            }

                            if (!empty($result['errors'])) {
                                foreach ($result['errors'] as $error) {
                                    \Filament\Notifications\Notification::make()
                                        ->warning()
                                        ->title('Attenzione')
                                        ->body($error)
                                        ->send();
                                }
                            }
                        } catch (\Exception $e) {
                            \Filament\Notifications\Notification::make()
                                ->danger()
                                ->title('Errore')
                                ->body('Si è verificato un errore: ' . $e->getMessage())
                                ->send();
                        }
                    })
                    ->visible(function () {
                        $user = Auth::user();
                        $firstCompany = Company::first();

                        // Only show for super admins in the first company
                        return $user &&
                            $user->is_super_admin &&
                            $firstCompany &&
                            $user->current_company_id === $firstCompany->id;
                    }),
            ]);
    }
}

<?php

namespace App\Filament\Admin\Resources\Employees\Tables;

use App\Notifications\EmployeeImportNotification;
use App\Services\EmployeeImportService;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;

class EmployeesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('role')
                    ->searchable(),
                TextColumn::make('cf')
                    ->searchable(),
                TextColumn::make('phone')
                    ->searchable(),
                TextColumn::make('department')
                    ->searchable(),
                TextColumn::make('hiring_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('termination_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('company_branch_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('coordinated_by_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('employee_types')
                    ->searchable(),
                TextColumn::make('supervisor_type')
                    ->searchable(),
                TextColumn::make('privacy_role')
                    ->searchable(),
                TextColumn::make('retention_period')
                    ->searchable(),
                TextColumn::make('extra_eu_transfer')
                    ->searchable(),
                TextColumn::make('privacy_data')
                    ->searchable(),
                IconColumn::make('is_structure')
                    ->boolean(),
                IconColumn::make('is_ghost')
                    ->boolean(),
                TextColumn::make('employee_type')
                    ->searchable(),
                TextColumn::make('employee_id')
                    ->searchable(),
                TextColumn::make('user_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('created_by')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('updated_by')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('deleted_by')
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
                TextColumn::make('deleted_at')
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
                Action::make('import_employees')
                    ->label('Importa Dipendenti')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->form([
                        FileUpload::make('file')
                            ->label('Seleziona file Excel')
                            ->required()
                            ->acceptedFileTypes(['xlsx', 'xls'])
                            ->maxSize(10240)  // 10MB
                            ->directory('imports')
                            ->helperText('Seleziona un file Excel (.xlsx o .xls) contenente i dati dei dipendenti')
                    ])
                    ->action(function (array $data) {
                        try {
                            $filePath = Storage::disk('local')->path($data['file']);

                            $importService = new EmployeeImportService();
                            $results = $importService->importFromFile($filePath);

                            // Delete the temporary file
                            Storage::disk('local')->delete($data['file']);

                            // Send notification
                            Notification::route('notifications', auth()->user())
                                ->notify(new EmployeeImportNotification($results));

                            if (!empty($results['errors'])) {
                                Notification::route('notifications', auth()->user())
                                    ->error('Errori Importazione', implode("\n", $results['errors']));
                            }
                        } catch (\Exception $e) {
                            Notification::route('notifications', auth()->user())
                                ->error('Errore Importazione', $e->getMessage());

                            // Delete the temporary file even on error
                            if (isset($data['file'])) {
                                Storage::disk('local')->delete($data['file']);
                            }
                        }
                    })
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ])
            ]);
    }
}

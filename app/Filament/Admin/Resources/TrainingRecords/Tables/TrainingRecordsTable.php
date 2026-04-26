<?php

namespace App\Filament\Admin\Resources\TrainingRecords\Tables;

use App\Models\Client;
use App\Models\Employee;
use App\Models\TrainingRecord;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class TrainingRecordsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('training_date')
                    ->label('Data')
                    ->date('d/m/Y')
                    ->sortable(),

                TextColumn::make('trainable_type')
                    ->label('Tipo')
                    ->formatStateUsing(fn($state) => match ($state) {
                        Employee::class => 'Dipendente',
                        Client::class   => 'Cliente',
                        default         => $state,
                    })
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        Employee::class => 'info',
                        Client::class   => 'warning',
                        default         => 'gray',
                    }),

                TextColumn::make('trainable.name')
                    ->label('Partecipante')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('regulatory_framework')
                    ->label('Normativa')
                    ->badge()
                    ->formatStateUsing(fn($state) => match ($state) {
                        'gdpr'             => 'GDPR',
                        'oam'              => 'OAM',
                        'ivass'            => 'IVASS',
                        'sicurezza_lavoro' => 'Sicurezza',
                        'antiriciclaggio'  => 'Antiriciclaggio',
                        'mifid'            => 'MiFID II',
                        default            => 'Altro',
                    })
                    ->color(fn($state) => match ($state) {
                        'gdpr'             => 'info',
                        'oam'              => 'warning',
                        'ivass'            => 'success',
                        'sicurezza_lavoro' => 'danger',
                        'antiriciclaggio'  => 'gray',
                        'mifid'            => 'primary',
                        default            => 'gray',
                    }),

                TextColumn::make('course_title')
                    ->label('Corso')
                    ->searchable()
                    ->limit(40)
                    ->tooltip(fn($record) => $record->course_title),

                TextColumn::make('hours')
                    ->label('Ore')
                    ->suffix('h')
                    ->sortable(),

                TextColumn::make('outcome')
                    ->label('Esito')
                    ->badge()
                    ->formatStateUsing(fn($state) => TrainingRecord::getOutcomeOptions()[$state] ?? $state)
                    ->color(fn($state) => match ($state) {
                        'passed'   => 'success',
                        'attended' => 'info',
                        'partial'  => 'warning',
                        'failed'   => 'danger',
                        default    => 'gray',
                    }),

                IconColumn::make('certificate_issued')
                    ->label('Attestato')
                    ->boolean()
                    ->trueColor('success')
                    ->falseColor('gray'),

                TextColumn::make('expiry_date')
                    ->label('Scadenza')
                    ->date('d/m/Y')
                    ->sortable()
                    ->color(fn($record) => $record->expiry_status_color)
                    ->description(fn($record) => $record->is_expired
                        ? 'Scaduta'
                        : ($record->is_expiring_soon ? 'In scadenza' : null)
                    ),
            ])
            ->defaultSort('training_date', 'desc')
            ->filters([
                SelectFilter::make('regulatory_framework')
                    ->label('Normativa')
                    ->options(TrainingRecord::getRegulatoryFrameworkOptions()),

                SelectFilter::make('outcome')
                    ->label('Esito')
                    ->options(TrainingRecord::getOutcomeOptions()),

                SelectFilter::make('trainable_type')
                    ->label('Tipo partecipante')
                    ->options([
                        Employee::class => 'Dipendente',
                        Client::class   => 'Cliente / Consulente',
                    ]),

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
            ]);
    }
}

<?php

namespace App\Filament\Admin\RelationManagers;

use App\Filament\Admin\Resources\TrainingRecords\Schemas\TrainingRecordForm;
use App\Models\TrainingRecord;
use Filament\Actions;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class TrainingRecordsRelationManager extends RelationManager
{
    protected static string $relationship = 'trainingRecords';

    protected static ?string $title = 'Registro Formazione';

    public function form(Schema $schema): Schema
    {
        return TrainingRecordForm::configureForRelation($schema);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('course_title')
            ->defaultSort('training_date', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('training_date')
                    ->label('Data')
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('regulatory_framework')
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
                        default            => 'gray',
                    }),

                Tables\Columns\TextColumn::make('course_title')
                    ->label('Corso')
                    ->searchable()
                    ->limit(45),

                Tables\Columns\TextColumn::make('hours')
                    ->label('Ore')
                    ->suffix('h'),

                Tables\Columns\TextColumn::make('outcome')
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

                Tables\Columns\IconColumn::make('certificate_issued')
                    ->label('Attestato')
                    ->boolean(),

                Tables\Columns\TextColumn::make('expiry_date')
                    ->label('Scadenza')
                    ->date('d/m/Y')
                    ->color(fn($record) => $record->expiry_status_color),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('regulatory_framework')
                    ->label('Normativa')
                    ->options(TrainingRecord::getRegulatoryFrameworkOptions()),
            ])
            ->headerActions([
                Actions\CreateAction::make()->label('Aggiungi formazione'),
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

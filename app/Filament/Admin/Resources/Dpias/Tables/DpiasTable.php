<?php

namespace App\Filament\Admin\Resources\Dpias\Tables;

use App\Models\Dpia;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class DpiasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nome DPIA')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('registroTrattamentiItem.Attivita')
                    ->label('Trattamento')
                    ->limit(50)
                    ->tooltip(fn($record) => $record->registroTrattamentiItem?->Attivita)
                    ->searchable(),

                TextColumn::make('status')
                    ->label('Stato')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'draft'        => 'gray',
                        'under_review' => 'warning',
                        'completed'    => 'success',
                        default        => 'gray',
                    })
                    ->formatStateUsing(fn(string $state): string => Dpia::getStatusOptions()[$state] ?? $state)
                    ->sortable(),

                IconColumn::make('is_necessary')
                    ->label('Necessario')
                    ->boolean()
                    ->trueColor('success')
                    ->falseColor('danger'),

                IconColumn::make('is_proportional')
                    ->label('Proporzionale')
                    ->boolean()
                    ->trueColor('success')
                    ->falseColor('danger'),

                TextColumn::make('dpiaItems_count')
                    ->label('Rischi')
                    ->counts('dpiaItems')
                    ->badge()
                    ->color('info'),

                TextColumn::make('next_review_date')
                    ->label('Prossima revisione')
                    ->date('d/m/Y')
                    ->sortable()
                    ->color(fn($record) => $record?->is_overdue ? 'danger' : null),

                TextColumn::make('completion_date')
                    ->label('Completata il')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Creata')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->label('Stato')
                    ->options(Dpia::getStatusOptions()),

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

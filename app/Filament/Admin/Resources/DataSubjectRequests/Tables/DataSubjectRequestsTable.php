<?php

namespace App\Filament\Admin\Resources\DataSubjectRequests\Tables;

use App\Models\DataSubjectRequest;
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

class DataSubjectRequestsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('received_at')
                    ->label('Ricevuta il')
                    ->date('d/m/Y')
                    ->sortable(),

                TextColumn::make('requester_name')
                    ->label('Richiedente')
                    ->searchable()
                    ->description(fn($record) => $record->requester_email),

                TextColumn::make('request_type')
                    ->label('Tipo')
                    ->badge()
                    ->formatStateUsing(fn($state) => DataSubjectRequest::getRequestTypeOptions()[$state] ?? $state)
                    ->color('info')
                    ->wrap(),

                TextColumn::make('status')
                    ->label('Stato')
                    ->badge()
                    ->formatStateUsing(fn($state) => DataSubjectRequest::getStatusOptions()[$state] ?? $state)
                    ->color(fn(string $state): string => match ($state) {
                        'received'    => 'info',
                        'in_progress' => 'warning',
                        'completed'   => 'success',
                        'rejected'    => 'danger',
                        'extended'    => 'gray',
                        default       => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('deadline_at')
                    ->label('Scadenza')
                    ->date('d/m/Y')
                    ->sortable()
                    ->color(fn($record) => match (true) {
                        $record->is_overdue                => 'danger',
                        $record->days_remaining <= 5       => 'warning',
                        default                            => null,
                    })
                    ->description(fn($record) => $record->extended_until
                        ? 'Prorogata al ' . $record->extended_until->format('d/m/Y')
                        : null
                    ),

                TextColumn::make('days_remaining')
                    ->label('Giorni rimasti')
                    ->state(fn($record) => $record->is_overdue
                        ? 'Scaduta'
                        : ($record->days_remaining . ' gg')
                    )
                    ->badge()
                    ->color(fn($record) => match (true) {
                        $record->is_overdue          => 'danger',
                        $record->days_remaining <= 5 => 'warning',
                        default                      => 'success',
                    })
                    ->visible(fn($record) => !in_array($record?->status ?? '', ['completed', 'rejected'])),

                IconColumn::make('identity_verified')
                    ->label('ID verificata')
                    ->boolean()
                    ->trueColor('success')
                    ->falseColor('warning'),

                TextColumn::make('channel')
                    ->label('Canale')
                    ->formatStateUsing(fn($state) => DataSubjectRequest::getChannelOptions()[$state] ?? $state)
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Creata')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('received_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->label('Stato')
                    ->options(DataSubjectRequest::getStatusOptions()),

                SelectFilter::make('request_type')
                    ->label('Tipo di richiesta')
                    ->options(DataSubjectRequest::getRequestTypeOptions()),

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

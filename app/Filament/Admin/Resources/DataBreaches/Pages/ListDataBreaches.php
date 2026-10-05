<?php

namespace App\Filament\Admin\Resources\DataBreaches\Pages;

use App\Filament\Admin\Resources\DataBreaches\DataBreachResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDataBreaches extends ListRecords
{
    protected static string $resource = DataBreachResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

<?php

namespace App\Filament\Admin\Resources\RegistroTrattamentis\Pages;

use App\Filament\Admin\Resources\RegistroTrattamentis\RegistroTrattamentiResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListRegistroTrattamentis extends ListRecords
{
    protected static string $resource = RegistroTrattamentiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

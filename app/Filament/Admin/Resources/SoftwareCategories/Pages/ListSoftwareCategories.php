<?php

namespace App\Filament\Resources\COMPILANCE\SoftwareCategories\Pages;

use App\Filament\Resources\COMPILANCE\SoftwareCategories\SoftwareCategoryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSoftwareCategories extends ListRecords
{
    protected static string $resource = SoftwareCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

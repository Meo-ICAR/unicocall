<?php

namespace App\Filament\Resources\COMPILANCE\SoftwareCategories\Pages;

use App\Filament\Resources\COMPILANCE\SoftwareCategories\SoftwareCategoryResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSoftwareCategory extends EditRecord
{
    protected static string $resource = SoftwareCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

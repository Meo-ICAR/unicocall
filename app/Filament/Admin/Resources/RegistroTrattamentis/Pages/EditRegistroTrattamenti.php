<?php

namespace App\Filament\Admin\Resources\RegistroTrattamentis\Pages;

use App\Filament\Admin\Resources\RegistroTrattamentis\RegistroTrattamentiResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditRegistroTrattamenti extends EditRecord
{
    protected static string $resource = RegistroTrattamentiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}

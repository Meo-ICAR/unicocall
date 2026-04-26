<?php

namespace App\Filament\Admin\Resources\DataSubjectRequests\Pages;

use App\Filament\Admin\Resources\DataSubjectRequests\DataSubjectRequestResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditDataSubjectRequest extends EditRecord
{
    protected static string $resource = DataSubjectRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}

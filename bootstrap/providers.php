<?php

use App\Providers\AppServiceProvider;
use BezhanSalleh\FilamentShield\FilamentShieldServiceProvider;
use DutchCodingCompany\FilamentSocialite\FilamentSocialiteServiceProvider;
use Filament\Actions\ActionsServiceProvider;
use Filament\Forms\FormsServiceProvider;
use Filament\Infolists\InfolistsServiceProvider;
use Filament\Notifications\NotificationsServiceProvider;
use Filament\Resources\ResourcesServiceProvider;
use Filament\SpatieLaravelMediaLibraryPlugin\SpatieLaravelMediaLibraryPluginServiceProvider;
use Filament\Support\SupportServiceProvider;
use Filament\Tables\TablesServiceProvider;
use Spatie\Permission\PermissionServiceProvider;
use Wildside\Userstamps\UserstampsServiceProvider;

return [
    AppServiceProvider::class,
];

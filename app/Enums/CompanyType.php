<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum CompanyType: string implements HasLabel
{
    case MEDIATORE = 'Mediatore';
    case CALL_CENTER = 'Call Center';
    case ALBERGO = 'Albergo';
    case SOFTWARE_HOUSE = 'Software House';
    case LIST_PROVIDER = 'List Provider';
    case UTILITY = 'Utility';
    case PROVIDER = 'Provider';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::MEDIATORE => 'Mediatore',
            self::CALL_CENTER => 'Call Center',
            self::ALBERGO => 'Albergo',
            self::SOFTWARE_HOUSE => 'Software House',
            self::LIST_PROVIDER => 'List Provider',
            self::UTILITY => 'Utility',
            self::PROVIDER => 'Provider',
        };
    }

    public function getId(): int
    {
        return match ($this) {
            self::MEDIATORE => 1,
            self::CALL_CENTER => 2,
            self::ALBERGO => 3,
            self::SOFTWARE_HOUSE => 4,
            self::LIST_PROVIDER => 5,
            self::UTILITY => 6,
            self::PROVIDER => 7,
        };
    }

    public static function fromId(int $id): ?self
    {
        return match ($id) {
            1 => self::MEDIATORE,
            2 => self::CALL_CENTER,
            3 => self::ALBERGO,
            4 => self::SOFTWARE_HOUSE,
            5 => self::LIST_PROVIDER,
            6 => self::UTILITY,
            7 => self::PROVIDER,
            default => null,
        };
    }

    public static function getAll(): array
    {
        return [
            self::MEDIATORE->getId() => self::MEDIATORE->getLabel(),
            self::CALL_CENTER->getId() => self::CALL_CENTER->getLabel(),
            self::ALBERGO->getId() => self::ALBERGO->getLabel(),
            self::SOFTWARE_HOUSE->getId() => self::SOFTWARE_HOUSE->getLabel(),
            self::LIST_PROVIDER->getId() => self::LIST_PROVIDER->getLabel(),
            self::UTILITY->getId() => self::UTILITY->getLabel(),
            self::PROVIDER->getId() => self::PROVIDER->getLabel(),
        ];
    }

    public function getDescription(): string
    {
        return match ($this) {
            self::MEDIATORE => 'Società di mediazione creditizia e assicurativa',
            self::CALL_CENTER => 'Centrale operativa per contatti telefonici',
            self::ALBERGO => 'Struttura ricettiva alberghiera',
            self::SOFTWARE_HOUSE => 'Azienda di sviluppo software',
            self::LIST_PROVIDER => 'Fornitore di liste di contatti e dati',
            self::UTILITY => 'Azienda di servizi pubblici essenziali',
            self::PROVIDER => 'Fornitore di servizi specializzati',
        };
    }

    public function isFinancial(): bool
    {
        return match ($this) {
            self::MEDIATORE => true,
            self::CALL_CENTER => true,
            self::ALBERGO => false,
            self::SOFTWARE_HOUSE => false,
            self::LIST_PROVIDER => true,
            self::UTILITY => false,
            self::PROVIDER => false,
        };
    }

    public function isService(): bool
    {
        return match ($this) {
            self::MEDIATORE => true,
            self::CALL_CENTER => true,
            self::ALBERGO => true,
            self::SOFTWARE_HOUSE => true,
            self::LIST_PROVIDER => true,
            self::UTILITY => true,
            self::PROVIDER => true,
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::MEDIATORE => 'fas-handshake',
            self::CALL_CENTER => 'fas-headset',
            self::ALBERGO => 'fas-bed',
            self::SOFTWARE_HOUSE => 'fas-code',
            self::LIST_PROVIDER => 'fas-list',
            self::UTILITY => 'fas-bolt',
            self::PROVIDER => 'fa-cogs',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::MEDIATORE => 'primary',
            self::CALL_CENTER => 'success',
            self::ALBERGO => 'warning',
            self::SOFTWARE_HOUSE => 'info',
            self::LIST_PROVIDER => 'secondary',
            self::UTILITY => 'danger',
            self::PROVIDER => 'gray',
        };
    }
}

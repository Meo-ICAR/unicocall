<?php

namespace App\Traits;

use App\Models\PrivacyDataType;
use App\Models\PrivacyLegalBasis;
use App\Models\PrivacyRetention;
use App\Models\PrivacySecurity;
use App\Models\PrivacySubject;

/**
 * Provides suggestion lists from privacy lookup tables for free-text fields.
 * No FK columns are added — fields remain plain text, lookups are used only
 * to populate datalist/autocomplete options in the UI.
 */
trait HasPrivacySuggestions
{
    /**
     * Suggested values for the `privacy_role` field.
     * Sourced from PrivacyLegalBasis names.
     */
    public static function privacyRoleSuggestions(): array
    {
        return PrivacyLegalBasis::orderBy('name')->pluck('name')->toArray();
    }

    /**
     * Suggested values for the `data_categories` / `data_subjects` fields.
     * Sourced from PrivacyDataType, grouped by category.
     */
    public static function dataTypeSuggestions(): array
    {
        return PrivacyDataType::orderBy('category')->orderBy('name')
            ->pluck('name')
            ->toArray();
    }

    /**
     * Suggested values grouped by category (comuni / particolari / giudiziari).
     */
    public static function dataTypeSuggestionsGrouped(): array
    {
        return PrivacyDataType::getGroupedByCategory();
    }

    /**
     * Suggested values for the `retention_period` field.
     * Returns formatted strings like "10 Anni", "6 Mesi".
     */
    public static function retentionSuggestions(): array
    {
        return PrivacyRetention::all()
            ->map(fn ($r) => $r->getFormattedRetention())
            ->unique()
            ->sort()
            ->values()
            ->toArray();
    }

    /**
     * Suggested values for the `security_measures` field.
     * Only implemented measures are suggested.
     */
    public static function securityMeasureSuggestions(): array
    {
        return PrivacySecurity::implemented()
            ->orderBy('name')
            ->pluck('name')
            ->toArray();
    }

    /**
     * Suggested values for the `data_subjects` field.
     * Sourced from PrivacySubject names.
     */
    public static function subjectSuggestions(): array
    {
        return PrivacySubject::orderBy('name')->pluck('name')->toArray();
    }

    /**
     * Suggested values for the `extra_eu_transfer` field.
     */
    public static function extraEuSuggestions(): array
    {
        return ['Sì', 'No', 'Parziale'];
    }

    /**
     * All suggestions keyed by field name, ready for Filament datalist.
     */
    public static function allPrivacySuggestions(): array
    {
        return [
            'privacy_role'      => static::privacyRoleSuggestions(),
            'data_categories'   => static::dataTypeSuggestions(),
            'data_subjects'     => static::subjectSuggestions(),
            'retention_period'  => static::retentionSuggestions(),
            'security_measures' => static::securityMeasureSuggestions(),
            'extra_eu_transfer' => static::extraEuSuggestions(),
        ];
    }
}

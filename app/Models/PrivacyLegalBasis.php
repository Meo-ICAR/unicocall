<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrivacyLegalBasis extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'reference_article',
        'description',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get formatted reference article
     */
    public function getFormattedReferenceAttribute(): string
    {
        return $this->reference_article ?: 'N/D';
    }

    /**
     * Get short description (first 100 chars)
     */
    public function getShortDescriptionAttribute(): string
    {
        return strlen($this->description) > 100
            ? substr($this->description, 0, 100) . '...'
            : $this->description;
    }

    /**
     * Scope to search by name
     */
    public function scopeByName($query, $name)
    {
        return $query->where('name', 'like', '%' . $name . '%');
    }

    /**
     * Scope to search by reference article
     */
    public function scopeByReference($query, $reference)
    {
        return $query->where('reference_article', 'like', '%' . $reference . '%');
    }

    /**
     * Get all legal bases as options for select
     */
    public static function getOptions(): array
    {
        return static::pluck('name', 'id')->toArray();
    }

    /**
     * Get legal bases grouped by article
     */
    public static function getGroupedByArticle(): array
    {
        return static::all()
            ->groupBy('reference_article')
            ->map(function ($group) {
                return $group->pluck('name', 'id');
            })
            ->toArray();
    }
}

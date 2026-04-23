<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Model;
use Wildside\Userstamps\HasUserstamps;

class Subappalti extends Model
{
    use HasUserstamps;

    protected $guarded = [];

    protected $casts = [
        'nomina_at' => 'datetime',
    ];

    /**
     * Get the sub contractor (polymorphic relationship)
     */
    public function sub(): MorphTo
    {
        return $this->morphTo('sub');
    }

    /**
     * Get the originator (polymorphic relationship)
     */
    public function originator(): MorphTo
    {
        return $this->morphTo('originator');
    }

    /**
     * Get the company that owns the subappalto
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Scope for company
     */
    public function scopeByCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }
}

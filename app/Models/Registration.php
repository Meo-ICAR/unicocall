<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Wildside\Userstamps\HasUserstamps;

class Registration extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'registration_type',
        'value',
        'start_at',
        'end_at',
        'notes',
        'registrable_type',
        'registrable_id',
        'company_id',
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function registrable(): MorphTo
    {
        return $this->morphTo();
    }

    public function scopeByType($query, $type)
    {
        return $query->where('registration_type', $type);
    }

    public function scopeActive($query)
    {
        return $query->where(function ($q) {
            $q
                ->whereNull('end_at')
                ->orWhere('end_at', '>', now());
        });
    }

    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }
}

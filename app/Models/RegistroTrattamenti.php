<?php

namespace App\Models;

use App\Models\Client;
use App\Models\Company;
use App\Models\Employee;
use App\Models\Subappalti;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RegistroTrattamenti extends Model
{
    use HasFactory, SoftDeletes;

    protected $connection = 'mariadb';

    protected $fillable = [
        'company_id',
        'name',
        'approved_at',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Relationships
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id', 'id');
    }

    public function registroTrattamentiItems(): HasMany
    {
        return $this->hasMany(RegistroTrattamentiItem::class, 'company_id', 'company_id');
    }

    public function clients(): HasMany
    {
        return $this->hasMany(Client::class, 'company_id', 'company_id');
    }

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class, 'company_id', 'company_id');
    }

    public function subappaltiClienti(): HasMany
    {
        return $this
            ->hasMany(Subappalti::class, 'company_id', 'company_id')
            ->where('originator_type', 'company')
            ->where('sub_type', 'client');
    }

    public function subappaltiDipendenti(): HasMany
    {
        return $this
            ->hasMany(Subappalti::class, 'company_id', 'company_id')
            ->where('originator_type', 'company')
            ->where('sub_type', 'employee');
    }

    // Scopes
    public function scopeApproved($query)
    {
        return $query->whereNotNull('approved_at');
    }

    public function scopePending($query)
    {
        return $query->whereNull('approved_at');
    }

    public function scopeByCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    // Accessors
    public function getStatusAttribute(): string
    {
        return $this->approved_at ? 'Approved' : 'Pending';
    }

    public function getApprovedAtFormattedAttribute(): string
    {
        return $this->approved_at ? $this->approved_at->format('d/m/Y H:i') : 'Not approved';
    }

    // Mutators
    public function setApprovedAtAttribute($value)
    {
        $this->attributes['approved_at'] = $value ? now() : null;
    }

    protected static function booted()
    {
        static::creating(function ($registroTrattamenti) {
            if (auth()->check() && method_exists(auth()->user(), 'current_company_id')) {
                $registroTrattamenti->company_id = auth()->user()->current_company_id;
            }
        });
    }
}

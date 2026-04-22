<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Wildside\Userstamps\HasUserstamps;

class Company extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'name',
        'vat_number',
        'sponsor',
        'company_type',
        'is_iso27001_certified',
        'contact_email',
        'dpo_email',
        'page_header',
        'page_footer',
        'user_id',
        'smtp_host',
        'smtp_port',
        'smtp_encryption',
        'smtp_enabled',
        'smtp_verify_ssl',
        'payment_frequency',
        'payment',
        'payment_last_date',
        'payment_startup',
    ];

    protected $casts = [
        'is_iso27001_certified' => 'boolean',
        'smtp_enabled' => 'boolean',
        'smtp_verify_ssl' => 'boolean',
        'payment_last_date' => 'datetime',
        'payment' => 'decimal:2',
        'payment_startup' => 'decimal:2',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class)->withPivot('role')->withTimestamps();
    }

    public function clients()
    {
        return $this->hasMany(Client::class);
    }

    public function branches()
    {
        return $this->hasMany(Branch::class);
    }

    public function employees()
    {
        return $this->hasMany(Employee::class);
    }

    public function websites()
    {
        return $this->hasMany(Website::class);
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(Registration::class);
    }

    public function addresses(): MorphMany
    {
        return $this->morphMany(Address::class, 'addressable');
    }

    public function getMainAddressAttribute(): ?Address
    {
        return $this->addresses()->where('address_type_id', 5)->first();  // Sede Legale
    }

    public function scopeByCompanyType($query, $type)
    {
        return $query->where('company_type', $type);
    }

    public function scopeCertified($query)
    {
        return $query->where('is_iso27001_certified', true);
    }
}

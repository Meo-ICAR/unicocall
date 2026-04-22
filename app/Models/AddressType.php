<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
// use Wildside\Userstamps\HasUserstamps;

class AddressType extends Model
{
    /** @use HasFactory<\Database\Factories\AddressTypeFactory> */
    use HasFactory, SoftDeletes;  // , HasUserstamps;

    protected $fillable = [
        'name',
        'is_person',
    ];

    protected $casts = [
        'is_person' => 'boolean',
    ];

    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class);
    }

    public function scopeForPersons($query)
    {
        return $query->where('is_person', true);
    }

    public function scopeForCompanies($query)
    {
        return $query->where('is_person', false);
    }
}

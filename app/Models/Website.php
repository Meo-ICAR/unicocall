<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Website extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'is_typical' => 'boolean',
            'is_footercompilant' => 'boolean',
            'is_iso27001_certified' => 'boolean',
            'privacy_date' => 'date',
            'transparency_date' => 'date',
            'privacy_prior_date' => 'date',
            'transparency_prior_date' => 'date',
        ];
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class, 'clienti_id');
    }
}

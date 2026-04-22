<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SocialiteUser extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'is_personal' => 'boolean',
            'is_pec' => 'boolean',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

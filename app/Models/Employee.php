<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'hiring_date' => 'date',
            'termination_date' => 'date',
            'is_structure' => 'boolean',
            'is_ghost' => 'boolean',
        ];
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'company_branch_id');
    }

    public function supervisor()
    {
        return $this->belongsTo(Employee::class, 'coordinated_by_id');
    }

    public function subordinates()
    {
        return $this->hasMany(Employee::class, 'coordinated_by_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

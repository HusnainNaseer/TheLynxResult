<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Session extends Model
{
    protected $table = 'schoolsessions';
protected $fillable=[
    'erp_session_id',
    'title',
    't1_working_days',
    't2_working_days',
    'is_active',
    'active_lock',
];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}

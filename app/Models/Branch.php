<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    protected $fillable = [
        'erp_branch_id',
        'name',
        'email',
        'phone',
        'address',
        'principal_headmistress',
        'executive_director_islamabad',
        'is_active',
    ];
}

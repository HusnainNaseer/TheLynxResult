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
        'is_active',
    ];
}

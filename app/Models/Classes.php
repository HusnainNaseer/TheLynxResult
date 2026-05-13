<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Classes extends Model
{
    protected $table = 'classes';

    protected $fillable = [
        'erp_class_id',
        'name',
        'erp_branch_id',
        'owned_by',        // keep for backward compat
    ];
}
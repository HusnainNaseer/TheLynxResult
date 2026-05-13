<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    protected $table = 'sections';

    protected $fillable = [
        'erp_section_id',
        'class_id',
        'name',
        'owned_by',
        'erp_branch_id',
    ];
}
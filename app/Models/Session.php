<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Session extends Model
{
    protected $table = 'schoolsessions';
protected $fillable=[
    'title',
    't1_working_days',
    't2_working_days'
];
}

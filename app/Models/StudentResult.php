<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentResult extends Model
{
    use HasFactory;

    protected $fillable = [
    'name', 
    'class', 
    'section', 
    'rollno', 
    'session_id',
    'session_id',
    'attendance',
    't1_working_days',
    't2_working_days',
    'overall_grade', 
    'overall_percentage',
    'grand_term_one',
    'grand_term_two',
    'grand_total',
    'remarks',
    'created_by',
    'promoted_class',
];


    public function marks()
    {
        return $this->hasMany(StudentMarks::class, 'result_id', 'id');
    }

    public function session()
    {
        return $this->belongsTo(\App\Models\Session::class, 'session_id');
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentResult extends Model
{
    use HasFactory;

    protected $fillable = [
    'student_id',
    'name', 
    'class', 
    'section', 
    'rollno', 
    'session_id',
    'erp_session_id',
    'branch_id',
    'class_id',
    'section_id',
    'erp_student_id',
    'erp_class_id',
    'erp_section_id',
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
    'edit_by',
    'promoted_class',
    'workflow_status',
    'subject_finalized_by',
    'subject_finalized_at',
    'class_teacher_finalized_by',
    'class_teacher_finalized_at',
    'coordinator_approved_at',
];

    protected $casts = [
        'subject_finalized_at' => 'datetime',
        'class_teacher_finalized_at' => 'datetime',
        'coordinator_approved_at' => 'datetime',
    ];


    public function marks()
    {
        return $this->hasMany(StudentMarks::class, 'result_id', 'id');
    }

    public function session()
    {
        return $this->belongsTo(\App\Models\Session::class, 'session_id');
    }

    public function student()
    {
        return $this->belongsTo(\App\Models\Student::class, 'student_id');
    }
}

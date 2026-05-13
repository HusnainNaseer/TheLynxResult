<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeacherSubjectAssignment extends Model
{
    protected $table = 'teacher_subject_assignments';

    protected $fillable = [
        'teacher_id',
        'branch_id',
        'branch_name',
        'class_id',
        'class_name',
        'section_id',
        'section_name',
        'subject_id',
        'subject_name',
    ];

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }
}
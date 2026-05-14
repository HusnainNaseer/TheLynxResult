<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeacherSubjectAssignment extends Model
{
    protected $table = 'teacher_subject_assignments';

    protected $fillable = [
        'teacher_id',
        'session_id',
        'erp_session_id',
        'branch_id',
        'branch_name',
        'class_id',
        'class_name',
        'section_id',
        'section_name',
        'subject_id',
        'subject_name',
        'assigned_by',
    ];

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function session(): BelongsTo
    {
        return $this->belongsTo(Session::class, 'session_id');
    }
}

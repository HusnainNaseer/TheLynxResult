<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Student extends Model
{
    protected $fillable = [
        'session_id',
        'erp_session_id',
        'erp_student_id',
        'erp_class_id',
        'erp_section_id',
        'section_name',
        'rollno',
        'stdname',
        'fathername',
        'phone_no',
        'owned_by',
    ];

    public function session(): BelongsTo
    {
        return $this->belongsTo(Session::class, 'session_id');
    }

    public function result(): HasOne
    {
        return $this->hasOne(StudentResult::class, 'student_id')->latestOfMany();
    }
}

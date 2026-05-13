<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClassSubject extends Model
{
    protected $table = 'class_subjects';

    protected $fillable = [
        'branch_id',
        'class_id',
        'subject_id',
    ];

    public function class(): BelongsTo
    {
        return $this->belongsTo(Classes::class, 'class_id');
    }
}
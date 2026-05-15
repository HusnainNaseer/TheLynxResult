<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubjectWiseMarks extends Model
{
    protected $fillable=[
        'subject_name',
        'session_id',
        'erp_session_id',
        'branch_id',
        'term_one_marks',
        'term_two_marks',
        'created_by'
    ];
    public function session()
{
    return $this->belongsTo(Session::class, 'session_id'); // session_id must exist in results table
}

}

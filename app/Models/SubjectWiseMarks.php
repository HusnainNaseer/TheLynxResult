<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubjectWiseMarks extends Model
{
    protected $fillable=[
        'subject_name',
        'term_one_mark',
        'term_two_mark',
        'created_by'
    ];
    public function session()
{
    return $this->belongsTo(Session::class, 'session_id'); // session_id must exist in results table
}

}

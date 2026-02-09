<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentMarks extends Model
{
    protected $fillable = [
        'result_id',
        'subject_id',
        'term_one_mark',
        'term_one_grade',
        'term_one_percent',
        'term_one_total',
        'term_two_mark',
        'term_two_grade',
        'term_two_percent',
        'term_two_total',
        'grand_term_one',
        'grand_term_two',
        'grand_total',
        'working_term_one',
        'working_term_two',
        'working_total',
        'remarks'
    ];

    // public function subject()
    // {
    //     return $this->belongsTo(SubjectWiseMarks::class, 'subject_id', 'id');
    // }
    public function subject()
{
    return $this->belongsTo(\App\Models\SubjectWiseMarks::class, 'subject_id');
}

}


<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClassSection extends Model
{
    /*
     * The migration creates the table as "class_sections".
     * This must match exactly — wrong table name = no data ever loads.
     */
    protected $table = 'classsections';

    protected $fillable = [
        'class_id',
        'section_id',
        'erp_class_id',
        'erp_section_id',
    ];

    public function class(): BelongsTo
    {
        return $this->belongsTo(Classes::class, 'class_id');
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class, 'section_id');
    }
}
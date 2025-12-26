<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CurriculumDetail extends Model
{
    protected $table = 'curriculum_details';
    protected $fillable = [
        'curriculum_id',
        'sub_subject_type',
        'week',
        'lesson_number',
        'lesson_name',
        'note',
    ];
    
    public function curriculum()
    {
        return $this->belongsTo(Curriculum::class, 'curriculum_id', 'id');
    }
}

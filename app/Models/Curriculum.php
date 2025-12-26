<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Curriculum extends Model
{
    protected $table = 'curriculums';
    protected $fillable = [
        'academic_year',
        'subject_name',
        'grade',
    ];
    public function details()
    {
        return $this->hasMany(CurriculumDetail::class, 'curriculum_id', 'id');
    }
    public function getGradeNameAttribute()
    {
        return $this->grade ? $this->grade : '';
    }
}

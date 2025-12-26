<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Curriculum extends Model
{
    protected $table = 'curriculums';
    protected $fillable = [
        'academic_year',
        'department_id',
        'grade',
    ];
    const ACTIVE    = 1;
    const INACTIVE  = 0;
    
    public function details()
    {
        return $this->hasMany(CurriculumDetail::class, 'curriculum_id', 'id');
    }
    
    public function department()
    {
        return $this->belongsTo(Department::class);
    }
    
    public function getGradeNameAttribute()
    {
        return $this->grade ? $this->grade : '';
    }
}

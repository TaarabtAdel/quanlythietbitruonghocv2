<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Curriculum extends Model
{
    protected $table = 'curriculums';
    protected $fillable = [
        'academic_year',//năm học   
        'department_id',//Môn học
        'grade',//khối
        'subject_type',//Phân môn
        'note',//Ghi chú
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

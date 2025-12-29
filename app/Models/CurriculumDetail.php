<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CurriculumDetail extends Model
{
    protected $table = 'curriculum_details';
    protected $fillable = [
        'curriculum_id',//Chương trình đào tạo
        'week',//Tuần PPCT
        'lesson_number',//Số tiết PPCT
        'lesson_name',//Tên bài học
        'note',//Ghi chú
    ];
    
    public function curriculum()
    {
        return $this->belongsTo(Curriculum::class, 'curriculum_id', 'id');
    }
}

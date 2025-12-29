<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Curriculum extends Model
{
    use SoftDeletes;
    protected $table = 'curriculums';
    protected $fillable = [
        'academic_year',//năm học   
        'department_id',//Môn học
        'grade',//khối
        'subject_type',//Phân môn
        'note',//Ghi chú
        'status',//Trạng thái
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
    //status_fm
    public function getStatusFmAttribute()
    {
        return $this->status == self::ACTIVE ? '<span class="lable-table bg-success-subtle text-success rounded border border-success-subtle font-text2 fw-bold">'.__('sys.active').'</span>' : '<span class="lable-table bg-danger-subtle text-danger rounded border border-danger-subtle font-text2 fw-bold">'.__('sys.inactive').'</span>';
    }
    public static function copyItem($id)
    {
        $originalItem = self::findOrFail($id);
        $data = $originalItem->attributes;
        $data['created_at'] = now();
        $data['updated_at'] = now();
        $data['deleted_at'] = null;
        $item = self::create($data);
        $item->details()->createMany($originalItem->details->map(function ($detail) use ($item) {
            return [
                'curriculum_id' => $item->id,
                'week' => $detail->week,
                'lesson_number' => $detail->lesson_number,
                'lesson_name' => $detail->lesson_name,
                'note' => $detail->note,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }));
    }
}

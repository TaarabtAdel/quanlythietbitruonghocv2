<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Curriculum extends AdminModel
{
    use HasFactory;
    
    protected $table = "curricula";
    
    protected $fillable = [
        'name',
        'code',
        'description',
        'department_id',
        'user_id',
        'deleted_at'
    ];

    /**
     * Mối quan hệ: Một chương trình đào tạo thuộc về một bộ môn
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Mối quan hệ: Một chương trình đào tạo thuộc về một người dùng
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Mối quan hệ: Một chương trình đào tạo có nhiều chi tiết
     */
    public function details()
    {
        return $this->hasMany(CurriculumDetail::class)->orderBy('order');
    }

    public function getStatusFmAttribute(){
        if ($this->deleted_at) {
            return '<span class="lable-table bg-danger-subtle text-danger rounded border border-danger-subtle font-text2 fw-bold">'.__('sys.inactive').'</span>';
        }else{
            return '<span class="lable-table bg-success-subtle text-success rounded border border-success-subtle font-text2 fw-bold">'.__('sys.active').'</span>';
        }
    }
}


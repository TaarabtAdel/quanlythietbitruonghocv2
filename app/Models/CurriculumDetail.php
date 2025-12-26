<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CurriculumDetail extends Model
{
    use HasFactory;
    
    protected $table = "curriculum_details";
    
    protected $fillable = [
        'curriculum_id',
        'subject_name',
        'credits',
        'hours',
        'semester',
        'order',
        'note'
    ];

    /**
     * Mối quan hệ: Một chi tiết chương trình đào tạo thuộc về một chương trình đào tạo
     */
    public function curriculum()
    {
        return $this->belongsTo(Curriculum::class);
    }
}


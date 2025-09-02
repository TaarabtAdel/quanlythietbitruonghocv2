<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Asset extends AdminModel
{
    use HasFactory;
    protected $table = 'assets';
    protected $fillable = ['id','device_type_id','name', 'quantity','image','department_id','price','country','year','unit','note','deleted_at'];
    
    public static function handleSearch($request,$query){
        $query->orderBy('name','ASC');
        return $query;
    }
    public function devicetype()
    {
        return $this->belongsTo(DeviceType::class,'device_type_id','id');
    }
    public function department()
    {
        return $this->belongsTo(Department::class);
    }
    // Fix lỗi hình ảnh
    public function getImageAttribute($value)
    {
        if ($value == '') {
            return asset('uploads/default_image.png'); // Đường dẫn đến hình ảnh mặc định
        }
        return $value;
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Device extends AdminModel
{
    use HasFactory;
    protected $table ='devices';
    protected $fillable = [
        'id',
        'device_type_id',
        'name',
        'quantity',
        'image',
        'department_id',
        'price',
        'country_name',
        'year',
        'unit',
        'note',
        'deleted_at'
    ];

    // Ovrrides
    public static function handleSearch($request,$query){
        $query->orderBy('name','ASC');
        return $query;
    }

    public function borrows()
    {
        return $this->belongsToMany(Borrow::class,'borrow_devices','device_id','borrow_id');
    }
    public function devicetype()
    {
        return $this->belongsTo(DeviceType::class,'device_type_id','id');
    }
    // Fix lỗi hình ảnh
    public function getImageAttribute($value)
    {
        if ($value == '') {
            return asset('uploads/default_image.png'); // Đường dẫn đến hình ảnh mặc định
        }
        return $value;
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
    public function getDeviceTypeNameAttribute()
    {
        return $this->devicetype? $this->devicetype->name : '';
    }
    public function getDepartmentNameAttribute()
    {
        return $this->department? $this->department->name : '';
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Department extends AdminModel
{
    use HasFactory;
    
    protected $table = "departments";
    protected $fillable = [
        'name','deleted_at'
        // các thuộc tính fillable khác
    ];
    public static function handleSearch($request,$query){
        $query->orderBy('name','ASC');
        return $query;
    }
    public function devices()
    {
        return $this->hasMany(Device::class);
    }
    public function assets()
    {
        return $this->hasMany(Asset::class);
    }
}
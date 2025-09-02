<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Lab extends AdminModel
{
    use HasFactory;
    protected $fillable = ['id','name', 'quantity','department_id','deleted_at'];

    public static function handleSearch($request,$query){
        $query->orderBy('name','ASC');
        return $query;
    }
    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id', 'id');
    }
}
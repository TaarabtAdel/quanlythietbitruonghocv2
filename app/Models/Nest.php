<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Nest extends AdminModel
{
    use HasFactory;
    protected $table = "nests";
    protected $fillable = [
        'name','deleted_at'
    ];
    public static function handleSearch($request,$query){
        $query->orderBy('name','ASC');
        return $query;
    }
    public function users()
    {
        return $this->hasMany(User::class);
    }
}

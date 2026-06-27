<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Document extends AdminModel
{
    use HasFactory;
    protected $table = "documents";
    protected $fillable = [
        'name','slug','image','description','source','sgd_document_id','deleted_at'
    ];
    public static function handleSearch($request,$query){
        $query->orderBy('name','ASC');
        return $query;
    }
}

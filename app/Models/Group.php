<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends AdminModel
{
    use HasFactory;
    protected $table ='groups';
    protected $fillable = [
        'name','deleted_at'
    ];

    public function users()
    {
        return $this->hasMany(User::class, 'group_id', 'id');
    }
    public function roles()
    {
        return $this->belongsToMany(Role::class,'groups_roles','group_id','role_id');
    }
}

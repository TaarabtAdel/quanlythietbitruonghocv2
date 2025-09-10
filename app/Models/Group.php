<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends AdminModel
{
    use HasFactory;
    protected $table ='groups';
    protected $fillable = [
        'name',
        'deleted_at',
    ];

    public function users()
    {
        return $this->hasMany(User::class, 'group_id', 'id');
    }
    public function roles()
    {
        return $this->belongsToMany(Role::class,'groups_roles','group_id','role_id');
    }
    public function getStatusFmAttribute(){
        if ($this->deleted_at) {
            return '<span class="lable-table bg-danger-subtle text-danger rounded border border-danger-subtle font-text2 fw-bold">'.__('sys.inactive').'</span>';
        }else{
            return '<span class="lable-table bg-success-subtle text-success rounded border border-success-subtle font-text2 fw-bold">'.__('sys.active').'</span>';
        }
    }
}

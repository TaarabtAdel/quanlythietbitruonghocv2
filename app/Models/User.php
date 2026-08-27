<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    const ACTIVE    = 1;
    const INACTIVE  = 0;
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'address',
        'phone',
        'gender',
        'image',
        'group_id',
        'nest_id',
        'birthday',
        'deleted_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function hasPermission($name){
        return true;
    }

    public function getConnectionName()
    {
        if (app()->bound('campus.auth_on_main') && app('campus.auth_on_main')) {
            return 'school_main';
        }

        return parent::getConnectionName();
    }
    public function CanManagerSchool(){
        return true;
    }
    public function CanManagerImport(){
        return true;
    }
    public function CanManagerExport(){
        return true;
    }

    /**
     * Sắp theo tên gọi (từ cuối), rồi full name — "Nguyễn Văn An" → "An".
     * MySQL: SUBSTRING_INDEX(name, ' ', -1).
     */
    public function scopeOrderByGivenName($query, string $direction = 'asc')
    {
        $direction = strtolower($direction) === 'desc' ? 'desc' : 'asc';
        $sqlDir = $direction === 'desc' ? 'DESC' : 'ASC';

        return $query
            ->orderByRaw("SUBSTRING_INDEX(name, ' ', -1) {$sqlDir}")
            ->orderBy('name', $direction);
    }

    public function nest()
    {
        return $this->belongsTo(Nest::class);
    }
    public function group()
    {
        return $this->belongsTo(Group::class);
    }
    public function getStatusFmAttribute(){
        if ($this->deleted_at) {
            return '<span class="lable-table bg-danger-subtle text-danger rounded border border-danger-subtle font-text2 fw-bold">'.__('sys.inactive').'</span>';
        }else{
            return '<span class="lable-table bg-success-subtle text-success rounded border border-success-subtle font-text2 fw-bold">'.__('sys.active').'</span>';
        }
    }
}

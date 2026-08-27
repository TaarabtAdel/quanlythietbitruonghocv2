<?php

namespace App\Models;

class Campus extends MainModel
{
    protected $connection = 'school_main';

    protected $table = 'campuses';

    protected $fillable = [
        'name',
        'database_name',
        'sort_order',
        'deleted_at',
    ];

    public static function ensureSchema(): void
    {
        \App\Models\Versions\Ver31::updateDatabase();
    }

    public function getStatusFmAttribute()
    {
        if ($this->deleted_at) {
            return '<span class="lable-table bg-danger-subtle text-danger rounded border border-danger-subtle font-text2 fw-bold">'.__('sys.inactive').'</span>';
        }

        return '<span class="lable-table bg-success-subtle text-success rounded border border-success-subtle font-text2 fw-bold">'.__('sys.active').'</span>';
    }
}

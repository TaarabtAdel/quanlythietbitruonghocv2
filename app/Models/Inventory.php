<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inventory extends AdminModel
{
    protected $fillable = [
        'name', 'date', 'user_id', 'note'
    ];

    public function items()
    {
        return $this->hasMany(InventoryItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

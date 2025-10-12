<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryItem extends Model
{
    protected $fillable = [
        'inventory_id', 'device_id', 'status', 'note', 'user_id', 'checked_at'
    ];

    public function inventory()
    {
        return $this->belongsTo(Inventory::class);
    }

    public function device()
    {
        return $this->belongsTo(Device::class);
    }

    public function checker()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BorrowDeviceFake extends Model
{
    use HasFactory;
    protected $fillable = [
        'borrow_id',
        'device_name',
        'room_id',
        'quantity',
        'borrow_date',
        'return_date',
        'lecture_name',
        'lesson_name',
        'session',
        'lecture_number',
        'tiet',
        'lab_id',
    ];

    protected $dates = [
        'borrow_date',
        'return_date',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'lecture_number' => 'integer',
        'tiet' => 'integer',
    ];
}

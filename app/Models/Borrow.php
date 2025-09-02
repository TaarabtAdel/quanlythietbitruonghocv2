<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Borrow extends Model
{
    use HasFactory;
    protected $table ='borrows';
    use HasFactory;
    const ACTIVE    = 1;
    const INACTIVE  = 0;
    const DRAFT     = -1;
    const CANCELED     = -2;
    
    protected $fillable = ['id', 'user_id', 'borrow_date','created_at','updated_at','deleted_at','borrow_note','status','approved'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function the_devices()
    {
        return $this->hasMany(BorrowDevice::class, 'borrow_id', 'id');
    }

    public function devices()
    {
        return $this->belongsToMany(Device::class,'borrow_devices','borrow_id','device_id');
    }
    public function the_rooms()
    {
        return $this->belongsToMany(Room::class,'borrow_devices','borrow_id','room_id');
    }
    public static function getStartEndDateFromWeek($week){
        $year           = substr($week, 0, 4);
        $weekNumber     = substr($week, -2);
        $startDate      = Carbon::now()->setISODate($year, $weekNumber)->startOfWeek();
        $endDate        = Carbon::now()->setISODate($year, $weekNumber)->endOfWeek();
        return [
            'startDate' => $startDate,
            'endDate' => $endDate
        ];
    }
    public static function getStartEndDateFromYear($school_years){
        $yearRange = explode('-', $school_years);
        $startDate = $endDate = '';
        if (count($yearRange) == 2) {
            $startYear = trim($yearRange[0]);
            $endYear = trim($yearRange[1]);
            // Tính toán ngày bắt đầu và ngày kết thúc dựa vào năm học
            $startDate  = $startYear . '-08-01'; // Năm học bắt đầu từ tháng 8
            $endDate    = $endYear . '-07-01'; // Năm học kết thúc vào tháng 7 năm sau
        }
        return [
            'startDate' => $startDate,
            'endDate' => $endDate
        ];
    }

}
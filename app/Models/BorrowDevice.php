<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class BorrowDevice extends Model
{
    use HasFactory;
    protected $table ='borrow_devices';
    use HasFactory;
    protected $fillable = ['id', 'borrow_id', 'device_id','room_id','quantity','borrow_date','return_date','lecture_name','lesson_name','session','image_last','image_first','status','lecture_number','lab_id','tiet'];
    public function borrow()
    {
        return $this->belongsTo(Borrow::class, 'borrow_id', 'id');
    }

    public function device()
    {
        return $this->belongsTo(Device::class, 'device_id', 'id');
    }

    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id', 'id');
    }
    public function user() 
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function lab() 
    {
        return $this->belongsTo(Lab::class, 'lab_id');
    }

    // Custom methods
    public static function getItems($request = null,$table = ''){
        $limit = $request->limit ?? 100;
        $query = self::query(true);
        $query->whereHas('borrow', function ($query){
            $query->where('status','>=',0);
        });
        if( $request->lab_id){
            $query->where('lab_id', $request->lab_id );
        }
        if( $request->session){
            $query->where('session', $request->session == 'AM' ? 'Sáng' : 'Chiều' );
        }
        if( $request->user_id){
            $query->whereHas('borrow', function ($query) use ($request) {
                $query->where('user_id', $request->user_id );
            });
        }
        if($request->nest_id){
            $query->whereHas('borrow.user', function ($query) use ($request) {
                $query->where('nest_id', $request->nest_id );
            });
        }

        if( $request->borrow_date){
            $query->whereHas('borrow', function ($query) use ($request) {
                $query->whereDate('borrow_date', $request->borrow_date );
            });
        }
        if ($request->school_years) {
            $startDateEndDate = Borrow::getStartEndDateFromYear($request->school_years);
            $query->whereHas('borrow', function ($query) use ($startDateEndDate) {
                $query->whereBetween('borrow_date', $startDateEndDate);
            });
        }
        if( $request->week ){
            $startDateEndDate = Borrow::getStartEndDateFromWeek($request->week);
            $query->whereHas('borrow', function ($query) use ($startDateEndDate) {
                $query->whereBetween('borrow_date', $startDateEndDate);
            });
        }

        $query->orderBy('borrow_date', 'asc')
        ->orderByRaw("CASE WHEN session = 'Sáng' THEN 1 WHEN session = 'Chiều' THEN 2 END")
        ->orderBy('lecture_number', 'asc');
        $items = $query->get();
        $items = self::groupBorrowDevices($items);
        return $items;
    }

    // Gom nhóm các thiết bị
    public static function groupBorrowDevices($items){

        $nitems = [];
        foreach( $items as $BorrowDevice ){
            $nitems[$BorrowDevice->borrow_date.'-'.$BorrowDevice->room_id.'-'.Str::slug($BorrowDevice->lesson_name).'-'.$BorrowDevice->session.'-'.$BorrowDevice->lecture_number][] = $BorrowDevice;
        }
        $items = [];
        foreach( $nitems as $item ){
            $departmentName = '';
            $lab_name = '';
            if( empty($item[0]) ){
                continue;
            }
            $device_names = [];
            foreach( $item as $key => $device_item ){
                if(empty($lab_name)){
                    $lab_name = $device_item->lab->name ?? '';
                }
                if(@$device_item->device->name){
                    $device_names[$key] = '- '.@$device_item->device->name . ' ('. $device_item->quantity .')';
                }
                if (empty($departmentName)) {
                    $departmentName = @$device_item->device->department->name;
                }
            }
            $device_names = implode(' <br> ', $device_names);
            $items[] = [
                'borrow_date' => $item[0]->borrow ? date('d/m/Y',strtotime($item[0]->borrow->borrow_date)) : '',
                'return_date' => $item[0]->return_date ? date('d/m/Y',strtotime($item[0]->return_date)) : '',
                'created_at' => $item[0]->created_at ? date('d/m/Y',strtotime($item[0]->created_at)) : '',
                'device_name' => $device_names,
                'quantity' => $item[0]->quantity,
                'session' => $item[0]->session,
                'lecture_name' => $item[0]->lecture_name,
                'lesson_name' => $item[0]->lesson_name,
                'lecture_number' => $item[0]->lecture_number,
                'room_name' => $item[0]->room->name ?? '',
                'user_name' => $item[0]->borrow->user->name ?? '',
                'nest_name' => $item[0]->borrow->user->nest->name ?? '',
                'borrow_note' => $item[0]->borrow->borrow_note ?? '',
                'tiet'    => $item[0]->tiet ?? '',
                'department'    => $departmentName, // Sử dụng giá trị đơn lẻ
                'lab_name'      => $lab_name, // Sử dụng giá trị đơn lẻ
            ];
        }
        return $items;
    }
}
<?php

namespace App\Models;

// use App\Models\AdminModel as Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Device;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Str;
class BorrowLab extends Model
{
    use HasFactory;
    protected $table = 'borrow_devices';
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        "lesson_name",
        "session",
        "lecture_name",
        "room_id",
        "lecture_number",
        "device_id",
        "borrow_date",
        "quantity",
        "lab_id",
        "tiet",
        "deleted_at",
    ];
    
    // Ovrrides
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
        if( $request->borrow_date){
            $query->whereHas('borrow', function ($query) use ($request) {
                $query->whereDate('borrow_date', $request->borrow_date );
            });
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

        $query
        ->orderBy('borrow_date', 'asc')
        ->orderByRaw("CASE WHEN session = 'Sáng' THEN 1 WHEN session = 'Chiều' THEN 2 END ASC")
        ->orderBy('tiet', 'asc');

        $query->whereHas('borrow.user', function ($query) use ($request) {
                $query->where('id', 95 );
            });
        $items = $query->get();

        $nitems = [];
        foreach( $items as $BorrowDevice ){
            if(!$BorrowDevice->lab_id) continue;
            // $nitems[$BorrowDevice->borrow->borrow_date.'-BR'.$BorrowDevice->borrow_id.'-R'.$BorrowDevice->room_id.'-B'.$BorrowDevice->session.'-TKB'.$BorrowDevice->lecture_number.'-P'.$BorrowDevice->lab_id] = $BorrowDevice;
            $session = $BorrowDevice->session == 'Sáng' ? 'AM' : 'PM';
            $borrow_date = $BorrowDevice->borrow->borrow_date;
            $nitems[$borrow_date.'-'.$session.'-'.$BorrowDevice->lecture_number.'-'.$BorrowDevice->room_id.'-'.$BorrowDevice->borrow_id.'-'.$BorrowDevice->lab_id] = $BorrowDevice;
        }
        $nitems = collect($nitems)->sortKeys();
        return $nitems;
    }
    public function room()
    {
        return $this->belongsTo(\App\Models\Room::class, 'room_id', 'id');
    }
    public function device()
    {
        return $this->belongsTo(\App\Models\Device::class, 'device_id', 'id');
    }
    public function borrow()
    {
        return $this->belongsTo(\App\Models\Borrow::class, 'borrow_id', 'id');
    }
    public function lab()
    {
        return $this->belongsTo(\App\Models\Lab::class, 'lab_id', 'id');
    }
    // Lấy danh sách phòng mượn theo tuần
    public static function getBorrowedLab($request){
        $items = [];
        if( $request->week && $request->lab_id ){
            $startDayEndDate = Borrow::getStartEndDateFromWeek($request->week);
            $periods = CarbonPeriod::create($startDayEndDate['startDate'],$startDayEndDate['endDate']);
            foreach ($periods as $date) {
                $date = $date->format('Y-m-d');
                $items[$date] = [];
                for($i = 1; $i <= 10; $i++){
                    $tietTKB = $i;
                    $session = 'Sáng';
                    if( $i > 5 ){
                        $session = 'Chiều';
                        $tietTKB = $i - 5;
                    }
                    $borrow_labs = BorrowDevice::select(['borrow_id','lab_id','session','lecture_number'])->where('borrow_date',$date);
                    $borrow_labs->where('lecture_number',$tietTKB);
                    $borrow_labs->where('session',$session);
                    $borrow_labs->where('lab_id','>',0);

                    if($request->session){
                        $session = $request->session == 'AM' ? 'Sáng' : 'Chiều';
                        $borrow_labs->where('session',$session);
                    }
                    if($request->lab_id){
                        $borrow_labs->where('lab_id',$request->lab_id);
                    }
                    if($request->department_id){
                        $borrow_labs->whereHas('device',function($query) use($request){
                            $query->where('department_id',$request->department_id);
                        });
                    }
                    if($request->user_id){
                        $borrow_labs->whereHas('borrow.user',function($query) use($request){
                            $query->where('user_id',$request->user_id);
                        });
                    }
                    $borrow_labs->whereHas('borrow',function($query) use($request){
                        $query->whereIn('status',[Borrow::ACTIVE,Borrow::INACTIVE]);
                    });

                    $borrow_labs = $borrow_labs->get();

                    $labs = [];
                    foreach( $borrow_labs as $borrow_lab ){
                        $labs = [
                            'borrow_id' => $borrow_lab->borrow_id,
                            'lab_id'    => $borrow_lab->lab_id,
                            'session'    => $borrow_lab->session,
                            'lecture_number'    => $borrow_lab->lecture_number,
                            'lab_name'  => $borrow_lab->lab->name ?? '',
                            'user_name'  => $borrow_lab->borrow->user->name ?? '',
                        ];
                    }
                    $items[$date][$i] = $labs;
                }
            }
        }
        return $items;
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

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

    // Teacher methods

    // Ovrrides
    public static function updateItem($id,$request){
    }
    public static function deleteItem($id){
    }
    // Relationships
    public function borrow_devices(){
        return $this->hasMany(BorrowDevice::class);
    }

    // Attributes
    public static function copyItem($id){
    }
    public function getBorrowItemsAttribute(){
        $item = self::findItem($this->id);
        $results = [];
        if( count($item->borrow_devices) ){
            foreach( $item->borrow_devices as $borrow_device ){
                $results[$borrow_device->tiet][] = $borrow_device;
            }
        }
        return $results;
    }
    public function getBorrowDateFmAttribute(){
        return $this->borrow_date ?  date('d-m-Y',strtotime($this->borrow_date)) : '';
    }
    public function getCreatedAtFmAttribute(){
        return $this->created_at ?  date('d-m-Y H:i',strtotime($this->created_at)) : '';
    }
    public function getUserNameAttribute(){
        return $this->user->name ?? 'Chưa xác định';
    }
    public function getNumberDevicesAttribute(){
        return $this->borrow_devices ? $this->borrow_devices->count() : 0;
    }
    public function getLabNamesAttribute(){
        $lab_ids = $this->borrow_devices->pluck('lab_id','lab_id');
        $names = '';
        if($lab_ids){
            $lab_ids = $lab_ids->toArray();
            $labs = \App\Models\Lab::whereIn('id',$lab_ids)->pluck('name')->toArray();
            $names = implode('<br>',$labs);
        }
        return $names;
    }
    public function getDeviceNamesAttribute(){
        $device_ids = $this->borrow_devices->pluck('device_id','device_id');
        $names = '';
        if($device_ids){
            $device_ids = $device_ids->toArray();
            $labs = \App\Models\Device::whereIn('id',$device_ids)->pluck('name')->toArray();
            $names = implode('<br>',$labs);
        }
        return $names;
    }
    public function getStatusFmAttribute(){
        switch ($this->status) {
            case self::DRAFT:
                return '<span class="lable-table bg-danger-subtle text-danger rounded border border-danger-subtle font-text2 fw-bold">Phiếu Nháp</span>';
                break;
            case self::ACTIVE:
                return '<span class="lable-table bg-success-subtle text-success rounded border border-success-subtle font-text2 fw-bold">Đã Duyệt</span>';
                break;
            case self::INACTIVE:
                return '<span class="lable-table bg-warning-subtle text-warning rounded border border-warning-subtle font-text2 fw-bold">Chờ Duyệt</span>';
                break;
            case self::CANCELED:
                return '<span class="lable-table bg-dark-subtle text-warning rounded border border-dark-subtle font-text2 fw-bold">Đã Hủy</span>';
                break;
        }
    }
    public function getCanEditAttribute(){
        $permissions = self::getPermissions();
        if(
            ($this->status == self::ACTIVE && $permissions['allow_edit_approved']) ||
            ($this->status == self::INACTIVE && $permissions['allow_edit_pending']) ||
            $this->status == self::DRAFT ||
            $this->status == self::CANCELED 
        ){
            return true;
        }
        return false;
    }
    public function getCanDeleteAttribute(){
        $permissions = self::getPermissions();
        if(
            ($this->status == self::ACTIVE && $permissions['allow_delete_approved']) ||
            ($this->status == self::INACTIVE && $permissions['allow_delete_pending']) ||
            $this->status == self::DRAFT ||
            $this->status == self::CANCELED 
        ){
            return true;
        }
        return false;
    }

    // Custom methods
    public static function getBorrowedLabs($request){
        $items = [];
        if( $request->week ){
            $startDayEndDate = self::getStartEndDateFromWeek($request->week);
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
                        $query->where('status','>=',0);
                    });

                    $borrow_labs = $borrow_labs->get();

                    $labs = [];
                    foreach( $borrow_labs as $borrow_lab ){
                        $labs[] = [
                            'borrow_id' => $borrow_lab->borrow_id,
                            'lab_id'    => $borrow_lab->lab_id,
                            'session'    => $borrow_lab->session,
                            'lecture_number'    => $borrow_lab->lecture_number,
                            'lab_name'  => $borrow_lab->lab->name ?? '',
                            'user_name'  => $borrow_lab->borrow->user->name ?? '',
                        ];
                    }
                    $items[$date][$i] = [
                        'labs' => $labs
                    ];

                }
            }
        }
        return $items;
    }
    // Lấy danh sách phòng mượn theo tuần
    public static function getBorrowedLab($request){
        $items = [];
        if( $request->week && $request->lab_id ){
            $startDayEndDate = self::getStartEndDateFromWeek($request->week);
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
                        $query->where('status',self::ACTIVE);
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
    public static function getPermissions(){
        $permissions = [
            'allow_edit_approved' => \App\Models\Option::get_option('borrow_device','allow_edit_approved',0),
            'allow_edit_pending' => \App\Models\Option::get_option('borrow_device','allow_edit_pending',0),
            'allow_delete_approved' => \App\Models\Option::get_option('borrow_device','allow_delete_approved',0),
            'allow_delete_pending' => \App\Models\Option::get_option('borrow_device','allow_delete_pending',0),
            'auto_approved' => \App\Models\Option::get_option('borrow_device','auto_approved',0),
            'check_duplicate' => \App\Models\Option::get_option('borrow_device','check_duplicate',0)
        ];
        return $permissions;
    }

    public static function get_borrow_purposes(){
        $option = \App\Models\Option::where('option_name','app_verison')->first();
        $currentVersion = $option->option_value ?? '1.0';
        if($currentVersion >= '2.3'){
            // Từ phiên bản 2.3 trở lên đã thay thế trong admin bảng borrow_purposes
            $borrow_purposes = \App\Models\BorrowPurpose::all()->pluck('name','slug')->toArray();
            return $borrow_purposes ?? self::PURPOSE;
        }
        return self::PURPOSE;
    }

}
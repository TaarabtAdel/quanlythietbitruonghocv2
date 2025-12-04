<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class Borrow extends MainModel
{
    use HasFactory;
    protected $table ='borrows';
    use SoftDeletes;
    const ACTIVE    = 1;
    const INACTIVE  = 0;
    const DRAFT     = -1;
    const CANCELED     = -2;
    const PURPOSE_EXPORT = [
        ''              => '',
        'phong_bo_mon'  => 'PBM',
        'day_cntt'      => 'DẠY CNTT',
        'sd_tbth'       => 'SD TBTH',
        'phong_dcn'     => 'P.ĐCN',
        'other'         => 'KHÁC',
    ];
    const PURPOSE = [
        ''              => 'Chưa xác định',
        'phong_bo_mon'  => 'ĐK Phòng Bộ Môn',
        'day_cntt'      => 'ĐK Dạy CNTT',
        'sd_tbth'       => 'ĐK Sử Dụng TB Thực Hành',
        'phong_dcn'     => 'ĐK Phòng Đa Chức Năng',
        'other'         => 'ĐK Khác'
    ];
    const RETURN_STATUS = [
        ''              => '-',
        'no'            => 'Chưa trả',
        'part'          => 'Trả một phần',
        'full'          => 'Trả đủ'
    ];

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
    public static function getStartEndDateFromYear($school_years)
    {
        $yearRange = explode('-', $school_years);
        $startDate = $endDate = null;
        if (count($yearRange) == 2) {
            $startYear = trim($yearRange[0]);
            $endYear   = trim($yearRange[1]);
            // Năm học bắt đầu từ 01/08 (tháng 8)
            $startDate = Carbon::createFromDate($startYear, 8, 1)->startOfDay();
            // Năm học kết thúc vào 01/07 năm kế tiếp
            $endDate   = Carbon::createFromDate($endYear, 7, 1)->endOfDay();
        }
        return [
            'startDate' => $startDate,
            'endDate'   => $endDate,
        ];
    }
    // Teacher methods

    // Ovrrides
    public static function updateItem($id,$request){
        $item = self::findItem($id);
        if($request->borrow_date){
            $item->borrow_date = $request->borrow_date;
        }
        if( isset($request->borrow_purpose) ){
            $item->borrow_purpose = $request->borrow_purpose;
        }
        if($request->status !== ''){
            $item->status = $request->status;
        }
        if( isset($request->borrow_note) ){
            $item->borrow_note = $request->borrow_note;
        }
        if( isset($request->is_returned) ){
            $item->is_returned = $request->is_returned;
        }
        $item->save();

        
        // Xóa tiết dạy
        if( $request->task == 'delete-tiet' && $request->tiet !== NULL ){
            $tiet = $request->tiet;
            $item->borrow_devices()->where('tiet',$tiet)->delete();
        }

        // Thay đổi số lượng thiết bị
        if( $request->task == 'change-qty-device' && $request->tiet !== NULL && $request->device_id !== NULL ){
            $qty = $request->qty;
            $tiet = $request->tiet;
            $device_id = $request->device_id;
            $borrow_devices = $item->borrow_devices()
            ->where('tiet',$tiet)
            ->where('device_id',$device_id)
            ->update([
                'quantity' => $qty
            ]);
        }
        // Xóa thiết bị
        if( $request->task == 'delete-device' && $request->tiet !== NULL && $request->device_id !== NULL ){
            $tiet = $request->tiet;
            $device_id = $request->device_id;
            $borrow_devices = $item->borrow_devices()->where('tiet',$tiet);
            if($borrow_devices->count() > 1){
                $borrow_devices->where('device_id',$device_id)->delete();
            }else{
                $borrow_devices->update([
                    'device_id' => 0
                ]);
            }
        }
        // Chọn phòng bộ môn
        if( $request->devices && in_array($request->task,['add-lab','show-labs']) ){
            foreach( $request->devices as $tiet => $device ){
                $borrow_devices = $item->borrow_devices()->where('tiet',$tiet);
                // Nếu có phòng thì cập nhật phòng, không thì tạo mới thiết bị rỗng
                if($borrow_devices->count()){
                    $borrow_devices->update([
                        'lab_id' => $device['lab_id'] ?? 0,
                        'lesson_name' => $device['lesson_name'],
                        'session' => $device['session'],
                        'lecture_name' => $device['lecture_name'],
                        'room_id' => $device['room_id'],
                        'lecture_number' => $device['lecture_number'],
                        'borrow_date' => $item->borrow_date,
                    ]);
                }else{
                    $borrow_devices->create([
                        'lesson_name' => $device['lesson_name'],
                        'session' => $device['session'],
                        'lecture_name' => $device['lecture_name'],
                        'room_id' => $device['room_id'],
                        'lecture_number' => $device['lecture_number'],
                        'lab_id' => $device['lab_id'],
                        'borrow_date' => $item->borrow_date,
                    ]);
                }
            }
        }

        // Xóa phòng bộ môn
        if( $request->devices && in_array($request->task,['delete-lab']) ){
            $tiet   = $request->tiet;
            $borrow_devices = $item->borrow_devices()->where('tiet',$tiet)->update([
                'lab_id' => 0
            ]);
        }
        // Thêm tiết dạy mới
        if( $request->devices && $request->task == 'add-tiet' ){
            $request_arr = $request->toArray();
            $request_devices = $request_arr['devices'];
            $tiet = end($request_devices)['tiet'];
            $borrow_devices = $item->borrow_devices()->where('tiet',$tiet)->get()->toArray();
            foreach( $borrow_devices as $borrow_device ){
                unset($borrow_device['id']);
                $borrow_device['tiet'] = $tiet + 1;
                $item->borrow_devices()->create($borrow_device);
            }
            // Thiết bị tự chuẩn bị
            $borrow_fake_devices = $item->borrow_fake_devices()->where('tiet',$tiet)->get()->toArray();
            foreach( $borrow_fake_devices as $borrow_fake_device ){
                unset($borrow_fake_device['id']);
                $borrow_fake_device['tiet'] = $tiet + 1;
                $item->borrow_fake_devices()->create($borrow_fake_device);
            }
        }

        // Thêm thiết bị
        if( $request->devices && in_array($request->task,['add-device']) ){
            $index = 0;
            foreach( $request->devices as $tiet => $device ){
                $tiet = $index;
                $device['borrow_date'] = $item->borrow_date;
                $device['tiet'] = $tiet;
                $item->borrow_devices()->updateOrCreate([
                    'tiet' => $tiet,
                    'device_id' => $device['device_id'] ?? 0,
                ],$device);
                if( !empty($device['lab_id']) ){
                    $item->borrow_devices()->where('tiet',$tiet)->update([
                        'lab_id' => $device['lab_id']
                    ]);
                }
                // Nếu thêm thiết bị thì xóa dữ liệu phòng bộ môn
                if( !empty($device['device_id']) ){
                    $item->borrow_devices()->where('tiet',$tiet)->where('device_id',0)->delete();
                }
                $index++;
            }
        }
        // Lưu yêu cầu
        if( in_array($request->task,['save-form','save-draft']) ){
            $number_tiets = range(1, 10);
            $active_tiets = [];

            // Lưu thiết bị từ kho
            if( count($request->devices) ){
                foreach( $request->devices as $tiet => $device ){
                    $active_tiets[] = $tiet;
                    $updateData = [
                        'lesson_name' => $device['lesson_name'],
                        'session' => $device['session'],
                        'lecture_name' => $device['lecture_name'],
                        'room_id' => $device['room_id'],
                        'lecture_number' => $device['lecture_number'],
                        // 'quantity' => $device['quantity'] ?? 1,
                        'lab_id' => $device['lab_id'],
                        'borrow_date' => $item->borrow_date
                    ];
                    // Kiểm tra tại thời điểm lưu có trùng phòng bộ môn hay không
                    $lab_id         = $updateData['lab_id'];//Phòng
                    $session        = $updateData['session'];//Buổi
                    $tietTKB        = $updateData['lecture_number'];//Tiết TKB
                    $date          = $updateData['borrow_date'];//Ngày dạy

                    if($lab_id){
                        $check_borrow_lab = BorrowDevice::select(['borrow_id', 'lab_id', 'session', 'lecture_number'])
                        ->where('borrow_date', $date)
                        ->where('lecture_number', $tietTKB)
                        ->where('session', $session)
                        ->where('lab_id', $lab_id)
                        ->where('borrow_id', '!=', $id)
                        ->whereHas('borrow', function ($query) {
                            $query->where('status', '>=', 0);
                        })->first();
                        if($check_borrow_lab){
                            $lab = \App\Models\Lab::find($lab_id);
                            $borrow = \App\Models\Borrow::find($check_borrow_lab->borrow_id);
                            $user = \App\Models\User::find($borrow->user_id);
                            return [
                                'success' => false,
                                'message' => '<strong>'.$lab->name.'</strong> đã có <strong>'.$user->name.'</strong> mượn. Buổi: <strong>'.$session.'</strong>, Tiết TKB: <strong>'.$tietTKB.'</strong>',
                            ];
                        }
                    }
                    $item->borrow_devices()->where('tiet',$tiet)->update($updateData);
                }
            }

            // Lưu thiết bị tự chuẩn bị
            if( count($request->quantity_fake_devices) ){
                $borrow_devices = $item->borrow_devices()->where('tiet',$tiet);
                // Lưu thiết bị ảo khi ko có thiết bị
                if($borrow_devices->count() == 0){
                    $borrow_devices->create([
                        'lesson_name' => $device['lesson_name'],
                        'session' => $device['session'],
                        'lecture_name' => $device['lecture_name'],
                        'room_id' => $device['room_id'],
                        'lecture_number' => $device['lecture_number'],
                        'lab_id' => $device['lab_id'],
                        'borrow_date' => $item->borrow_date,
                    ]);
                }
            }

            // Xử lý phiếu có thiết bị + phòng bộ môn
            if( count($active_tiets) ){
                foreach( $active_tiets as $active_tiet ){
                    $number_devices = $item->borrow_devices()
                    ->where('tiet',$tiet)
                    ->count();
                    if( $number_devices > 1 ){
                        $item->borrow_devices()->where('tiet',$tiet)->where('device_id',0)->delete();
                    }
                }
            }

            // Hook xử lý sự kiện sau khi phiếu mượn tạo thành công
            if($request->task == 'save-form'){
                $auto_approved = Option::get_option('borrow_device','auto_approved',0);
                if($auto_approved){
                    $item->status = self::ACTIVE;
                    $item->save();
                }
            }
        }
        
        return [
            'success' => true,
            'item' => $item,
            'message' => 'Cập nhật thành công',
        ];
    }
    public static function deleteItem($id){
        $item = self::findItem($id);
        $item->borrow_devices()->delete();
        return $item->delete();
    }
    // Relationships
    public function borrow_devices(){
        return $this->hasMany(BorrowDevice::class);
    }

    public function borrow_fake_devices(){
        return $this->hasMany(BorrowDeviceFake::class);
    }

    // Attributes
    public static function copyItem($id){
        $item = self::findItem($id);
        $dataBorrow = [
            "user_id" => $item->user_id,
            "status" =>  self::DRAFT,
            "borrow_date" =>  Carbon::now()->format('y-m-d'),
            "borrow_note" =>  $item->borrow_note,
        ];
        $borrow = self::create($dataBorrow);
        foreach ($item->borrow_devices as $device) {
            $dataDevice = [
                "borrow_id" => $borrow->id,
                "device_id" => $device->device_id,
                "room_id" => $device->room_id,
                "quantity" => $device->quantity,
                "borrow_date" => Carbon::now()->format('y-m-d'),
                "return_date" => Carbon::now()->format('y-m-d'),
                "lecture_name" => $device->lecture_name,
                "lesson_name" => $device->lesson_name,
                "session" => $device->session,
                "image_first" => $device->image_first,
                "image_last" => $device->image_last,
                "status" => $device->status,
                "lecture_number" => $device->lecture_number,
                "tiet" => $device->tiet,
                "lab_id" => $device->lab_id,
            ];
            $device = \App\Models\BorrowDevice::create($dataDevice);
        }
    }
    public function getBorrowItemsAttribute(){
        $item = self::find($this->id);
        $results = [];
        if( count($item->borrow_devices) ){
            foreach( $item->borrow_devices as $borrow_device ){
                $results[$borrow_device->tiet][] = $borrow_device;
            }
        }
        return $results;
    }
    public function getBorrowFakeItemsAttribute(){
        $item = self::find($this->id);
        $results = [];
        if( count($item->borrow_fake_devices) ){
            foreach( $item->borrow_fake_devices as $borrow_device ){
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
    // public function getDeviceNamesAttribute(){
    //     $device_ids = $this->borrow_devices->pluck('device_id','device_id');
    //     $names = '';
    //     if($device_ids){
    //         $device_ids = $device_ids->toArray();
    //         $labs = \App\Models\Device::whereIn('id',$device_ids)->pluck('name')->toArray();
    //         $names = implode('<br>',$labs);
    //     }
    //     return $names;
    // }
    public function getDeviceNamesAttribute()
    {
        $names = '';

        if ($this->borrow_devices && $this->borrow_devices->count()) {
            $deviceWithQty = $this->borrow_devices->map(function ($borrowDevice) {
                $device = \App\Models\Device::find($borrowDevice->device_id);
                if ($device) {
                    return $device->name . '<strong class="text-danger"> (x' . $borrowDevice->quantity . ') </strong>';
                }
                return null;
            })->filter()->toArray();

            $names = implode('<br>', $deviceWithQty);
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
                        $labs[$borrow_lab->borrow_id.'-'.$borrow_lab->session.'-'.$borrow_lab->tiet] = [
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
    // Lấy dang sách phòng mượn tổng hợp theo tuần
    public static function getBorrowedLabsSummary($request){
        // 1. Lấy ngày bắt đầu và kết thúc của tuần
        $startDayEndDate = self::getStartEndDateFromWeek($request->week);

        // 2. Thực hiện truy vấn và lấy dữ liệu dưới dạng Collection
        $borrow_labs = BorrowDevice::select('*')
            ->whereBetween('borrow_date', [$startDayEndDate['startDate']->format('Y-m-d'), $startDayEndDate['endDate']->format('Y-m-d')])
            ->where('lab_id', '>', 0)
            ->whereHas('borrow', function($query) use($request){
                $query->where('status', '>=', 0);
            })
            ->orderBy('borrow_date', 'asc')
            ->distinct()
            ->get(); // Trả về Laravel Collection

        // 3. Xử lý nhóm và định dạng dữ liệu (Thay thế tất cả các vòng lặp foreach thủ công)
        $final_grouped_items = $borrow_labs
            // Bước A: Chuẩn bị dữ liệu và tạo khóa nhóm
            ->map(function ($item) {
                // Định dạng tiết học: 5s, 3c
                $session_short = ($item->session === 'Sáng') ? 's' : 'c';
                $session_and_lecture = $item->lecture_number . $session_short;

                return [
                    // Khóa nhóm chính
                    'group_key' => $item->borrow_date . '|' . ($item->borrow->user->name ?? '') . '|' . $item->lab_id,
                    // Thông tin cần thiết
                    'borrow_date' => $item->borrow_date,
                    'user_name'   => $item->borrow->user->name ?? '',
                    'lab_id'      => $item->lab_id,
                    'lab_name'    => $item->lab->name ?? '',
                    'lecture'     => $session_and_lecture, // Tiết học đã định dạng
                    'lecture_number_int' => (int) $item->lecture_number, // Dùng để sắp xếp
                ];
            })
            // Bước B: Nhóm dữ liệu theo khóa chính (Ngày|Giáo viên|Phòng)
            ->groupBy('group_key')
            // Bước C: Xử lý từng nhóm để gộp các tiết học và định dạng kết quả cuối cùng
            ->map(function ($group) {
                // Lấy thông tin cơ bản từ bản ghi đầu tiên của nhóm
                $first_item = $group->first();

                // Lấy tất cả các tiết học (đã định dạng)
                $unique_lectures = $group->pluck('lecture')->unique()->all();

                // Sắp xếp các tiết học dựa trên số tiết (sử dụng trường lecture_number_int)
                // Thay vì dùng usort trên chuỗi '5s', ta sắp xếp các bản ghi gốc trước khi pluck, hoặc dùng logic substr
                // Tuy nhiên, việc sắp xếp thủ công trên mảng unique_lectures vẫn là cách hiệu quả nhất cho định dạng này
                usort($unique_lectures, function($a, $b) {
                    $num_a = (int) substr($a, 0, -1); // Lấy số từ '5s'
                    $num_b = (int) substr($b, 0, -1);
                    return $num_a - $num_b;
                });
                
                // Gộp chuỗi tiết học
                $lectures_combined = implode(', ', $unique_lectures);

                return [
                    'borrow_date' => $first_item['borrow_date'],
                    'user_name'   => $first_item['user_name'],
                    'lab_id'      => $first_item['lab_id'],
                    'lab_name'    => $first_item['lab_name'],
                    'lectures_combined' => $lectures_combined,
                ];
            })
            // Bước D: Nhóm lại theo ngày để có định dạng cuối cùng: ["2025-10-01" => [ nhóm 1, nhóm 2, ... ]]
            ->groupBy('borrow_date')
            ->toArray();

        return $final_grouped_items;
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
            $borrow_purposes = \App\Models\BorrowPurpose::query()->whereNull('deleted_at')->pluck('name','slug')->toArray();
            return $borrow_purposes ?? self::PURPOSE;
        }
        return self::PURPOSE;
    }

}
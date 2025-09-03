<form class="form-search" action="{{ route('labs.index') }}" method="get">
    <div class="row">
    <div class="col-lg-3 col-md-6 col-sm-12"> 
            <label class="form-label">Ngày dạy</label>
            <input disabled type="date" name="borrow_date" class="form-control" value="{{ request()->borrow_date }}">
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12 d-none">
            <label class="form-label">Lớp</label>
            <select disabled class="form-control f-filter" name='room_id'>
                <option value="">Lớp</option>
                @foreach($rooms as $room)
                <option value="{{$room->id}}"
                    @selected($room->id == request()->room_id)>
                    {{ $room->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12"> 
            <label class="form-label">Buổi</label>
            <select disabled name="session" class="form-control">
                <option @selected( request()->session == 'Sáng') value="Sáng">Sáng</option>
                <option @selected( request()->session == 'Chiều') value="Chiều">Chiều</option>
            </select>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12"> 
            <label class="form-label">Tiết TKB</label>
            <select disabled name="lecture_number" class="form-control">
                <option @selected( request()->lecture_number  == 1) value="1">1</option>
                <option @selected( request()->lecture_number  == 2) value="2">2</option>
                <option @selected( request()->lecture_number  == 3) value="3">3</option>
                <option @selected( request()->lecture_number  == 4) value="4">4</option>
                <option @selected( request()->lecture_number  == 5) value="5">5</option>
            </select>
        </div>
        <div class="col col-lg-3">
            <label class="form-label">Phân Loại</label>
            <select disabled class="form-control f-filter" name='department_id'>
                <option value="">Môn học</option>
                @foreach($departments as $department)
                <option value="{{$department->id}}"
                    @selected($department->id == request()->department_id)>
                    {{ $department->name }}</option>
                @endforeach
            </select>
        </div>
    </div>
    
</form>
<div class="table-responsive white-space-nowrap mt-2">
    <table class="table align-middle">
        <thead class="table-light">
            <tr>
                <th>STT</th>
                <th width="300px">Tên phòng học</th>
                <th>Bộ môn</th>
                <th>GVSD</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $key => $item )
            <tr data-quantity="1" data-name="{{ $item->name }}" data-device_type_name="{{ $item->device_type_name }}" data-department_name="{{ $item->department_name }}">
                <td>{{ $key + 1 }}</td>
                <td>{{ $item->name }}</td>
                <td>{{ $item->department->name }}</td>
                <td>{{ $borrow_lab_ids[$item->id] ?? '-' }}</td>
                <td>
                    @if( empty($borrow_lab_ids[$item->id]) ) 
                    <button data-lab-id="{{ $item->id }}" data-name="{{ $item->name }}" class="btn btn-sm btn-primary add-lab">Chọn</button> 
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @include('admintheme::includes.globals.pagination')
</div>
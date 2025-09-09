<form class="row form-search" action="{{ route('devices.index') }}" method="get">
    <div class="col-12 col-lg-3 mb-2">
        <input type="text" name="name" class="form-control f-filter-up" placeholder="Tên thiết bị" value="{{ request()->name }}">
    </div>
    <div class="col-12 col-lg-3 mb-2">
        <select class="form-control f-filter" name='qty'>
            <option @selected( request()->qty === '' ) value="">Tình trạng</option>
            <option value="1" @selected( request()->qty == 1 )> Thiết bị còn</option>
            <option value="0" @selected( request()->qty !== "" && request()->qty === '0' )> Thiết bị đã hết</option>
        </select>
    </div>
    <div class="col-12 col-lg-3 mb-2">
        <select class="form-control f-filter" name='device_type_id'>
            <option value="">Loại thiết bị</option>
            @foreach($device_types as $device_type)
            <option value="{{$device_type->id}}"
                @selected($device_type->id == request()->device_type_id)>
                {{ $device_type->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-12 col-lg-3 mb-2">
        <select class="form-control f-filter" name='department_id'>
            <option value="">Môn học</option>
            @foreach($departments as $department)
            <option value="{{$department->id}}"
                @selected($department->id == request()->department_id)>
                {{ $department->name }}</option>
            @endforeach
        </select>
    </div>
</form>
<div class="table-responsive white-space-nowrap mt-2">
    <table class="table align-middle">
        <thead class="table-light">
            <tr>
                <th>STT</th>
                <th width="300px">Tên thiết bị</th>
                <th>Số lượng</th>
                <th>Loại thiết bị</th>
                <th>Bộ môn</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $key => $item )
            <tr data-quantity="1" data-name="{{ $item->name }}" data-device_type_name="{{ $item->device_type_name }}" data-department_name="{{ $item->department_name }}">
                <td>{{ $key + 1 }}</td>
                <td>{{ $item->name }}</td>
                <td>{{ $item->quantity }}</td>
                <td>{{ $item->device_type_name }}</td>
                <td>{{ $item->department_name }}</td>
                <td> <button data-device-id="{{ $item->id }}" class="btn btn-sm btn-primary add-device">Thêm</button> </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @include('globals.pagination')
</div>
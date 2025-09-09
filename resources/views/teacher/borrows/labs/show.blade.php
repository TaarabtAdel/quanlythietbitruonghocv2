@extends('admintheme::layouts.master')
@section('content')
@include('admintheme::includes.globals.breadcrumb',[
'page_title' => 'Lịch Báo Sử Dụng Phòng',
'actions' => [

]
])
<p class="mb-2">Lưu ý: Dữ liệu đang hiển thị từ <span class="fw-bold">{{ @$startDate->format('d/m/Y') }}</span> đến <span class="fw-bold">{{ @$endDate->format('d/m/Y') }}</span> </p>

<!-- Item actions -->
<form action="{{ route($route_prefix.'labs') }}" method="get">
    <div class="row">
        <div class="col-lg-4 col-md-6 col-sm-12">
            <label class="form-label fw-bold">Giáo Viên</label>
            <x-admintheme::form-input-users name="user_id" selected_id="{{ request()->user_id }}" autoSubmit="1" />
        </div>
        <div class="col-lg-2 col-md-6 col-sm-12">
            <label class="form-label fw-bold">Buổi</label>
            <select name="session" class="form-control" onchange="this.form.submit()">
                <option value="">---</option>
                <option @selected(request()->session == 'AM') value="AM">Sáng</option>
                <option @selected(request()->session == 'PM') value="PM">Chiều</option>
            </select>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12">
            <label class="form-label fw-bold">Phòng Học</label>
            <x-admintheme::form-input-labs name="lab_id" selected_id="{{ request()->lab_id }}" autoSubmit="1" />
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12">
            <label class="form-label fw-bold">Ngày dạy : Tuần</label>
            <input type="week" min="2022-W01" max="{{ date('Y') }}-W99" name="week" class="form-control"
                value="{{ request()->week }}" onchange="this.form.submit()">
        </div>
    </div>
</form>

<div class="card mt-4">
    <div class="card-body">
        <div class="product-table">
            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <tbody>
                        <tr>
                            <td></td>
                            <td colspan="2" class="text-center fw-bold">Buổi sáng</td>
                            <td colspan="2" class="text-center fw-bold">Buổi chiều</td>
                        </tr>
                        <tr>
                            <td>Ngày</td>
                            <td>Tiết</td>
                            <td class="text-uppercase fw-bold">{{ $lab_name }}</td>
                            <td>Tiết</td>
                            <td class="text-uppercase fw-bold">{{ $lab_name }}</td>
                        </tr>
                        @foreach( $items as $date => $tiet_arr )
                            @php $pm = 6;  @endphp
                            @for($am = 1; $am <= 5; $am++)
                            <tr>
                                @if( $am == 1 )
                                <td rowspan="5" class="fw-bold text-danger">{{ __(date('l',strtotime($date))) }}</td>
                                @endif
                                <td>{{ $am }}</td>
                                <td class="{{ !empty($tiet_arr[$am]['user_name']) ? 'bg-info' : '' }}">{{ $tiet_arr[$am]['user_name'] ?? '' }}</td>
                                <td>{{ $am }}</td>
                                <td class="{{ !empty($tiet_arr[$pm]['user_name']) ? 'bg-info' : '' }}">{{ $tiet_arr[$pm]['user_name'] ?? '' }}</td>
                            </tr>
                            @php $pm++;  @endphp
                            @endfor
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection
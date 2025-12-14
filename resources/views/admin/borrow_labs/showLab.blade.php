@extends('admin.layouts.master')
@section('content')
@include('globals.breadcrumb',[
    'page_title' => 'Lịch Báo Sử Dụng Phòng',
    'actions' => [
    ]
])
@if( isset(request()->sw_start_week) && isset(request()->sw_end_week) )
<p class="mb-2">Dữ liệu đang hiển thị từ ngày <span class="fw-bold">{{ date('d/m/Y',strtotime(request()->sw_start_week)) }}</span> đến <span class="fw-bold">{{ date('d/m/Y',strtotime(request()->sw_end_week)) }}</span> </p>
@else
<p class="mb-2">Dữ liệu đang hiển thị từ <span class="fw-bold">{{ @$startDate->format('d/m/Y') }}</span> đến <span class="fw-bold">{{ @$endDate->format('d/m/Y') }}</span> </p>
@endif
<!-- Item actions -->
<form action="{{ route($route_prefix.'labs') }}" method="get">
    <div class="row">
        <div class="col-lg-4 col-md-6 col-sm-12">
            <label class="form-label fw-bold">Giáo Viên</label>
            <x-form-input-users name="user_id" selected_id="{{ request()->user_id }}" autoSubmit="1" />
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
            <x-form-input-labs name="lab_id" selected_id="{{ request()->lab_id }}" autoSubmit="1" />
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12">
            <label class="form-label fw-bold">Ngày dạy : Tuần</label>
            <x-form-input-school-week name="week" selected_id="{{ request()->week }}" autoSubmit="true" />
            <!-- <input type="week" min="2022-W01" max="{{ date('Y') }}-W99" name="week" class="form-control"
                value="{{ request()->week }}" onchange="this.form.submit()"> -->
        </div>
    </div>
</form>

@include($view_path.'.includes.single-lab',[
    'items' => $items,
    'lab_name' => $lab_name,
])
@endsection
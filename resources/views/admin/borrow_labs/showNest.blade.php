@extends('admin.layouts.master')
@section('content')
@include('globals.breadcrumb',[
    'page_title' => 'Lịch Báo Sử Dụng Phòng Theo Tổ',
    'actions' => [

    ]
])
<p class="mb-2">Lưu ý: Dữ liệu đang hiển thị từ <span class="fw-bold">{{ @$startDate->format('d/m/Y') }}</span> đến <span class="fw-bold">{{ @$endDate->format('d/m/Y') }}</span> </p>

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
        <div class="col col-12 col-lg-3">
            <label class="form-label fw-bold">Tổ</label>
            <x-form-input-nests name="nest_id" selected_id="{{ request()->nest_id }}" autoSubmit="true" />
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12">
            <label class="form-label fw-bold">Ngày dạy : Tuần</label>
            <input type="week" min="2022-W01" max="{{ date('Y') }}-W99" name="week" class="form-control"
                value="{{ request()->week }}" onchange="this.form.submit()">
        </div>
    </div>
</form>

@if( count($lab_items) )
    @foreach( $lab_items as $lab_name => $items )
        @include($view_path.'.includes.single-lab',[
            'items' => $items,
            'lab_name' => $lab_name,
        ])
    @endforeach
@else
<div class="card mt-4">
    <div class="card-body">
    <p class="text-center mt-4">Chưa có dữ liệu cho tuần này</p>
    </div>
</div>
@endif

@endsection
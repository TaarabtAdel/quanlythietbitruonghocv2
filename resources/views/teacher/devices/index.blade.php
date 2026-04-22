@extends('teacher.layouts.master')
@section('title', 'Thiết bị')
@section('content')
<div class="page-wrapper">
    @include('globals.breadcrumb',[
        'page_title' => 'Danh sách thiết bị',
        'actions' => []
    ])
    <form class="row" action="" method="get">
        <div class="col-lg-4 mb-2">
            <label class="form-label fw-bold">Tên</label>
            <input class="form-control" name="name" type="text" placeholder="Nhập tên sau đó nhấn enter để tìm"
                value="{{ request()->name }}">
        </div>
        <div class="col-lg-4 mb-2">
            <label class="form-label fw-bold">Loại thiết bị</label>
            <x-form-input-device-types name="device_type_id" selected_id="{{ request()->device_type_id }}"
                autoSubmit="1" />
        </div>
        <div class="col-lg-4 mb-2">
            <label class="form-label fw-bold">Môn học</label>
            <x-form-input-departments name="department_id" selected_id="{{ request()->department_id }}"
                autoSubmit="1" />
        </div>
    </form>

    <div class="card mt-4">
        <div class="card-body">
            <div class="product-table">
                <div class="table-responsive white-space-nowrap">
                    <table class="table align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>STT</th>
                                <th width="300px">Tên thiết bị</th>
                                <th>Số lượng</th>
                                <th>Tiêu hao</th>
                                <th>Còn SD</th>
                                <th>Loại thiết bị</th>
                                <th>Bộ môn</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($items as $key => $item )
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ $item->name }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td>{{ $item->broken ?? '' }}</td>
                                <td>{{ max(0, (int)($item->quantity ?? 0) - (int)($item->broken ?? 0)) }}</td>
                                <td>{{ $item->device_type_name }}</td>
                                <td>{{ $item->department_name }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @include('globals.pagination')
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
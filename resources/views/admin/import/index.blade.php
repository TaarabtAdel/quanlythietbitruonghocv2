@extends('admin.layouts.master')
@section('title','Nhập: ' . __(request()->type) )
@section('content')
    @include('globals.breadcrumb',[
        'page_title' => 'Nhập: '.__(request()->type),
        'actions' => []
    ])
    <div class="card mt-4">
        <div class="card-body">
            <form action="{{ route($route_prefix.'store') }}" method="post" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="type" value="{{ request()->type }}">
                <div class="mb-4">
                    <h5 class="mb-4">Bạn đang chuẩn bị nhập dữ liệu vào: {{ __(request()->type) }}</h5>
                    <p class="mb-0">- Dữ liệu mới sẽ được thêm vào, dữ liệu trùng lặp sẽ được cập nhật lại.</p>
                    <p class="mb-0">- Nhấn vào <a target="_blank" href="{{ asset('system/import/'.$templateFile) }}?t={{ time() }}">đây</a> để tải tệp nhập liệu mẫu </p>
                    <p class="mb-0">- Tải file dữ liệu đã được cập nhật lên và nhấn <strong>Tiến Hành</strong></p>
                </div>
                <div class="form-group mb-4">
                    <label class="form-label">Chọn Tệp (đuôi .xls, .xlsx)</label>
                    <input type="file" name="file" class="form-control" accept=".xls, .xlsx">
                    <x-form-input-error field="file"/>
                </div>
                <div class="mb-4">
                    <div class="d-md-flex d-grid align-items-center gap-3">
                        <a href="{{ route('admin.import.index',[ 'type'=> request()->type ]) }}" class="btn btn-dark">Quay lại</a>
                        <button class="btn btn-primary px-4">Tiến Hành</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

@endsection
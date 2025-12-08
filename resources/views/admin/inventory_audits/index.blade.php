@extends('admin.layouts.master')

@section('title', 'Danh sách kiểm kê')
@section('content')

@php
    // Đảm bảo $route_prefix được định nghĩa, ví dụ: 'admin.audits'
    $route_prefix = $route_prefix ?? 'admin.audits';
@endphp

{{-- Breadcrumb và Action Button (Giữ nguyên logic phân quyền) --}}
@if (Auth::check() && Auth::user()->hasPermission(request()->type.'_create'))
    @include('globals.breadcrumb',[
        'page_title' => 'Danh sách kiểm kê thiết bị',
        'actions' => [
            // Thay thế 'type' bằng 'audits' nếu cần, hoặc giữ nguyên nếu 'type' là 'audits'
            'add_new' => route($route_prefix.'.create'), 
            // Nếu dùng tham số type trong route: 'add_new' => route($route_prefix.'.create',['type'=>request()->type]),
        ]
    ])
@else
    @include('globals.breadcrumb',[
        'page_title' => 'Danh sách kiểm kê thiết bị',
    ])
@endif

<hr>

{{-- Form Tìm kiếm & Lọc --}}
<form action="{{ route($route_prefix.'.index') }}" method="get">
    {{-- Giữ nguyên nếu bạn dùng type để phân biệt loại kiểm kê
    <input type="hidden" name="type" value="{{ request()->type }}">
    --}}
    <div class="row">
        {{-- Tên kiểm kê (Giả định trường 'name' đã được thêm vào Audit Model/Migration) --}}
        <div class="col-lg-5 col-md-6 mb-3">
            <label class="form-label fw-bold">Tên/Nội dung kiểm kê</label>
            <input class="form-control" name="name" type="text" placeholder="Nhập tên sau đó nhấn enter để tìm"
                value="{{ request()->name }}">
        </div>
        
        {{-- Lọc theo Năm học --}}
        <div class="col-lg-3 col-md-4 mb-3">
            <label class="form-label fw-bold">Năm học</label>
            <x-form-input-school-years name="school_year" selected_id="{{ request()->school_year }}"
                    autoSubmit="true" />
        </div>
        
        {{-- Lọc theo Trạng Thái (Sử dụng X-form-input-status hoặc Select Box thủ công) --}}
        <div class="col-lg-2 col-md-2 mb-3">
            <label class="form-label fw-bold">Trạng Thái</label>
            {{-- Thay thế X-form-input-status bằng Select Box phù hợp với giá trị -1, 0, 1 --}}
            <select name="status" class="form-control" onchange="this.form.submit()">
                <option value="">-- Tất cả --</option>
                <option value="-1" {{ request()->status == '-1' ? 'selected' : '' }}>Nháp</option>
                <option value="1" {{ request()->status == '1' ? 'selected' : '' }}>Đã duyệt</option>
            </select>
            
            {{-- Nếu bạn vẫn muốn dùng component cũ và đã cập nhật logic của nó: 
            <x-form-input-status name="status" status="{{ request()->status }}" autoSubmit="1" />
            --}}
        </div>
        
        <div class="col-lg-2 col-md-2 mb-3 d-flex align-items-end">
            <button type="submit" class="btn btn-primary w-100">Tìm kiếm</button>
        </div>
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
                            <th>Nội dung Phiếu</th>
                            <th>Năm học</th>
                            <th>Ngày kiểm kê</th>
                            <th>Người tạo</th>
                            <th>Trạng thái</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @if( count( $items ) )
                            @foreach( $items as $key => $item )
                            <tr>
                                <td>{{ ($items->currentPage() - 1) * $items->perPage() + ($key + 1) }}</td>
                                {{-- Giả định tên phiếu là trường 'name' hoặc 'content' --}}
                                <td>
                                    <a href="{{ route($route_prefix.'.show', $item->id) }}" class="fw-bold">{{ $item->name ?? 'Phiếu kiểm kê' }}</a>
                                </td>
                                <td>{{ $item->school_year }}</td>
                                <td>{{ $item->audit_date ? date('d/m/Y', strtotime($item->audit_date)) : 'Chưa nhập' }}</td>
                                {{-- Giả định mối quan hệ 'user' đã được nạp eager load --}}
                                <td>{{ @$item->user->name ?? 'Hệ thống' }}</td> 
                                {{-- Sử dụng Accessor status_text đã định nghĩa trong Model --}}
                                <td>{!! $item->status_fm !!}</td> 
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-light border dropdown-toggle dropdown-toggle-nocaret"
                                            type="button" data-bs-toggle="dropdown">
                                            <i class="bi bi-three-dots"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <a class="dropdown-item"
                                                    href="{{ route($route_prefix.'.show', $item->id) }}?page={{ request()->page }}">
                                                    {{ __('sys.show') }}
                                                </a>
                                            </li>
                                            {{-- Nút Chỉnh sửa --}}
                                            @if (Auth::check() && Auth::user()->hasPermission(request()->type.'_update'))
                                                <li>
                                                    <a class="dropdown-item"
                                                        href="{{ route($route_prefix.'.edit', $item->id) }}?page={{ request()->page }}">
                                                        {{ __('sys.edit') }}
                                                    </a>
                                                </li>
                                            @endif
                                            {{-- Nút Chỉnh sửa --}}
                                            @if (Auth::check() && Auth::user()->hasPermission(request()->type.'_copy'))
                                                <li>
                                                    <a class="dropdown-item"
                                                        href="{{ route($route_prefix.'.copy', $item->id) }}?page={{ request()->page }}">
                                                        {{ __('sys.copy') }}
                                                    </a>
                                                </li>
                                            @endif
                                            {{-- Nút Xóa --}}
                                            @if (Auth::check() && Auth::user()->hasPermission(request()->type.'_delete'))
                                                <li>
                                                    <form
                                                        action="{{ route($route_prefix.'.destroy', $item->id) }}?page={{ request()->page }}"
                                                        method="post">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button onclick=" return confirm('{{ __('sys.confirm_delete') }}') "
                                                            class="dropdown-item text-danger">
                                                            {{ __('sys.delete') }}
                                                        </button>
                                                    </form>
                                                </li>
                                            @endif
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        @else
                        <tr>
                            <td colspan="7" class="text-center">{{ __('sys.no_item_found') }}</td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    {{-- Phân trang --}}
    @if( count( $items ) )
    <div class="card-footer pb-0">
        @include('globals.pagination', ['items' => $items])
    </div>
    @endif
</div>

@endsection
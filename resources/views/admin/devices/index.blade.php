@extends('admin.layouts.master')
@section('content')
@if (Auth::check() && Auth::user()->hasPermission(request()->type.'_create'))
    @include('globals.breadcrumb',[
        'page_title' => 'Danh sách thiết bị',
        'actions' => [
            'add_new' => route($route_prefix.'create',['type'=>request()->type]),
            //'export' => route($route_prefix.'export'),
        ]
    ])
@else
    @include('globals.breadcrumb',[
        'page_title' => 'Danh sách thiết bị',
    ])
@endif


<!-- Item actions -->
<form action="{{ route($route_prefix.'index') }}" method="get">
    <input type="hidden" name="type" value="{{ request()->type }}">
    <div class="row g-3"> 
        
        <div class="col-12 col-md-3">
            <label class="form-label fw-bold">Tên Thiết Bị</label>
            <input class="form-control" name="name" type="text" placeholder="Tìm kiếm..."
                value="{{ request()->name }}">
        </div>

        <div class="col-12 col-sm-6 col-md-3">
            <label class="form-label fw-bold">Loại Thiết Bị</label>
            <x-form-input-device-types name="device_type_id" selected_id="{{ request()->device_type_id }}"
                autoSubmit="1" />
        </div>

        <div class="col-12 col-sm-6 col-md-3">
            <label class="form-label fw-bold">Môn Học</label>
            <x-form-input-departments name="department_id" selected_id="{{ request()->department_id }}"
                autoSubmit="1" />
        </div>

        <div class="col-12 col-md-3">
            <label class="form-label fw-bold">Trạng Thái</label>
            <x-form-input-status name="status" status="{{ request()->status }}"
                autoSubmit="1" />
        </div>
        
        <div class="col-12 d-md-none">
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
                            <th><input type="checkbox" id="checkAll"></th>
                            <th>STT</th>
                            <th>Tên</th>
                            <th>Số lượng</th>
                            <th>Tiêu hao</th>
                            <th>Còn SD</th>
                            <th>Loại thiết bị</th>
                            <th>Bộ môn</th>
                            <th>Trạng thái</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @if( count( $items ) )
                        @foreach( $items as $key => $item )
                        <tr>
                            <td> <input type="checkbox" name="ids[]" class="check-item" value="{{ $item->id }}"> </td>
                            <td>{{ ($items->currentPage() - 1) * $items->perPage() + ($key + 1) }}</td>
                            <td>{{ $item->name }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>{{ $item->broken ?? '' }}</td>
                            <td>{{ max(0, (int)($item->quantity ?? 0) - (int)($item->broken ?? 0)) }}</td>
                            <td>{{ $item->devicetype->name ?? '' }}</td>
                            <td>{{ $item->department->name ?? '' }}</td>
                            <td>{!! $item->status_fm !!}</td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-light border dropdown-toggle dropdown-toggle-nocaret"
                                        type="button" data-bs-toggle="dropdown">
                                        <i class="bi bi-three-dots"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        @if (Auth::check() && Auth::user()->hasPermission(request()->type.'_update'))
                                            <li>
                                                <a class="dropdown-item"
                                                    href="{{ route($route_prefix.'edit',$item->id) }}?page={{ request()->page }}">
                                                    {{ __('sys.edit') }}
                                                </a>
                                            </li>
                                        @endif
                                        @if (Auth::check() && Auth::user()->hasPermission(request()->type.'_delete'))
                                            <li>
                                                <form
                                                    action="{{ route($route_prefix.'destroy',$item->id) }}?page={{ request()->page }}"
                                                    method="post">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button onclick=" return confirm('{{ __('sys.confirm_delete') }}') "
                                                        class="dropdown-item">
                                                        {{ $item->deleted_at ? __('sys.force_delete') : __('sys.delete') }}
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
                            <td colspan="10" class="text-center">{{ __('sys.no_item_found') }}</td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @if( count( $items ) )
    <div class="card-footer pb-0">
        @include('globals.pagination')
    </div>
    @endif
</div>

@endsection
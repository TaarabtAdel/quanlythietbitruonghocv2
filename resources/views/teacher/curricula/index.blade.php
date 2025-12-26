@extends('teacher.layouts.master')
@section('title','Danh sách chương trình đào tạo')
@section('content')
    @include('globals.breadcrumb',[
        'page_title' => 'Danh sách chương trình đào tạo',
    ])
<!-- Item actions -->
<form action="{{ route($route_prefix.'index') }}" method="get">
    <div class="row">
        <div class="col">
            <label class="form-label fw-bold">Tên chương trình</label>
            <input class="form-control" name="name" type="text" placeholder="Nhập tên sau đó nhấn enter để tìm"
                value="{{ request()->name }}">
        </div>
        <div class="col">
            <label class="form-label fw-bold">Mã chương trình</label>
            <input class="form-control" name="code" type="text" placeholder="Nhập mã sau đó nhấn enter để tìm"
                value="{{ request()->code }}">
        </div>
        <div class="col">
            <label class="form-label fw-bold">Bộ môn</label>
            <x-form-input-departments name="department_id" selected_id="{{ request()->department_id }}" autoSubmit="1" />
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
                            <th>Tên</th>
                            <th>Mã</th>
                            <th>Bộ môn</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if( count( $items ) )
                        @foreach( $items as $key => $item )
                        <tr>
                            <td>{{ ($items->currentPage() - 1) * $items->perPage() + ($key + 1) }}</td>
                            <td>{{ $item->name }}</td>
                            <td>{{ $item->code ?? '-' }}</td>
                            <td>{{ $item->department->name ?? '-' }}</td>
                            <td>
                                <a href="{{ route($route_prefix.'show', $item->id) }}" class="btn btn-sm btn-primary">
                                    <i class="bi bi-eye"></i> Xem chi tiết
                                </a>
                            </td>
                        </tr>
                        @endforeach
                        @else
                        <tr>
                            <td colspan="5" class="text-center">{{ __('sys.no_item_found') }}</td>
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


@extends('admin.layouts.master')
@section('content')
@include('globals.breadcrumb',[
    'page_title' => 'Cơ sở trực thuộc',
    'actions' => [
        'add_new' => route($route_prefix.'create'),
    ]
])

<p class="text-muted">
    Đăng nhập <strong>cơ sở chính</strong> rồi vào đây để thêm cơ sở.
    Bấm <strong>Thêm mới</strong>, nhập tên cơ sở (database có thể để trống).
    Sau đó dùng menu <strong>tên cơ sở</strong> góc trên bên phải để chuyển sang xem cơ sở đó.
</p>

<form action="{{ route($route_prefix.'index') }}" method="get">
    <div class="row">
        <div class="col">
            <label class="form-label fw-bold">Tên cơ sở</label>
            <input class="form-control" name="name" type="text" placeholder="Nhập tên sau đó nhấn enter để tìm"
                value="{{ request()->name }}">
        </div>
        <div class="col col-lg-2">
            <label class="form-label fw-bold">Trạng Thái</label>
            <x-form-input-status name="status" status="{{ request()->status }}" autoSubmit="1" />
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
                            <th>Tên cơ sở</th>
                            <th>Database</th>
                            <th>Trạng thái</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="table-light">
                            <td>—</td>
                            <td>Cơ sở chính</td>
                            <td><code>{{ $mainDatabase }}</code></td>
                            <td><span class="lable-table bg-success-subtle text-success rounded border border-success-subtle font-text2 fw-bold">Mặc định</span></td>
                            <td>
                                <form action="{{ route('admin.campuses.switch') }}" method="post" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="campus_key" value="main">
                                    <button type="submit" class="btn btn-sm btn-outline-primary">Xem</button>
                                </form>
                            </td>
                        </tr>
                        @forelse($items as $key => $item)
                        <tr>
                            <td>{{ ($items->currentPage() - 1) * $items->perPage() + ($key + 1) }}</td>
                            <td>{{ $item->name }}</td>
                            <td><code>{{ $item->database_name }}</code></td>
                            <td>{!! $item->status_fm !!}</td>
                            <td>
                                @if(!$item->deleted_at)
                                <form action="{{ route('admin.campuses.switch') }}" method="post" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="campus_key" value="{{ $item->id }}">
                                    <button type="submit" class="btn btn-sm btn-outline-primary">Xem</button>
                                </form>
                                @endif
                                <div class="dropdown d-inline">
                                    <button class="btn btn-sm btn-light border dropdown-toggle dropdown-toggle-nocaret"
                                        type="button" data-bs-toggle="dropdown">
                                        <i class="bi bi-three-dots"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a class="dropdown-item" href="{{ route($route_prefix.'edit',$item->id) }}?page={{ request()->page }}">
                                                {{ __('sys.edit') }}
                                            </a>
                                        </li>
                                        <li>
                                            <form action="{{ route($route_prefix.'destroy',$item->id) }}?page={{ request()->page }}" method="post">
                                                @csrf
                                                @method('DELETE')
                                                <button onclick="return confirm('{{ __('sys.confirm_delete') }}')" class="dropdown-item">
                                                    {{ $item->deleted_at ? __('sys.force_delete') : __('sys.delete') }}
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted">Chưa có cơ sở trực thuộc. Trường đang dùng cơ sở chính.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                @if($items->total())
                    {{ $items->links() }}
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

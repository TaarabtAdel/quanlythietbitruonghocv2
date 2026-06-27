@extends('teacher.layouts.master')
@section('title', 'Văn bản Thiết bị')
@section('content')
@include('globals.breadcrumb',[
    'page_title' => 'Danh sách văn bản',
    'actions' => []
])

<form class="form-search" action="{{ route('documents.index') }}" method="get">
    <div class="row">
        <div class="col-lg-4 mb-2">
            <label class="form-label fw-bold">Tên</label>
            <input class="form-control" name="name" type="text" placeholder="Nhập tên sau đó nhấn enter để tìm"
                value="{{ request()->name }}">
        </div>
    </div>
</form>

<div class="card mt-4">
    <div class="card-body">
        <div class="product-table">
            <div class="table-responsive white-space-nowrap mt-2">
                <table class="table align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>STT</th>
                            <th>Tên tài liệu</th>
                            <th>Ngày tạo</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($items as $key => $item)
                        <tr>
                            <td>{{ ($items->currentPage() - 1) * $items->perPage() + ($key + 1) }}</td>
                            <td>
                                <a href="{{ route('documents.show', $item->id) }}">{{ $item->name }}</a>
                            </td>
                            <td>{{ date('d/m/Y', strtotime($item->created_at)) }}</td>
                            <td>
                                <a href="{{ route('documents.show', $item->id) }}"><i class="fas fa-eye"></i> Xem</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="card-footer pb-0">
        @include('globals.pagination')
    </div>
</div>
@endsection

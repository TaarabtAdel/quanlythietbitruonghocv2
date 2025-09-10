@extends('teacher.layouts.master')
@section('title','Danh sách lớp học')
@section('content')
    @include('globals.breadcrumb',[
        'page_title' => 'Danh sách lớp học',
    ])
<!-- Item actions -->
<form action="{{ route($route_prefix.'index') }}" method="get">
    <input type="hidden" name="type" value="{{ request()->type }}">
    <div class="row">
        <div class="col">
            <label class="form-label fw-bold">Tên lớp học</label>
            <input class="form-control" name="name" type="text" placeholder="Nhập tên sau đó nhấn enter để tìm"
                value="{{ request()->name }}">
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
                        </tr>
                    </thead>
                    <tbody>
                        @if( count( $items ) )
                        @foreach( $items as $key => $item )
                        <tr>
                            <td>{{ ($items->currentPage() - 1) * $items->perPage() + ($key + 1) }}</td>
                            <td>{{ $item->name }}</td>
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
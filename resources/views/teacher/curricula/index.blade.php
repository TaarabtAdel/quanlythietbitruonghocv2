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
            <label class="form-label fw-bold">Năm học</label>
            <x-form-input-school-years name="academic_year" selected_id="{{ request()->academic_year }}" autoSubmit="1" />
        </div>
        <div class="col">
            <label class="form-label fw-bold">Bộ môn</label>
            <x-form-input-departments name="department_id" selected_id="{{ request()->department_id }}" autoSubmit="1" />
        </div>
        <div class="col">
            <label class="form-label fw-bold">Khối</label>
            <select name="grade" class="form-control" onchange="this.form.submit()">
                <option value="">--- Tất cả ---</option>
                <option value="10" {{ request()->grade == '10' ? 'selected' : '' }}>Khối 10</option>
                <option value="11" {{ request()->grade == '11' ? 'selected' : '' }}>Khối 11</option>
                <option value="12" {{ request()->grade == '12' ? 'selected' : '' }}>Khối 12</option>
            </select>
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
                            <th>Năm học</th>
                            <th>Bộ môn</th>
                            <th>Khối</th>
                            <th>Số bài học</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if( count( $items ) )
                        @foreach( $items as $key => $item )
                        <tr>
                            <td>{{ ($items->currentPage() - 1) * $items->perPage() + ($key + 1) }}</td>
                            <td>{{ $item->academic_year }}</td>
                            <td>{{ $item->department->name ?? '-' }}</td>
                            <td>{{ $item->grade_name }}</td>
                            <td>{{ $item->details_count }}</td>
                            <td>
                                <a href="{{ route($route_prefix.'show', $item->id) }}" class="btn btn-sm btn-primary">
                                    <i class="bi bi-eye"></i> Xem chi tiết
                                </a>
                            </td>
                        </tr>
                        @endforeach
                        @else
                        <tr>
                            <td colspan="6" class="text-center">{{ __('sys.no_item_found') }}</td>
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


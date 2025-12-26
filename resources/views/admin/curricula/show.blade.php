@extends('admin.layouts.master')
@section('title','Chi tiết chương trình đào tạo #'. $item->id)
@section('content')
@include('globals.breadcrumb',[
    'page_title' => 'Chi tiết chương trình đào tạo #'.$item->id,
])

<div class="row">
    <div class="col-12 col-lg-8">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title mb-4">Thông tin chương trình đào tạo</h5>
                
                <div class="mb-3">
                    <label class="fw-bold">Tên chương trình:</label>
                    <p>{{ $item->name }}</p>
                </div>

                <div class="mb-3">
                    <label class="fw-bold">Mã chương trình:</label>
                    <p>{{ $item->code ?? '-' }}</p>
                </div>

                <div class="mb-3">
                    <label class="fw-bold">Bộ môn:</label>
                    <p>{{ $item->department->name ?? '-' }}</p>
                </div>

                <div class="mb-3">
                    <label class="fw-bold">Mô tả:</label>
                    <p>{{ $item->description ?? '-' }}</p>
                </div>

                <div class="mb-3">
                    <label class="fw-bold">Trạng thái:</label>
                    <p>{!! $item->status_fm !!}</p>
                </div>

                <div class="mb-3">
                    <label class="fw-bold">Người tạo:</label>
                    <p>{{ $item->user->name ?? '-' }}</p>
                </div>

                <div class="mb-3">
                    <label class="fw-bold">Ngày tạo:</label>
                    <p>{{ date('d/m/Y H:i', strtotime($item->created_at)) }}</p>
                </div>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Chi tiết các môn học</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th width="50" class="text-center">STT</th>
                                <th>Tên môn học</th>
                                <th width="100" class="text-center">Số tín chỉ</th>
                                <th width="100" class="text-center">Số giờ</th>
                                <th width="100" class="text-center">Học kỳ</th>
                                <th>Ghi chú</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(count($item->details))
                                @foreach($item->details as $index => $detail)
                                <tr>
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td>{{ $detail->subject_name }}</td>
                                    <td class="text-center">{{ $detail->credits }}</td>
                                    <td class="text-center">{{ $detail->hours }}</td>
                                    <td class="text-center">{{ $detail->semester ?? '-' }}</td>
                                    <td>{{ $detail->note ?? '-' }}</td>
                                </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="6" class="text-center">Chưa có môn học nào</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card mt-4">
    <div class="card-body">
        <div class="d-flex align-items-center justify-content-end gap-2">
            <a href="{{ route($route_prefix.'index') }}" class="btn btn-secondary px-4">
                <i class='bx bx-arrow-back'></i> Quay lại
            </a>
            @if (Auth::check() && Auth::user()->hasPermission(request()->type.'_update'))
                <a href="{{ route($route_prefix.'edit', $item->id) }}" class="btn btn-primary px-4">
                    <i class='bx bx-edit'></i> Chỉnh sửa
                </a>
            @endif
        </div>
    </div>
</div>

@endsection


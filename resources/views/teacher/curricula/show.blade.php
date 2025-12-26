@extends('teacher.layouts.master')
@section('title','Chi tiết chương trình đào tạo #'. $item->id)
@section('content')
@include('globals.breadcrumb',[
    'page_title' => 'Chi tiết chương trình đào tạo #'.$item->id,
])

<div class="row">
    <div class="col-12 col-lg-8">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title mb-4">Thông tin phân phối chương trình</h5>
                
                <div class="mb-3">
                    <label class="fw-bold">Năm học:</label>
                    <p>{{ $item->academic_year }}</p>
                </div>

                <div class="mb-3">
                    <label class="fw-bold">Bộ môn:</label>
                    <p>{{ $item->department->name ?? '-' }}</p>
                </div>

                <div class="mb-3">
                    <label class="fw-bold">Khối:</label>
                    <p>{{ $item->grade_name }}</p>
                </div>

                <div class="mb-3">
                    <label class="fw-bold">Ngày tạo:</label>
                    <p>{{ date('d/m/Y H:i', strtotime($item->created_at)) }}</p>
                </div>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Chi tiết các bài học</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th width="50" class="text-center">STT</th>
                                <th>Loại phân môn</th>
                                <th width="100" class="text-center">Tuần</th>
                                <th width="100" class="text-center">Số tiết</th>
                                <th>Tên bài học</th>
                                <th>Ghi chú</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(count($item->details))
                                @foreach($item->details as $index => $detail)
                                <tr>
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td>
                                        @php
                                            $typeNames = [
                                                'co_ban' => 'Cơ bản',
                                                'chuyen_sau' => 'Chuyên sâu',
                                                'tu_chon' => 'Tự chọn',
                                                'bat_buoc' => 'Bắt buộc'
                                            ];
                                        @endphp
                                        {{ $typeNames[$detail->sub_subject_type] ?? $detail->sub_subject_type ?? '-' }}
                                    </td>
                                    <td class="text-center">{{ $detail->week ?? '-' }}</td>
                                    <td class="text-center">{{ $detail->lesson_number ?? '-' }}</td>
                                    <td>{{ $detail->lesson_name }}</td>
                                    <td>{{ $detail->note ?? '-' }}</td>
                                </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="6" class="text-center">Chưa có bài học nào</td>
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
        </div>
    </div>
</div>

@endsection


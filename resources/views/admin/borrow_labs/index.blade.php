@extends('admin.layouts.master')
@section('content')
@include('globals.breadcrumb',[
    'page_title' => 'Phòng mượn: '.request()->week,
    'actions' => []
])

<!-- Item actions -->
<form action="{{ route($route_prefix.'labs') }}" method="get">
    @if( isset(request()->sw_start_week) && isset(request()->sw_end_week) )
    <p class="mb-2">Dữ liệu đang hiển thị từ ngày <span class="fw-bold">{{ date('d/m/Y',strtotime(request()->sw_start_week)) }}</span> đến <span class="fw-bold">{{ date('d/m/Y',strtotime(request()->sw_end_week)) }}</span> </p>
    @else
    <p class="mb-2">Dữ liệu đang hiển thị từ <span class="fw-bold">{{ @$startDate->format('d/m/Y') }}</span> đến <span class="fw-bold">{{ @$endDate->format('d/m/Y') }}</span> </p>
    @endif
    <p class="mb-2">Lưu ý: <br> 
        - Xem chi tiết một phòng bằng cách chọn Phòng Học <br>
        - Xem chi tiết một tổ bằng cách chọn Tổ
    </p>
    <div class="row">
        <div class="col col-12 col-md-2">
            <label class="form-label fw-bold">Ngày dạy</label>
            <input type="date" name="borrow_date" class="form-control" value="{{ request()->borrow_date }}" onchange="this.form.submit()">
        </div>
        <div class="col col-12 col-lg-1">
            <label class="form-label fw-bold">Buổi</label>
            <select name="session" class="form-control" onchange="this.form.submit()">
                <option value="">---</option>
                <option @selected(request()->session == 'AM') value="AM">Sáng</option>
                <option @selected(request()->session == 'PM') value="PM">Chiều</option>
            </select>
        </div>
        <div class="col col-12 col-lg-3">
            <label class="form-label fw-bold">Giáo Viên</label>
            <x-form-input-users name="user_id" selected_id="{{ request()->user_id }}" autoSubmit="true" />
        </div>
        <div class="col col-12 col-lg-2">
            <label class="form-label fw-bold">Tổ</label>
            <x-form-input-nests name="nest_id" selected_id="{{ request()->nest_id }}" autoSubmit="true" />
        </div>
        <div class="col col-12 col-lg-2">
            <label class="form-label fw-bold">Phòng</label>
            <x-form-input-labs name="lab_id" selected_id="{{ request()->lab_id }}" autoSubmit="true" />
        </div>
        <div class="col col-12 col-lg-2">
            <label class="form-label fw-bold">Ngày dạy : Tuần</label>
            <x-form-input-school-week name="week" selected_id="{{ request()->week }}" autoSubmit="true" />
            <!-- <input type="week" min="2022-W01" max="{{ date('Y') }}-W99" name="week" class="form-control"
                value="{{ request()->week }}" onchange="this.form.submit()"> -->
        </div>
    </div>
</form>

<div class="card mt-4">
    <div class="card-body">
        <div class="product-table">
            <div class="table-responsive white-space-nowrap">
                <table class="table table-bordered align-middle">
                    <tr>
                        <th>STT</th>
                        <th>Ngày mượn</th>
                        <th>Buổi</th>
                        <th>Phòng</th>
                        <th>Tiết</th>
                        <th>Giáo Viên</th>
                        <th>Lớp</th>
                    </tr>
                    @php $index = 0; @endphp
                    @if( count( $items ) )
                    @foreach( $items as $key => $item )
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td> {{ $item->borrow->borrow_date ? date('d/m/Y',strtotime($item->borrow->borrow_date )) : '' }}</td>
                        <td><span
                                class="fw-bold text-{{ $item->session == 'Sáng' ? 'info' : 'warning' }}">{{ $item->session }}</span>
                        </td>
                        <td>{{ $item->lab->name ?? '' }}</td>
                        <td>{{ $item->lecture_number ?? '' }}</td>
                        <td>{{ $item->borrow->user->name ?? '' }}</td>
                        <td>{{ $item->room->name ?? '' }}</td>
                    </tr>
                    @php $index ++; @endphp
                    @endforeach
                    @else
                    <tr>
                        <td colspan="6" class="text-center">{{ __('sys.no_item_found') }}</td>
                    </tr>
                    @endif
                </table>
            </div>
        </div>
    </div>
    <div class="card-footer pb-0">

    </div>
</div>

@endsection
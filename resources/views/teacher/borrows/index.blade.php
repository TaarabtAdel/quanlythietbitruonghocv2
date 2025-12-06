@extends('teacher.layouts.master')
@section('title','Phiếu mượn thiết bị')
@section('content')
@include('globals.breadcrumb',[
    'page_title' => 'Danh sách phiếu',
    'actions' => [
        'add_new' => route($route_prefix.'create'),
    ]
])

<!-- Item actions -->
<form action="{{ route($route_prefix.'index') }}" method="get">
    <p class="mb-2 text-danger">Lưu ý: Chỉ tìm kiếm một trong ba trường Ngày dạy | Tuần | Năm</p>
     @if( isset($startDate) )
    <p class="mb-2 text-success">Dữ liệu đang hiển thị từ <span class="fw-bold">{{ @$startDate->format('d/m/Y') }}</span> đến <span class="fw-bold">{{ @$endDate->format('d/m/Y') }}</span> </p>
    @endif
    @if( isset(request()->borrow_date) )
    <p class="mb-2 text-success">Dữ liệu đang hiển thị vào ngày <span class="fw-bold">{{ date('d/m/Y',strtotime(request()->borrow_date)) }}</span> </p>
    @endif
    <div class="row">
        <!-- <div class="col-lg-3 col-md-6 col-sm-12">
            <label class="form-label fw-bold">Buổi</label>
            <select name="session" class="form-control" onchange="this.form.submit()">
                <option value="">---</option>
                <option @selected(request()->session == 'AM') value="AM">Sáng</option>
                <option @selected(request()->session == 'PM') value="PM">Chiều</option>
            </select>
        </div> -->
        <div class="col col-12 col-md-2">
            <label class="form-label fw-bold">Ngày dạy</label>
            <input type="date" name="borrow_date" class="form-control" value="{{ request()->borrow_date }}" onchange="this.form.submit()">
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12">
            <label class="form-label fw-bold">Ngày dạy : Tuần</label>
            <input type="week" min="2022-W01" max="{{ date('Y') }}-W99" name="week" class="form-control"
                value="{{ request()->week }}" onchange="this.form.submit()">
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12">
            <label class="form-label fw-bold">Ngày dạy : Năm</label>
            <x-form-input-school-years name="school_years" selected_id="{{ request()->school_years }}"
                autoSubmit="true" />
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12">
            <label class="form-label fw-bold">Trạng thái</label>
            <select name="status" class="form-control" onchange="this.form.submit()">
                <option value="">---</option>
                <option @selected(request()->status == $model::ACTIVE) value="{{ $model::ACTIVE   }}">Duyệt</option>
                <option @selected(request()->status != '' && request()->status == $model::INACTIVE)
                    value="{{ $model::INACTIVE }}">Chờ</option>
                <option @selected(request()->status == $model::CANCELED) value="{{ $model::CANCELED }}">Hủy</option>
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
                            <th>Mã</th>
                            <th>Người mượn</th>
                            <th>Ngày dạy</th>
                            <th>Thiết bị</th>
                            <th>Phòng bộ môn</th>
                            <th>Mục đích</th>
                            <th>Trạng thái</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @if( count( $items ) )
                        @foreach( $items as $key => $item )
                        <tr>
                            <td>#{{ $item->id }}</td>
                            <td>
                                {{ $item['user_name'] }}
                                <p class="mb-0 product-category">{{ $item['created_at_fm'] }}</p>
                            </td>
                            <td>{{ $item->borrow_date_fm }}</td>
                            <td>{!! $item->device_names !!} {!! $item->fake_device_names !!}</td>
                            <td>{!! $item->lab_names !!}</td>
                            <td>{{  isset(App\Models\Borrow::get_borrow_purposes()[$item->borrow_purpose]) ?  App\Models\Borrow::get_borrow_purposes()[$item->borrow_purpose] : $item->borrow_purpose }}</td>
                            <td>{!! $item->status_fm !!}</td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-light border dropdown-toggle dropdown-toggle-nocaret"
                                        type="button" data-bs-toggle="dropdown">
                                        <i class="bi bi-three-dots"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a class="dropdown-item" href="{{ route($route_prefix.'show',$item->id) }}">
                                                {{ __('sys.show') }}
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="{{ route($route_prefix.'copy',$item->id) }}">
                                                {{ __('sys.copy') }}
                                            </a>
                                        </li>
                                        @if($item->can_edit)
                                        <li>
                                            <a class="dropdown-item" href="{{ route($route_prefix.'edit',$item->id) }}">
                                                {{ __('sys.edit') }}
                                            </a>
                                        </li>
                                        @endif

                                        @if($item->can_delete)
                                        <li>
                                            <form action="{{ route($route_prefix.'destroy',$item->id) }}" method="post">
                                                @csrf
                                                @method('DELETE')
                                                <button onclick=" return confirm('{{ __('sys.confirm_delete') }}') "
                                                    class="dropdown-item">
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
                            <td colspan="5" class="text-center">{{ __('sys.no_item_found') }}</td>
                        </tr>
                        @endif
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
@extends('admin.layouts.master')
@section('title','Danh sách phiếu mượn')
@section('content')
    @include('globals.breadcrumb', [
        'page_title' => 'Danh sách phiếu',
        'actions' => [
            //'add_new' => route($route_prefix.'create'),
            //'export' => route($route_prefix.'export'),
        ],
    ])

    <!-- Item actions -->
    <form action="{{ route($route_prefix . 'index') }}" method="get">
        <p class="mb-2 text-danger">Lưu ý: Chỉ tìm kiếm một trong ba trường Ngày dạy | Tuần | Năm</p>
        @if( isset($startDate) )
        <p class="mb-2 text-success">Dữ liệu đang hiển thị từ <span class="fw-bold">{{ @$startDate->format('d/m/Y') }}</span> đến <span class="fw-bold">{{ @$endDate->format('d/m/Y') }}</span> </p>
        @endif
        @if( isset(request()->borrow_date) )
        <p class="mb-2 text-success">Dữ liệu đang hiển thị vào ngày <span class="fw-bold">{{ date('d/m/Y',strtotime(request()->borrow_date)) }}</span> </p>
        @endif
        <div class="row">
            <div class="col col-12 col-md-2">
                <label class="form-label fw-bold">Giáo Viên</label>
                <x-form-input-users name="user_id" selected_id="{{ request()->user_id }}" autoSubmit="true" />
            </div>
            <div class="col col-12 col-md-2">
                <label class="form-label fw-bold">Tổ</label>
                <x-form-input-nests name="nest_id" selected_id="{{ request()->nest_id }}" autoSubmit="true" />
            </div>
            <div class="col col-12 col-md-2">
                <label class="form-label fw-bold">Ngày dạy</label>
                <input type="date" name="borrow_date" class="form-control" value="{{ request()->borrow_date }}" onchange="this.form.submit()">
            </div>
            <div class="col col-12 col-md-2">
                <label class="form-label fw-bold">Ngày dạy : Tuần</label>
                <input type="week" min="2022-W01" max="{{ date('Y') }}-W99" name="week" class="form-control"
                    value="{{ request()->week }}" onchange="this.form.submit()">
            </div>
            <div class="col col-12 col-md-2">
                <label class="form-label fw-bold">Ngày dạy : Năm</label>
                <x-form-input-school-years name="school_years" selected_id="{{ request()->school_years }}"
                    autoSubmit="true" />
            </div>
            <div class="col col-12 col-md-2">
                <label class="form-label fw-bold">Trạng thái</label>
                <select name="status" class="form-control" onchange="this.form.submit()">
                    <option value="">---</option>
                    <option @selected(request()->status == $model::ACTIVE) value="{{ $model::ACTIVE }}">Duyệt</option>
                    <option @selected(request()->status !== null && request()->status == $model::INACTIVE) value="{{ $model::INACTIVE }}">Chờ</option>
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
                                <th width="15%">Người mượn</th>
                                <th width="12%">Ngày dạy</th>
                                <th>Thiết bị</th>
                                <th width="20%">Phòng bộ môn</th>
                                <th width="10%">Mục đích</th>
                                <th width="10%"></th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (count($items))
                                @foreach ($items as $key => $item)
                                    <tr>
                                        <td>#{{ $item->id }}</td>
                                        <td>
                                            {{ $item->user_name }}
                                            <p class="mb-0 product-category">{{ $item['created_at_fm'] }} - <span class="return-{{ $item->is_returned }}">{{ App\Models\Borrow::RETURN_STATUS[$item->is_returned] }}</span></p>
                                        </td>
                                        <td>{{ $item->borrow_date_fm }}</td>
                                        <td>{!! $item->device_names !!}</td>
                                        <td>{!! $item->lab_names !!}</td>
                                        <td>{{ isset($borrow_purposes[$item->borrow_purpose]) ? $borrow_purposes[$item->borrow_purpose] : '' }}</td>
                                        <td>{!! $item->status_fm !!}</td>
                                        @if (Auth::check() && ( ( Auth::user()->hasPermission( 'Borrow_delete' )) || ( Auth::user()->hasPermission( 'Borrow_update' ))  || ( Auth::user()->hasPermission( 'Borrow_view' ) )))
                                        <td>
                                            <div class="dropdown">
                                                <button
                                                    class="btn btn-sm btn-light border dropdown-toggle dropdown-toggle-nocaret"
                                                    type="button" data-bs-toggle="dropdown">
                                                    <i class="bi bi-three-dots"></i>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    @if (Auth::check() && Auth::user()->hasPermission('Borrow_view'))
                                                        <li>
                                                            <a class="dropdown-item"
                                                                href="{{ route($route_prefix . 'show', $item->id) }}">
                                                                {{ __('sys.show') }}
                                                            </a>
                                                        </li>
                                                    @endif
                                                    @if (Auth::check() && Auth::user()->hasPermission('Borrow_update'))
                                                        <li>
                                                            <a class="dropdown-item"
                                                                href="{{ route($route_prefix . 'edit', $item->id) }}">
                                                                {{ __('sys.edit') }}
                                                            </a>
                                                        </li>
                                                    @endif
                                                    @if (Auth::check() && Auth::user()->hasPermission('Borrow_approve'))
                                                        @if ($item->status == $model::INACTIVE)
                                                            <li>
                                                                <a onclick=" return confirm('{{ __('Bạn có chắc chắn duyệt phiếu này !') }}') "
                                                                    class="dropdown-item"
                                                                    href="{{ route($route_prefix . 'index', ['task' => 'approve', 'id' => $item->id]) }}">
                                                                    {{ __('sys.approve') }}
                                                                </a>
                                                            </li>
                                                        @endif
                                                        <li>
                                                            <form action="{{ route($route_prefix . 'destroy', $item->id) }}"
                                                                method="post">
                                                                @csrf
                                                                @if ($item->status == $model::CANCELED)
                                                                    @method('DELETE')
                                                                @else
                                                                    @method('PUT')
                                                                    <input type="hidden" name="status"
                                                                        value="{{ $model::CANCELED }}">
                                                                @endif
                                                                <button
                                                                    onclick=" return confirm('{{ $item->status == $model::CANCELED ? __('sys.force_delete') : __('sys.confirm_canceled') }}') "
                                                                    class="dropdown-item">
                                                                    {{ $item->status == $model::CANCELED ? __('sys.force_delete') : 'Hủy phiếu' }}
                                                                </button>
                                                            </form>
                                                        </li>
                                                    @endif
                                                </ul>
                                            </div>
                                        </td>
                                        @endif
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
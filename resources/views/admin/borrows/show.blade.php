@extends('admin.layouts.master')
@section('title','Xem phiếu mượn #'. $item->id)
@section('content')
@include('globals.breadcrumb',[
    'page_title' => 'Xem phiếu #'.$item->id,
    'actions' => [
        'ExportBorrowDetail' => route('admin.export.store',['type'=>'BorrowDetail','id'=>$item->id])
    ]
])

<div class="row">
    <div class="col-12 col-lg-12">
        <form id="borrow-form" action="" method="post">
            @csrf
            @method('PUT')
            @include('teacher.borrows.includes.form-show')
        </form>
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-end gap-2">
                    <a href="{{ route($route_prefix.'index') }}" class="btn btn-sm btn-dark">Quay lại</a>
                    <a href="{{ route($route_prefix.'edit',$item->id) }}" class="btn btn-sm btn-primary">Sửa</a>
                    @if (Auth::check() && Auth::user()->hasPermission('Borrow_approve'))
                        @if ($item->status == $model::INACTIVE)
                        <a onclick=" return confirm('{{ __('Bạn có chắc chắn duyệt phiếu này !') }}') "
                            class="btn btn-sm btn-success"
                            href="{{ route($route_prefix . 'index', ['task' => 'approve', 'id' => $item->id,'redirect'=>'show']) }}">
                            {{ __('sys.approve') }}
                        </a>
                        @endif
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
                                class="btn btn-sm btn-danger">
                                {{ $item->status == $model::CANCELED ? __('sys.force_delete') : 'Hủy phiếu' }}
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
<!--end row-->
@endsection
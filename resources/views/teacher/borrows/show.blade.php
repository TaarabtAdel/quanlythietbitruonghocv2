@extends('teacher.layouts.master')
@section('content')
@include('globals.breadcrumb',[
'page_title' => 'Xem phiếu #'.$item->id,
])

<div class="row">
    <div class="col-12 col-lg-12">
        <form id="borrow-form" action="" method="post">
            @csrf
            @method('PUT')
            @include($view_path.'.includes.form-show')
        </form>
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-end gap-2">
                    <a class="btn btn-sm btn-info" href="{{ route($route_prefix.'copy',$item->id) }}">
                        {{ __('sys.copy') }}
                    </a>
                    @if($item->can_edit)
                        <a class="btn btn-sm btn-primary" href="{{ route($route_prefix.'edit',$item->id) }}">
                            {{ __('sys.edit') }} phiếu
                        </a>
                    @endif
                    @if($item->can_delete)
                    <form action="{{ route($route_prefix.'destroy',$item->id) }}" method="post" class="col-12 col-lg-auto">
                        @csrf
                        @method('DELETE')
                        <button onclick=" return confirm('{{ __('sys.confirm_delete') }}') " class="btn btn-sm btn-danger px-4 mr-2 col-12 col-lg-auto">
                            {{ __('sys.delete') }} 
                        </button>
                    </form>
                    @endif
                    <a href="{{ route($route_prefix.'index') }}" class="btn btn-sm btn-dark">Quay lại</a>
                </div>
            </div>
        </div>
    </div>
</div>
<!--end row-->
@endsection

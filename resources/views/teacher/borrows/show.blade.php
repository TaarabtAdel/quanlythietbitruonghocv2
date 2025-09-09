@extends('admintheme::layouts.master')
@section('content')
@include('admintheme::includes.globals.breadcrumb',[
'page_title' => 'Xem phiếu #'.$item->id,
])

<div class="row">
    <div class="col-12 col-lg-12">
        <form id="borrow-form" action="" method="post">
            @csrf
            @method('PUT')
            @include('borrow::includes.form-show')
        </form>
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-end gap-2">
                    <a href="{{ route($route_prefix.'index') }}" class="btn btn-sm btn-dark">Quay lại</a>
                </div>
            </div>
        </div>
    </div>
</div>
<!--end row-->
@endsection

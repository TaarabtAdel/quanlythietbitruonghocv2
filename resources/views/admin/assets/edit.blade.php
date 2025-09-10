@extends('admin.layouts.master')
@section('title','Cập nhật thiết bị #'. $item->id)
@section('content')
@include('globals.breadcrumb',[
    'page_title' => 'Cập nhật thiết bị #'.$item->id,
])

<form action="{{ route($route_prefix.'update',$item->id) }}" method="post" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <div class="row">
        <div class="col-12 col-lg-8">
            @include($view_path.'.includes.form-left')
        </div>
        <div class="col-12 col-lg-4">
        @include($view_path.'.includes.form-right')
        </div>
    </div>
</form>
<!--end row-->
@endsection
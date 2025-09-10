@extends('admin.layouts.master')
@section('title','Cập nhật lớp học #'. $item->id)
@section('content')
@include('globals.breadcrumb',[
    'page_title' => 'Cập nhật lớp học #'.$item->id,
])

<form action="{{ isset($item) && $item->id 
        ? route($route_prefix.'update', $item->id) 
        : route($route_prefix.'store') }}" 
      method="post" 
      enctype="multipart/form-data">

    @csrf
    @if(isset($item) && $item->id)
        @method('PUT')
    @endif

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
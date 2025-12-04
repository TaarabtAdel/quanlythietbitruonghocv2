@extends('admin.layouts.master')
@section('title','Cập nhật phiếu kiểm kê #'. $item->id)
@section('content')
@include('globals.breadcrumb',[
    'page_title' => 'Cập nhật phiếu kiểm kê #'.$item->id,
])

<form action="{{ isset($item) && $item->id 
        ? route($route_prefix.'.update', $item->id) 
        : route($route_prefix.'.store') }}" 
      method="post" 
      enctype="multipart/form-data">

    @csrf
    @if(isset($item) && $item->id)
        @method('PUT')
        @include('admin.inventory_audits.includes.form')
    @endif

</form>

<!--end row-->
@endsection
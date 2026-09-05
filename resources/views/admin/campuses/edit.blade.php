@extends('admin.layouts.master')
@section('title', isset($item) && $item->id ? 'Cập nhật cơ sở #'.$item->id : 'Thêm cơ sở')
@section('content')
@include('globals.breadcrumb',[
    'page_title' => isset($item) && $item->id ? 'Cập nhật cơ sở' : 'Thêm cơ sở trực thuộc',
])

@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ isset($item) && $item->id
        ? route($route_prefix.'update', $item->id)
        : route($route_prefix.'store') }}"
      method="post">
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
@endsection

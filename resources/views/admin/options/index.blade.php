@extends('admin.layouts.master')
@section('title','Cấu hình hệ thống')
@section('content')
@include('globals.breadcrumb',[
    'page_title' => 'Cấu Hình',
    'actions' => []
])
<!-- Item actions -->
<form action="{{ route('admin.options.update') }}" method="post">
    @csrf
    @foreach( $optionGroupNames as $option_group_name => $option_group_label )
    <div class="card" id="{{ $option_group_name }}">
        <div class="card-body p-4">
            <div class="mb-2 fw-bold">{!! $option_group_label !!}</div>
            @foreach( $allOptions[$option_group_name] as $optionField )
            <div class="row mb-3">
                <label for="{{ $optionField['option_name'] }}" class="col-sm-5 col-form-label">{!! $optionField['option_label'] !!}</label>
                <div class="col-sm-7">
                    @if(  !empty($optionField['type']) && $optionField['type'] == 'checkbox' )
                    <input value="0" type="hidden" class="form-check-input" name="{{ $option_group_name }}[{{ $optionField['option_name'] }}]" @checked( !empty($optionField['option_value']) && $optionField['option_value'] == 0 )>
                    <input value="1" type="checkbox" class="form-check-input" name="{{ $option_group_name }}[{{ $optionField['option_name'] }}]" @checked( !empty($optionField['option_value']) && $optionField['option_value'] == 1 )>
                    @else
                    <input type="text" class="form-control" name="{{ $option_group_name }}[{{ $optionField['option_name'] }}]" placeholder="{{ $optionField['option_label'] }}" value="{{ $optionField['option_value'] ?? '' }}">
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endforeach
    <div class="card-footer">
        <button class="btn btn-primary">Lưu</button>
    </div>
</form>
@endsection
@extends('admin.layouts.master')

@section('title', $item['name'] ?? 'Văn bản từ Sở')

@section('content')
@include('globals.breadcrumb', ['page_title' => 'Văn bản từ Sở'])

<div class="card">
    <div class="card-body">
        <span class="badge bg-info mb-2">Từ Sở</span>
        <h5>{{ $item['name'] ?? '' }}</h5>
        @if (!empty($item['school_year']))
            <p><strong>Năm học:</strong> {{ $item['school_year'] }}</p>
        @endif
        @if (!empty($item['description']))
            <p>{{ $item['description'] }}</p>
        @endif
        @if (!empty($item['file_url']))
            <a href="{{ $item['file_url'] }}" target="_blank" class="btn btn-primary">Tải / xem tệp</a>
        @endif
    </div>
</div>
@endsection

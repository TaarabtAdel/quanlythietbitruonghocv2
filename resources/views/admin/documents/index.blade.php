@extends('admin.layouts.master')

@section('title','Danh sách văn bản')
@section('content')
@if (Auth::check() && Auth::user()->hasPermission(request()->type.'_create') && ($tab ?? 'internal') === 'internal')
    @include('globals.breadcrumb',[
        'page_title' => 'Danh sách văn bản',
        'actions' => [
            'add_new' => route($route_prefix.'create',['type'=>request()->type]),
        ]
    ])
@else
    @include('globals.breadcrumb',[
        'page_title' => 'Danh sách văn bản',
    ])
@endif

<ul class="nav nav-tabs mb-3">
    <li class="nav-item">
        <a class="nav-link {{ ($tab ?? 'internal') === 'internal' ? 'active' : '' }}" href="{{ route($route_prefix.'index', ['tab' => 'internal']) }}">Nội bộ</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ ($tab ?? 'internal') === 'sgd' ? 'active' : '' }}" href="{{ route($route_prefix.'index', ['tab' => 'sgd']) }}">Từ Sở</a>
    </li>
</ul>

@if (($tab ?? 'internal') === 'sgd' && empty($sgdConfigured))
    <div class="alert alert-warning">Chưa cấu hình URL portal Sở và API key tại <a href="{{ route('admin.options.index') }}">Cấu hình hệ thống</a>.</div>
@endif

<form action="{{ route($route_prefix.'index') }}" method="get">
    <input type="hidden" name="tab" value="{{ $tab ?? 'internal' }}">
    <input type="hidden" name="type" value="{{ request()->type }}">
    <div class="row">
        <div class="col">
            <label class="form-label fw-bold">Tên văn bản</label>
            <input class="form-control" name="name" type="text" placeholder="Nhập tên sau đó nhấn enter để tìm"
                value="{{ request()->name }}">
        </div>
        @if (($tab ?? 'internal') === 'internal')
        <div class="col col-lg-2">
            <label class="form-label fw-bold">Trạng Thái</label>
            <x-form-input-status name="status" status="{{ request()->status }}" autoSubmit="1" />
        </div>
        @endif
    </div>
</form>

<div class="card mt-4">
    <div class="card-body">
        <div class="table-responsive white-space-nowrap">
            <table class="table align-middle">
                <thead class="table-light">
                    <tr>
                        <th>STT</th>
                        <th>Tên</th>
                        @if (($tab ?? 'internal') === 'sgd')
                            <th>Năm học</th>
                        @else
                            <th>Trạng thái</th>
                        @endif
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @if( count( $items ) )
                    @foreach( $items as $key => $item )
                    <tr>
                        <td>{{ ($items->currentPage() - 1) * $items->perPage() + ($key + 1) }}</td>
                        <td>
                            @if (($tab ?? 'internal') === 'sgd')
                                <a href="{{ route($route_prefix.'sgd-show', $item['id']) }}">{{ $item['name'] }}</a>
                            @else
                                <a href="{{ route($route_prefix.'show',$item->id) }}">{{ $item->name }}</a>
                            @endif
                        </td>
                        <td>
                            @if (($tab ?? 'internal') === 'sgd')
                                {{ $item['school_year'] ?? '—' }}
                            @else
                                {!! $item->status_fm !!}
                            @endif
                        </td>
                        <td>
                            @if (($tab ?? 'internal') === 'internal')
                            <div class="dropdown">
                                <button class="btn btn-sm btn-light border dropdown-toggle dropdown-toggle-nocaret" type="button" data-bs-toggle="dropdown">
                                    <i class="bi bi-three-dots"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    @if (Auth::check() && Auth::user()->hasPermission(request()->type.'_update'))
                                        <li><a class="dropdown-item" href="{{ route($route_prefix.'edit',$item->id) }}">{{ __('sys.edit') }}</a></li>
                                    @endif
                                    @if (Auth::check() && Auth::user()->hasPermission(request()->type.'_delete'))
                                        <li>
                                            <form action="{{ route($route_prefix.'destroy',$item->id) }}" method="post">
                                                @csrf @method('DELETE')
                                                <button onclick="return confirm('{{ __('sys.confirm_delete') }}')" class="dropdown-item">{{ __('sys.delete') }}</button>
                                            </form>
                                        </li>
                                    @endif
                                </ul>
                            </div>
                            @elseif (!empty($item['file_url']))
                                <a class="btn btn-sm btn-outline-primary" href="{{ $item['file_url'] }}" target="_blank">Tải</a>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                    @else
                    <tr><td colspan="4" class="text-center">{{ __('sys.no_item_found') }}</td></tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
    @if( count( $items ) )
    <div class="card-footer pb-0">
        @include('globals.pagination')
    </div>
    @endif
</div>
@endsection

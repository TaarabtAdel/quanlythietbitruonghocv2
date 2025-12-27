@extends('admin.layouts.master')
@section('content')
@if (Auth::check() && Auth::user()->hasPermission(request()->type.'_create'))
    @include('globals.breadcrumb',[
        'page_title' => 'Danh sách phân phối chương trình',
        'actions' => [
            'add_new' => route($route_prefix.'create',['type'=>request()->type]),
        ]
    ])
@else
    @include('globals.breadcrumb',[
        'page_title' => 'Danh sách phân phối chương trình',
    ])
@endif


<!-- Item actions -->
<form action="{{ route($route_prefix.'index') }}" method="get">
    <input type="hidden" name="type" value="{{ request()->type }}">
    <div class="row">
        <div class="col">
            <label class="form-label fw-bold">Bộ môn</label>
            <x-form-input-departments name="department_id" selected_id="{{ request()->department_id }}" autoSubmit="1" />
        </div>
        <div class="col">
            <label class="form-label fw-bold">Năm học</label>
            <x-form-input-school-years name="academic_year" selected_id="{{ request()->academic_year }}" autoSubmit="1" />
        </div>
        <div class="col">
            <label class="form-label fw-bold">Khối</label>
            <x-form-input-grade name="grade" selected_id="{{ request()->grade }}" autoSubmit="1" />
        </div>
        <div class="col">
            <label class="form-label fw-bold">Phân môn</label>
            <select name="subject_type" class="form-control" onchange="this.form.submit()">
                <option value="">--- Tất cả ---</option>
                <option value="mon_chinh" {{ request()->subject_type == 'mon_chinh' ? 'selected' : '' }}>Môn chính</option>
                <option value="chuyen_de" {{ request()->subject_type == 'chuyen_de' ? 'selected' : '' }}>Chuyên đề</option>
            </select>
        </div>
    </div>
</form>

<div class="card mt-4">
    <div class="card-body">
        <div class="product-table">
            <div class="table-responsive white-space-nowrap">
                <table class="table align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>STT</th>
                            <th>Năm học</th>
                            <th>Bộ môn</th>
                            <th>Khối</th>
                            <th>Phân môn</th>
                            <th>Số bài học</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @if( count( $items ) )
                        @foreach( $items as $key => $item )
                        <tr>
                            <td>{{ ($items->currentPage() - 1) * $items->perPage() + ($key + 1) }}</td>
                            <td>{{ $item->academic_year }}</td>
                            <td>{{ $item->department->name ?? '-' }}</td>
                            <td>{{ $item->grade_name }}</td>
                            <td>
                                @php
                                    $typeNames = [
                                        'mon_chinh' => 'Môn chính',
                                        'chuyen_de' => 'Chuyên đề',
                                    ];
                                @endphp
                                {{ $typeNames[$item->subject_type] ?? $item->subject_type ?? '-' }}
                            </td>
                            <td>{{ $item->details_count }}</td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-light border dropdown-toggle dropdown-toggle-nocaret"
                                        type="button" data-bs-toggle="dropdown">
                                        <i class="bi bi-three-dots"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        @if (Auth::check() && Auth::user()->hasPermission(request()->type.'_update'))
                                            <li>
                                                <a class="dropdown-item"
                                                    href="{{ route($route_prefix.'edit',$item->id) }}?page={{ request()->page }}">
                                                    {{ __('sys.edit') }}
                                                </a>
                                            </li>
                                        @endif
                                        @if (Auth::check() && Auth::user()->hasPermission(request()->type.'_view'))
                                            <li>
                                                <a class="dropdown-item"
                                                    href="{{ route($route_prefix.'show',$item->id) }}">
                                                    {{ __('sys.view') }}
                                                </a>
                                            </li>
                                        @endif
                                        @if (Auth::check() && Auth::user()->hasPermission(request()->type.'_delete'))
                                            <li>
                                                <form
                                                    action="{{ route($route_prefix.'destroy',$item->id) }}?page={{ request()->page }}"
                                                    method="post">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button onclick=" return confirm('{{ __('sys.confirm_delete') }}') "
                                                        class="dropdown-item">
                                                        {{ __('sys.delete') }}
                                                    </button>
                                                </form>
                                            </li>
                                        @endif
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                        @else
                        <tr>
                            <td colspan="7" class="text-center">{{ __('sys.no_item_found') }}</td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @if( count( $items ) )
    <div class="card-footer pb-0">
        @include('globals.pagination')
    </div>
    @endif
</div>

@endsection

<!--breadcrumb-->
<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
    <div class="breadcrumb-title pe-3">Home</div>
    <div class="ps-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 p-0">
                <li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">{{ $page_title ?? '' }}</li>
            </ol>
        </nav>
    </div>
    <div class="ms-auto">
        @if( !empty($actions) )
            @foreach( $actions as $action_name => $route_link )
                @switch( $action_name )
                    @case('add_new')
                        <a href="{{ $route_link }}" class="btn btn-primary px-4"><i class="bi bi-plus-lg me-2"></i>{{ __('sys.add_new') }}</a>
                        @break
                    @case('export')
                    <button class="btn btn-light px-4"><i class="bi bi-box-arrow-right me-2"></i>Export</button>
                        @break
                @endswitch
            @endforeach
        @endif
        <!-- <div class="btn-group">
            <button type="button" class="btn btn-primary">Settings</button>
            <button type="button" class="btn btn-primary split-bg-primary dropdown-toggle dropdown-toggle-split"
                data-bs-toggle="dropdown"> <span class="visually-hidden">Toggle Dropdown</span>
            </button>
            <div class="dropdown-menu dropdown-menu-right dropdown-menu-lg-end">
                <a class="dropdown-item" href="javascript:;">Action</a>
                <a class="dropdown-item" href="javascript:;">Another action</a>
                <a class="dropdown-item" href="javascript:;">Something else here</a>
                <div class="dropdown-divider"></div> <a class="dropdown-item" href="javascript:;">Separated link</a>
            </div>
        </div> -->
    </div>
</div>
<!--end breadcrumb-->
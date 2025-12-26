<div class="card">
    <div class="card-body">
        <div class="d-flex align-items-center justify-content-between">
            <a href="{{ route($route_prefix.'index',['page'=>request()->page]) }}" class="btn btn-danger px-4">{{ __('sys.back') }}</a>
            <button type="submit" class="btn btn-primary px-4">{{ __('sys.save') }}</button>
        </div>
    </div>
</div>

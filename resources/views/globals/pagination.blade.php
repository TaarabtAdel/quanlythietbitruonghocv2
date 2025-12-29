<div class="row align-items-center mb-3">
    <div class="col-md-6">
        @if ( isset($route_prefix) && Route::has($route_prefix.'bulkAction'))
        <div id="bulkActionContainer" class="input-group" style="display: none; width: auto;">
            <select id="bulkActionSelect" class="form-select form-select-sm" style="max-width: 200px;">
                <option value="">-- Chọn hành động --</option>
                <option value="delete">Xóa <span class="selected-count"></span> mục đã chọn</option>
                <option value="restore">Khôi phục mục đã chọn</option>
                <!-- <option value="force_delete">Xóa vĩnh viễn mục đã chọn</option> -->
            </select>
            <button id="applyBulkAction" 
                    class="btn btn-sm btn-outline-danger" 
                    data-action="{{ route($route_prefix.'bulkAction') }}">
                Áp dụng (<span id="checkCount">0</span>)
            </button>
        </div>
        @endif
    </div>

    <div class="col-md-6 text-end">
        <nav class="d-inline-block">
            {{ $items->appends(request()->query())->links() }}
        </nav>
    </div>
</div>
<div class="row">
    <div class="col-lg-4">
        <div class="form-group mb-4">
            <label class="form-label">Mã Phiếu Mượn <span class="text-danger">(*)</span></label>
            <input type="number" name="id" class="form-control" value="{{ request()->id }}">
            <x-form-input-error field="id" />
            @if( request()->id )
            <p class="mt-2">Vui lòng nhấn <strong>TIẾN HÀNH</strong> để xuất phiếu mượn #{{ request()->id }}</p>
            @endif
        </div>
    </div>
    <div class="col-lg-8">
        <div id="preview-demo-img">
            <img class="img-fluid" src="/system/export/preview/borrowdetail.png" alt="">
        </div>
    </div>
</div>
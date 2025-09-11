<div class="form-group mb-4">
    <label class="form-label">Mã Phiếu Mượn <span class="text-danger">(*)</span></label>
    <input type="number" name="id" class="form-control" value="{{ request()->id }}">
    <x-form-input-error field="id" />
    @if( request()->id )
    <p class="mt-2">Vui lòng nhấn <strong>TIẾN HÀNH</strong> để xuất phiếu mượn #{{ request()->id }}</p>
    @endif
</div>
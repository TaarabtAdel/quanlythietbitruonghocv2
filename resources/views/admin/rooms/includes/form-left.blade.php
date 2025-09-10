<div class="card">
    <div class="card-body">
        <div class="mb-4">
            <label class="mb-3">Tên <span class="text-danger">(*)</span></label>
            <input type="text" class="form-control" name="name" value="{{ $item->name ?? old('name') }}">
            <x-form-input-error field="name"/>
        </div>
    </div>
</div>
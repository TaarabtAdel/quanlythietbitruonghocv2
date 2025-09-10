<div class="card">
    <div class="card-body">
        <div class="mb-4">
            <label class="mb-3">Tên <span class="text-danger">(*)</span></label>
            <input type="text" class="form-control" name="name" value="{{ $item->name ?? old('name') }}">
            <x-form-input-error field="name"/>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-header">
        <div class="text-uppercase fw-bold">Thông tin phòng học</div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-lg-12">
                <div class="mb-4">
                <label class="mb-3">Bộ môn <span class="text-danger">(*)</span></label>
                    <x-form-input-departments name="department_id" selected_id="{{ old('department_id',@$item->department_id) }}"/>
                    <x-form-input-error field="department_id"/>
                </div>
            </div>
        </div>
    </div>
</div>
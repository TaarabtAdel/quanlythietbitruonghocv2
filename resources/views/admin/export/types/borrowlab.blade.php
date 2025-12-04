
<div class="form-group mb-4">
    <label class="form-label fw-bold">Tuần : <span class="text-danger">(*)</span></label>
    <input type="week" min="2022-W01" max="{{ date('Y') }}-W99" name="week" class="form-control" value="{{ request()->week }}">
    <x-form-input-error field="week" />
</div>
<div class="form-group mb-4">
    <label class="form-label fw-bold">Chọn Phòng :</label>
    <x-form-input-labs name="lab_id" selected_id="{{ request()->lab_id }}" />
    <p class="mb-0">Không chọn có nghĩa là xuất cho tất cả các phòng</p>
    <x-form-input-error field="lab_id" />
</div>
<div class="form-group mb-4">
    <label class="form-label fw-bold">Chọn Loại Xuất :</label>
    <select name="export_type" class="form-select">
        <option value="detail">Theo Phòng</option>
        <option value="all">Tổng hợp</option>
    </select>
    <x-form-input-error field="export_type" />
</div>
<div class="row">
    <div class="col-lg-6">
        <div class="form-group mb-4">
            <label class="form-label fw-bold">Chọn năm học : <span class="text-danger">(*)</span></label>
            <x-form-input-school-years name="year" selected_id="{{ request()->year }}" id="year" />
            <x-form-input-error field="year" />
        </div>
        <div class="form-group mb-4">
            <label class="form-label fw-bold">Từ ngày : <span class="text-danger">(*)</span></label>
            <input type="date" name="start_date" class="form-control" value="{{ request()->start_date }}">
            <!-- <p class="mb-0">Nếu đã chọn Năm thì không chọn Tuần</p> -->
            <x-form-input-error field="start_date" />
        </div>
        <div class="form-group mb-4">
            <label class="form-label fw-bold">Đến ngày : <span class="text-danger">(*)</span></label>
            <input type="date" name="end_date" class="form-control" value="{{ request()->end_date }}">
            <!-- <p class="mb-0">Nếu đã chọn Tuần thì không chọn Năm</p> -->
            <x-form-input-error field="end_date" />
        </div>
        <div class="form-group mb-4">
            <label class="form-label fw-bold">Giáo Viên <span class="text-danger">(*)</span></label>
            <x-form-input-users name="user_id" selected_id="{{ request()->user_id }}" />
            <x-form-input-error field="user_id" />
        </div>
        <div class="form-group mb-4">
            <label class="form-label fw-bold">Xuất theo : <span class="text-danger">(*)</span></label>
            <select name="export_by" class="form-control preview-demo" id="export_by">
                <option value="device" data-img="/system/export/preview/borrowdevicesuser.png">Thiết Bị</option>
                <option value="phieu" data-img="/system/export/preview/borrowdevicesuser_2.png">Phiếu</option>
            </select>
            <span class="text-success">* Xuất theo phiếu sẽ tạo ra các sheet, mỗi sheet là một phiếu</span>
            <x-form-input-error field="export_by" />
        </div>
    </div>
    <div class="col-lg-6">
        <div id="preview-demo-img"></div>
    </div>
</div>
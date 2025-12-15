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
            <x-form-input-error field="start_date" />
        </div>
        <div class="form-group mb-4">
            <label class="form-label fw-bold">Đến ngày : <span class="text-danger">(*)</span></label>
            <input type="date" name="end_date" class="form-control" value="{{ request()->end_date }}">
            <x-form-input-error field="end_date" />
        </div>
        <div class="form-group mb-4">
            <label class="form-label fw-bold">Chọn Tổ</label>
            <x-form-input-nests name="nest_id" selected_id="{{ request()->nest_id }}" />
            <p class="mb-0">Không chọn có nghĩa là xuất cho tất cả các tổ</p>
            <!-- <x-form-input-error field="nest_id" /> -->
        </div>

        <div class="form-group mb-4 d-none">
            <label class="form-label fw-bold">Xuất theo : <span class="text-danger">(*)</span></label>
            <select name="export_by" class="form-control" id="export_by">
                <option value="tiet">Tiết</option>
                <option value="phieu">Phiếu</option>
            </select>
            <span class="text-success">* Xuất theo tiết sẽ đếm dữ liệu theo tiết học</span>
            <x-form-input-error field="export_by" />
        </div>
    </div>
    <div class="col-lg-6">
        <div id="preview-demo-img">
            <img class="img-fluid" src="/system/export/preview/borrowlabs.png" alt="">
        </div>
    </div>
</div>
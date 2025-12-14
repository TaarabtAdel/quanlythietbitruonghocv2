<div class="row">
    <div class="col-lg-6">
        <div class="form-group mb-4">
            <label class="form-label fw-bold">Tuần : <span class="text-danger">(*)</span></label>
            <!-- <input type="week" min="2022-W01" max="{{ date('Y') }}-W99" name="week" class="form-control"
                value="{{ request()->week }}"> -->
            <x-form-input-school-week name="week" selected_id="{{ request()->week }}" />
            <x-form-input-error field="sw_start_week" />
        </div>
        <div class="form-group mb-4">
            <label class="form-label fw-bold">Chọn Phòng :</label>
            <x-form-input-labs name="lab_id" selected_id="{{ request()->lab_id }}" />
            <p class="mb-0">Không chọn có nghĩa là xuất cho tất cả các phòng</p>
            <x-form-input-error field="lab_id" />
        </div>
        <div class="form-group mb-4">
            <label class="form-label fw-bold">Chọn Loại Xuất :</label>
            <select name="export_type" class="form-select preview-demo">
                <option value="detail" data-img="/system/export/preview/borrowlab.png">Theo Phòng</option>
                <option value="all" data-img="/system/export/preview/borrowlab_2.png">Tổng hợp</option>
            </select>
            <x-form-input-error field="export_type" />
        </div>
        <div class="mb-4">
            <div class="d-md-flex d-grid align-items-center gap-3">
                <button class="btn btn-primary px-4">Tiến Hành</button>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div id="preview-demo-img"></div>
    </div>
</div>
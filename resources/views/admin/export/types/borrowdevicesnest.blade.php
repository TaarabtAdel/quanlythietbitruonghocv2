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
            <label class="form-label fw-bold">Chọn Tổ</label>
            <x-form-input-nests name="nest_id" selected_id="{{ request()->nest_id }}" />
            <p class="mb-0">Không chọn có nghĩa là xuất cho tất cả các tổ</p>
            <!-- <x-form-input-error field="nest_id" /> -->
        </div>

    </div>
    <div class="col-lg-6">
        <div id="preview-demo-img">
            <img class="img-fluid" src="/system/export/preview/borrowdevicesnest.png" alt="">
        </div>
    </div>
</div>
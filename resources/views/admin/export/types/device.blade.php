<div class="row">
    <div class="col-lg-6">
        <div class="form-group mb-4">
            <label class="form-label fw-bold">Loại Thiết Bị</label>
            <x-form-input-device-types name="device_type_id" selected_id="{{ request()->device_type_id }}" />
            <p class="mb-0">Không chọn có nghĩa là xuất cho tất cả các loại</p>
        </div>
        <div class="form-group mb-4">
            <label class="form-label fw-bold">Môn Học</label>
            <x-form-input-departments name="department_id" selected_id="{{ request()->department_id }}" />
            <p class="mb-0">Không chọn có nghĩa là xuất cho tất cả các môn</p>
        </div>
    </div>
    <div class="col-lg-6">
        <div id="preview-demo-img">
            <img class="img-fluid" src="/system/export/preview/device.png" alt="">
        </div>
    </div>
</div>
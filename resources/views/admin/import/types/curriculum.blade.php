<div class="mb-4">
    <h5 class="mb-4">Bạn đang chuẩn bị nhập dữ liệu vào: Chương trình đào tạo</h5>
    <p class="mb-0">- Dữ liệu mới sẽ được thêm vào chương trình đào tạo.</p>
    <p class="mb-0">- Nhấn vào <a target="_blank" href="{{ asset('system/import/'.$templateFile) }}?t={{ time() }}">đây</a> để tải tệp nhập liệu mẫu </p>
    <p class="mb-0">- Tải file dữ liệu đã được cập nhật lên và nhấn <strong>Tiến Hành</strong></p>
</div>

<div class="row">
    <div class="col-lg-6">
        <div class="form-group mb-4">
            <label class="form-label fw-bold">Năm học <span class="text-danger">(*)</span></label>
            <x-form-input-school-years name="school_year" selected_id="{{ old('school_year') }}" />
            <x-form-input-error field="school_year" />
        </div>

        <div class="form-group mb-4">
            <label class="form-label fw-bold">Bộ môn <span class="text-danger">(*)</span></label>
            <x-form-input-departments name="department_id" selected_id="{{ old('department_id') }}" />
            <x-form-input-error field="department_id" />
        </div>

        <div class="form-group mb-4">
            <label class="form-label fw-bold">Khối <span class="text-danger">(*)</span></label>
            <select name="grade" class="form-control" required>
                <option value="">--- Chọn khối ---</option>
                <option value="10" {{ old('grade') == '10' ? 'selected' : '' }}>Khối 10</option>
                <option value="11" {{ old('grade') == '11' ? 'selected' : '' }}>Khối 11</option>
                <option value="12" {{ old('grade') == '12' ? 'selected' : '' }}>Khối 12</option>
            </select>
            <x-form-input-error field="grade" />
        </div>

        <div class="form-group mb-4">
            <label class="form-label fw-bold">Loại phân môn</label>
            <select name="subject_type" class="form-control">
                <option value="">--- Chọn loại phân môn ---</option>
                <option value="co_ban" {{ old('subject_type') == 'co_ban' ? 'selected' : '' }}>Cơ bản</option>
                <option value="chuyen_sau" {{ old('subject_type') == 'chuyen_sau' ? 'selected' : '' }}>Chuyên sâu</option>
                <option value="tu_chon" {{ old('subject_type') == 'tu_chon' ? 'selected' : '' }}>Tự chọn</option>
                <option value="bat_buoc" {{ old('subject_type') == 'bat_buoc' ? 'selected' : '' }}>Bắt buộc</option>
            </select>
            <x-form-input-error field="subject_type" />
        </div>


        <div class="form-group mb-4">
            <label class="form-label">Chọn Tệp (đuôi .xls, .xlsx) <span class="text-danger">(*)</span></label>
            <input type="file" name="file" class="form-control" accept=".xls, .xlsx" required>
            <x-form-input-error field="file"/>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title">Hướng dẫn nhập dữ liệu</h6>
                <ul class="mb-0">
                    <li>File Excel phải có định dạng .xls hoặc .xlsx</li>
                    <li>Cột đầu tiên (A): Loại phân môn</li>
                    <li>Cột thứ hai (B): Tuần</li>
                    <li>Cột thứ ba (C): Số tiết</li>
                    <li>Cột thứ tư (D): Tên bài học <strong>(bắt buộc)</strong></li>
                    <li>Cột thứ năm (E): Ghi chú</li>
                    <li>Dòng đầu tiên là tiêu đề, sẽ được bỏ qua</li>
                    <li>Dữ liệu mới sẽ được thêm vào chương trình đào tạo</li>
                </ul>
            </div>
        </div>
    </div>
</div>


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
                <!-- Từ 1 -> 12 -->
                @for($i = 1; $i <= 12; $i++)
                    <option value="{{ $i }}" {{ old('grade') == $i ? 'selected' : '' }}>Khối {{ $i }}</option>
                @endfor
            </select>
            <x-form-input-error field="grade" />
        </div>

        <div class="form-group mb-4">
            <label class="form-label fw-bold">Loại phân môn</label>
            <select name="subject_type" class="form-control">
                <option value="">--- Chọn loại phân môn ---</option>
                <option value="mon_chinh" {{ old('subject_type') == 'mon_chinh' ? 'selected' : '' }}>Môn chính</option>
                <option value="chuyen_de" {{ old('subject_type') == 'chuyen_de' ? 'selected' : '' }}>Chuyên đề</option>
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
                    <li>Cột đầu tiên (A): Tuần</li>
                    <li>Cột thứ hai (B): Số tiết</li>
                    <li>Cột thứ ba (C): Tên bài học <strong>(bắt buộc)</strong></li>
                    <li>Cột thứ tư (D): Ghi chú</li>
                    <li>Dòng đầu tiên là tiêu đề, sẽ được bỏ qua</li>
                    <li>Dữ liệu mới sẽ được thêm vào chương trình đào tạo</li>
                </ul>
            </div>
        </div>
    </div>
</div>


<div class="card">
    <div class="card-body">
        <div class="mb-4">
            <label class="mb-3">Năm học <span class="text-danger">(*)</span></label>
            <x-form-input-school-years name="academic_year" selected_id="{{ $item->academic_year ?? old('academic_year') }}" />
            <x-form-input-error field="academic_year"/>
        </div>

        <div class="mb-4">
            <label class="mb-3">Bộ môn <span class="text-danger">(*)</span></label>
            <x-form-input-departments name="department_id" selected_id="{{ $item->department_id ?? old('department_id') }}" />
            <x-form-input-error field="department_id"/>
        </div>

        <div class="mb-4">
            <label class="mb-3">Khối</label>
            <select name="grade" class="form-control">
                <option value="">--- Chọn khối ---</option>
                <option value="10" {{ ($item->grade ?? old('grade')) == '10' ? 'selected' : '' }}>Khối 10</option>
                <option value="11" {{ ($item->grade ?? old('grade')) == '11' ? 'selected' : '' }}>Khối 11</option>
                <option value="12" {{ ($item->grade ?? old('grade')) == '12' ? 'selected' : '' }}>Khối 12</option>
            </select>
            <x-form-input-error field="grade"/>
        </div>

        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Chi tiết phân phối chương trình</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="curriculum-details">
                        <thead class="table-light">
                            <tr>
                                <th width="50" class="text-center">STT</th>
                                <th>Loại phân môn</th>
                                <th width="100" class="text-center">Tuần</th>
                                <th width="100" class="text-center">Số tiết</th>
                                <th>Tên bài học <span class="text-danger">(*)</span></th>
                                <th>Ghi chú</th>
                                <th width="80" class="text-center">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(isset($item) && $item->id && count($item->details))
                                @foreach($item->details as $index => $detail)
                                <tr class="detail-row" data-index="{{ $index }}">
                                    <td class="text-center align-middle">{{ $index + 1 }}</td>
                                    <td>
                                        <select name="details[{{ $index }}][sub_subject_type]" class="form-control form-control-sm">
                                            <option value="">---</option>
                                            <option value="co_ban" {{ $detail->sub_subject_type == 'co_ban' ? 'selected' : '' }}>Cơ bản</option>
                                            <option value="chuyen_sau" {{ $detail->sub_subject_type == 'chuyen_sau' ? 'selected' : '' }}>Chuyên sâu</option>
                                            <option value="tu_chon" {{ $detail->sub_subject_type == 'tu_chon' ? 'selected' : '' }}>Tự chọn</option>
                                            <option value="bat_buoc" {{ $detail->sub_subject_type == 'bat_buoc' ? 'selected' : '' }}>Bắt buộc</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" name="details[{{ $index }}][week]" class="form-control form-control-sm text-center" value="{{ $detail->week }}" placeholder="Tuần" min="1">
                                    </td>
                                    <td>
                                        <input type="number" name="details[{{ $index }}][lesson_number]" class="form-control form-control-sm text-center" value="{{ $detail->lesson_number }}" placeholder="Số tiết" min="1">
                                    </td>
                                    <td>
                                        <input type="text" name="details[{{ $index }}][lesson_name]" class="form-control form-control-sm" value="{{ $detail->lesson_name }}" placeholder="Tên bài học" required>
                                    </td>
                                    <td>
                                        <input type="text" name="details[{{ $index }}][note]" class="form-control form-control-sm" value="{{ $detail->note }}" placeholder="Ghi chú">
                                    </td>
                                    <td class="text-center align-middle">
                                        <button type="button" class="btn btn-sm btn-danger remove-detail">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            @else
                                <tr class="detail-row" data-index="0">
                                    <td class="text-center align-middle">1</td>
                                    <td>
                                        <select name="details[0][sub_subject_type]" class="form-control form-control-sm">
                                            <option value="">---</option>
                                            <option value="co_ban">Cơ bản</option>
                                            <option value="chuyen_sau">Chuyên sâu</option>
                                            <option value="tu_chon">Tự chọn</option>
                                            <option value="bat_buoc">Bắt buộc</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" name="details[0][week]" class="form-control form-control-sm text-center" placeholder="Tuần" min="1">
                                    </td>
                                    <td>
                                        <input type="number" name="details[0][lesson_number]" class="form-control form-control-sm text-center" placeholder="Số tiết" min="1">
                                    </td>
                                    <td>
                                        <input type="text" name="details[0][lesson_name]" class="form-control form-control-sm" placeholder="Tên bài học" required>
                                    </td>
                                    <td>
                                        <input type="text" name="details[0][note]" class="form-control form-control-sm" placeholder="Ghi chú">
                                    </td>
                                    <td class="text-center align-middle">
                                        <button type="button" class="btn btn-sm btn-danger remove-detail">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    <button type="button" class="btn btn-sm btn-primary add-detail">
                        <i class="bi bi-plus-circle"></i> Thêm bài học
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

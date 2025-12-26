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
                @for($i = 1; $i <= 12; $i++)
                    <option value="{{ $i }}" {{ ($item->grade ?? old('grade')) == $i ? 'selected' : '' }}>Khối {{ $i }}</option>
                @endfor
            </select>
            <x-form-input-error field="grade"/>
        </div>

        <div class="mb-4">
            <label class="mb-3">Phân môn</label>
            <select name="subject_type" class="form-control">
                <option value="">--- Chọn phân môn ---</option>
                <option value="mon_chinh" {{ ($item->subject_type ?? old('subject_type')) == 'mon_chinh' ? 'selected' : '' }}>Môn chính</option>
                <option value="chuyen_de" {{ ($item->subject_type ?? old('subject_type')) == 'chuyen_de' ? 'selected' : '' }}>Chuyên đề</option>
            </select>
            <x-form-input-error field="subject_type"/>
        </div>

        <div class="mb-4">
            <label class="mb-3">Ghi chú</label>
            <textarea class="form-control" name="note" rows="3">{{ $item->note ?? old('note') }}</textarea>
            <x-form-input-error field="note"/>
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
                                <th width="100" class="text-center">Tuần PPCT</th>
                                <th width="100" class="text-center">Tiết PPCT</th>
                                <th>Tên bài học <span class="text-danger">(*)</span></th>
                                <th width="80" class="text-center">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(isset($item) && $item->id && count($item->details))
                                @foreach($item->details as $index => $detail)
                                <tr class="detail-row" data-index="{{ $index }}">
                                    <td class="text-center align-middle">{{ $index + 1 }}</td>
                                    <td>
                                        <input type="number" name="details[{{ $index }}][week]" class="form-control form-control-sm text-center" value="{{ $detail->week }}" placeholder="Tuần" min="1">
                                    </td>
                                    <td>
                                        <input type="number" name="details[{{ $index }}][lesson_number]" class="form-control form-control-sm text-center" value="{{ $detail->lesson_number }}" placeholder="Số tiết" min="1">
                                    </td>
                                    <td>
                                        <input type="text" name="details[{{ $index }}][lesson_name]" class="form-control form-control-sm" value="{{ $detail->lesson_name }}" placeholder="Tên bài học" required>
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
                                        <input type="number" name="details[0][week]" class="form-control form-control-sm text-center" placeholder="Tuần PPCT" min="1">
                                    </td>
                                    <td>
                                        <input type="number" name="details[0][lesson_number]" class="form-control form-control-sm text-center" placeholder="Tiết PPCT" min="1">
                                    </td>
                                    <td>
                                        <input type="text" name="details[0][lesson_name]" class="form-control form-control-sm" placeholder="Tên bài học" required>
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

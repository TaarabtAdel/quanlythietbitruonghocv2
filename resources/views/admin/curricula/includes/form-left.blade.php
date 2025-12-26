<div class="card">
    <div class="card-body">
        <div class="mb-4">
            <label class="mb-3">Tên chương trình đào tạo <span class="text-danger">(*)</span></label>
            <input type="text" class="form-control" name="name" value="{{ $item->name ?? old('name') }}" required>
            <x-form-input-error field="name"/>
        </div>

        <div class="mb-4">
            <label class="mb-3">Mã chương trình</label>
            <input type="text" class="form-control" name="code" value="{{ $item->code ?? old('code') }}">
            <x-form-input-error field="code"/>
        </div>

        <div class="mb-4">
            <label class="mb-3">Bộ môn</label>
            <x-form-input-departments name="department_id" selected_id="{{ $item->department_id ?? old('department_id') }}" />
            <x-form-input-error field="department_id"/>
        </div>

        <div class="mb-4">
            <label class="mb-3">Mô tả</label>
            <textarea class="form-control" name="description" rows="4">{{ $item->description ?? old('description') }}</textarea>
            <x-form-input-error field="description"/>
        </div>

        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Chi tiết chương trình đào tạo</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="curriculum-details">
                        <thead class="table-light">
                            <tr>
                                <th width="50" class="text-center">STT</th>
                                <th>Tên môn học <span class="text-danger">(*)</span></th>
                                <th width="100" class="text-center">Số tín chỉ</th>
                                <th width="100" class="text-center">Số giờ</th>
                                <th width="100" class="text-center">Học kỳ</th>
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
                                        <input type="text" name="details[{{ $index }}][subject_name]" class="form-control form-control-sm" value="{{ $detail->subject_name }}" placeholder="Tên môn học" required>
                                    </td>
                                    <td>
                                        <input type="number" name="details[{{ $index }}][credits]" class="form-control form-control-sm text-center" value="{{ $detail->credits }}" min="0">
                                    </td>
                                    <td>
                                        <input type="number" name="details[{{ $index }}][hours]" class="form-control form-control-sm text-center" value="{{ $detail->hours }}" min="0">
                                    </td>
                                    <td>
                                        <input type="number" name="details[{{ $index }}][semester]" class="form-control form-control-sm text-center" value="{{ $detail->semester }}" placeholder="Học kỳ" min="1">
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
                                        <input type="text" name="details[0][subject_name]" class="form-control form-control-sm" placeholder="Tên môn học" required>
                                    </td>
                                    <td>
                                        <input type="number" name="details[0][credits]" class="form-control form-control-sm text-center" value="0" min="0">
                                    </td>
                                    <td>
                                        <input type="number" name="details[0][hours]" class="form-control form-control-sm text-center" value="0" min="0">
                                    </td>
                                    <td>
                                        <input type="number" name="details[0][semester]" class="form-control form-control-sm text-center" placeholder="Học kỳ" min="1">
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
                        <i class="bi bi-plus-circle"></i> Thêm môn học
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>


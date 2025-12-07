<div class="card">
    <div class="card-body">

        <div class="card-title fw-bold text-uppercase">
            @if(isset($audit))
            THÔNG TIN PHIẾU #{{ $item->id }} - {{ $item->status }}
            @else
            XEM PHIẾU KIỂM KÊ MỚI
            @endif
        </div>
        <div class="my-3 border-top"></div>

        <div class="row">
            <div class="col-lg-6">
                <div class="form-group">
                    <label class="form-label" for="name">Nội dung / Tiêu đề Phiếu</label>
                    <input name="name" type="text" class="form-control" value="{{ old('name', $item->name ?? '') }}">
                    <span class="input-error text-danger">@error('name') {{ $message }} @enderror</span>
                </div>
            </div>

            <div class="col-lg-3">
                <div class="form-group">
                    <label class="form-label">Người Tạo</label>
                    <p class="form-control-static fw-bold">
                        @if(isset($audit))
                        {{ @$item->user->name ?? 'N/A' }}
                        @else
                        {{ Auth::user()->name ?? 'Bạn' }} (Tạo mới)
                        @endif
                    </p>
                </div>
            </div>

            <div class="col-lg-3">
                <div class="form-group">
                    <label class="form-label">Ngày Tạo Phiếu</label>
                    <p class="form-control-static fw-bold">
                        @if(isset($audit))
                        {{ date('d/m/Y H:i', strtotime($item->created_at)) }}
                        @else
                        {{ date('d/m/Y H:i') }} (Hiện tại)
                        @endif
                    </p>
                </div>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-lg-6">
                <div class="form-group input-borrow_purpose">
                    <label class="form-label" for="note">Ghi Chú kiểm kê</label>
                    <textarea class="form-control" name="note" id="note">{{ old('note', $item->note ?? '') }}</textarea>
                    <span class="input-error text-danger">@error('note') {{ $message }} @enderror</span>
                </div>
                <button type="button" class="btn btn-sm btn-primary repeater-add-btn show-devices px-4 mt-3">Thêm Thiết
                    Bị</button>
            </div>

            <div class="col-lg-3">
                <div class="form-group input-date">
                    <label class="form-label" for="audit_date">Ngày Kiểm Kê</label>
                    <input name="audit_date" type="date" class="form-control"
                        value="{{ old('audit_date', $item->audit_date ?? date('Y-m-d')) }}">
                    <span class="input-error text-danger">@error('audit_date') {{ $message }} @enderror</span>

                    <label class="form-label mt-2" for="school_year">Năm Học</label>
                    <x-form-input-school-years name="school_year" selected_id="{{ $item->school_year }}"
                        id="school_year" />
                    <x-form-input-error field="school_year" />
                </div>
            </div>

            <div class="col-lg-3">
                <div class="form-group">
                    <label class="form-label">Cập nhật lần cuối</label>
                    <p class="form-control-static fw-bold">
                        @if(isset($audit))
                        {{ date('d/m/Y H:i', strtotime($item->updated_at)) }}
                        @else
                        N/A
                        @endif
                    </p>

                    <label class="form-label mt-2" for="status">Trạng thái</label>
                    <select name="status" id="status" class="form-control" required>
                        @php $currentStatus = old('status', $item->status ?? \App\Models\InventoryAudit::DRAFT); @endphp
                        <option value="{{ \App\Models\InventoryAudit::DRAFT }}"
                            {{ $currentStatus == \App\Models\InventoryAudit::DRAFT ? 'selected' : '' }}>Nháp</option>
                        <option value="{{ \App\Models\InventoryAudit::ACTIVE }}"
                            {{ $currentStatus == \App\Models\InventoryAudit::ACTIVE ? 'selected' : '' }}>Đã duyệt
                        </option>
                    </select>
                    <span class="input-error text-danger">@error('status') {{ $message }} @enderror</span>
                </div>
            </div>
        </div>

    </div>
</div>

<div class="alert alert-light" role="alert">
    <h5 class="alert-heading" style="margin-top: 0;">Thông tin quan trọng về Kiểm kê & Kho</h5>
    <hr style="margin: 8px 0;">

    <p style="margin-bottom: 10px; font-size: 1.1em;">
        <strong>⚠️ Lưu ý: Hãy lưu lại liên tục các thay đổi của bạn.</strong>
    </p>

    <p style="margin-bottom: 5px;">
        Khi bạn nhấn nút <strong>"Lưu và cập nhật Tổng Sổ / Hỏng trong kho"</strong>, hệ thống sẽ thực hiện cập nhật số
        lượng tồn kho chính dựa trên kết quả kiểm kê cuối cùng:
    </p>
    <ul style="margin-bottom: 0; padding-left: 20px;">
        <li>Trường <strong>Tổng số</strong> (sau năm học) trong kiểm kê sẽ cập nhật vào cột <strong>Tổng số
                lượng</strong> trong kho.</li>
        <li>Trường <strong>Hỏng</strong> (sau năm học) trong kiểm kê sẽ cập nhật vào cột <strong>Số lượng hỏng</strong>
            trong kho.</li>
    </ul>
    <p style="margin-top: 8px; font-weight: bold; color: #007bff;">
        Thao tác này sẽ đảm bảo số liệu kiểm kê khớp với số liệu tồn kho hiện tại.
    </p>
</div>

<div id="repeater">
    <div class="table-responsive">
        <table class="table table-bordered table-striped table-sm" style="width: 100%;">
            <thead class="thead-dark text-center">
                <tr>
                    <th scope="col" rowspan="3" class="align-middle" style="width: 3%;">S T T</th>
                    <th scope="col" rowspan="3" class="align-middle" style="width: 20%;">TÊN THIẾT BỊ GIÁO DỤC</th>
                    <th scope="col" rowspan="3" class="align-middle" style="width: 5%;">Năm sản xuất</th>
                    <th scope="col" rowspan="3" class="align-middle" style="width: 5%;">Nước sản xuất</th>
                    <th scope="col" rowspan="3" class="align-middle" style="width: 4%;">Đơn vị tính</th>
                    <th scope="col" rowspan="3" class="align-middle" style="width: 8%;">Đơn giá</th>
                    <th scope="col" colspan="2" style="width: 12%;">SỐ LƯỢNG THIẾT BỊ KHI LẬP SỔ</th>
                    <th scope="col" colspan="4" style="width: 36%;">NĂM HỌC 20... - 20...</th>
                    <th scope="col" rowspan="3" class="align-middle" style="width: 4%;"></th>
                </tr>
                <tr>
                    <th scope="col" rowspan="2" class="align-middle" style="width: 7%;">Tổng số</th>
                    <th scope="col" rowspan="2" class="align-middle" style="width: 7%;">Hỏng</th>

                    <th scope="col" rowspan="2" class="align-middle" style="width: 7%;">Tăng</th>
                    <th scope="col" rowspan="2" class="align-middle" style="width: 7%;">Giảm</th>
                    <th scope="col" colspan="2">Số còn lại sau năm học</th>
                </tr>
                <tr>
                    <th scope="col" style="width: 8%;">Tổng số</th>
                    <th scope="col" style="width: 8%;">Hỏng</th>
                </tr>
            </thead>
            <tbody id="devices">
                @php
                // Khởi tạo biến STT (Số thứ tự)
                $stt = 1;
                @endphp

                {{-- Lặp qua các bản ghi chi tiết (InventoryRecords) --}}
                @foreach ($item->records as $record)

                {{-- Dùng $record->id làm index để đảm bảo tính duy nhất và liên kết với bản ghi đã lưu --}}
                <tr class="device_item" data-index="{{ $record->id }}">

                    {{-- Cột 1: STT và device_id ẩn (Handle cho Sortable) --}}
                    <td class="text-center align-middle">
                        {{ $stt++ }}
                        <input name="devices[{{ $record->id }}][device_id]" type="hidden"
                            value="{{ $record->device_id }}">
                    </td>

                    {{-- Cột 2-6: Dữ liệu tĩnh của thiết bị (Lấy qua quan hệ ->device) --}}
                    <td class="align-middle">
                        {{ $record->device->name ?? 'Không tìm thấy tên thiết bị' }}
                    </td>
                    <td class="text-center align-middle">{{ $record->device->year ?? '' }}</td>
                    <td class="text-center align-middle">{{ $record->device->country ?? '' }}</td>
                    <td class="text-center align-middle">{{ $record->device->unit ?? '' }}</td>
                    <td class="text-center align-middle">{{ $record->device->price ? number_format($record->device->price) : 0 }}</td>

                    {{-- Cột 7: Số lượng đầu năm (initial_total) --}}
                    <td class="p-1 align-middle">
                        <input name="devices[{{ $record->id }}][initial_total]" type="number" min="0"
                            class="form-control form-control-sm text-center initial-total qty-input"
                            value="{{ $record->initial_total }}">
                    </td>

                    {{-- Cột 8: Số lượng hỏng đầu năm (initial_broken/damaged) --}}
                    <td class="p-1 align-middle">
                        <input name="devices[{{ $record->id }}][initial_broken]" type="number" min="0"
                            class="form-control form-control-sm text-center initial-broken qty-input"
                            value="{{ $record->initial_damaged }}"> {{-- Lấy từ DB: initial_damaged --}}
                    </td>

                    {{-- Cột 9: Tăng thêm (increase) --}}
                    <td class="p-1 align-middle">
                        <input name="devices[{{ $record->id }}][increase]" type="number" min="0"
                            class="form-control form-control-sm text-center increase-qty qty-input"
                            value="{{ $record->increase_quantity }}"> {{-- Lấy từ DB: increase_quantity --}}
                    </td>

                    {{-- Cột 10: Giảm đi (decrease) --}}
                    <td class="p-1 align-middle">
                        <input name="devices[{{ $record->id }}][decrease]" type="number" min="0"
                            class="form-control form-control-sm text-center decrease-qty qty-input"
                            value="{{ $record->decrease_quantity }}"> {{-- Lấy từ DB: decrease_quantity --}}
                    </td>

                    {{-- Cột 11: Tổng số còn lại cuối năm (final_total) --}}
                    <td class="p-1 align-middle">
                        <input name="devices[{{ $record->id }}][final_total]" type="number" min="0"
                            class="form-control form-control-sm text-center final-total-qty"
                            value="{{ $record->final_total }}" readonly>
                    </td>

                    {{-- Cột 12: Số lượng hỏng cuối năm (final_broken/damaged) --}}
                    <td class="p-1 align-middle">
                        <input name="devices[{{ $record->id }}][final_broken]" type="number" min="0"
                            class="form-control form-control-sm text-center final-broken-qty qty-input"
                            value="{{ $record->final_damaged }}"> {{-- Lấy từ DB: final_damaged --}}
                    </td>

                    {{-- Cột 13: Thao tác --}}
                    <td class="p-1 align-middle text-center">
                        <button type="button" class="btn btn-danger btn-sm delete-device-row">Xóa</button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
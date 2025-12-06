<div class="card">
    <div class="card-body">

        <div class="card-title fw-bold text-uppercase">
            @if(isset($audit))
            THÔNG TIN PHIẾU #{{ $item->id }} - {{ $item->status }}
            @else
            TẠO PHIẾU KIỂM KÊ MỚI
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
                    <x-form-input-school-years name="school_year" selected_id="{{ request()->school_year }}"
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
                <!-- <tr>
                    <td class="p-1 align-middle">1</td>
                    <td class="align-middle">Âm thoa $440 \text{H}_z$</td>
                    <td class="text-center align-middle">2025</td>
                    <td class="text-center align-middle">VN</td>
                    <td class="text-center align-middle">Bộ</td>
                    <td class="text-center align-middle">185000</td>
                    <td class="p-1 align-middle">
                        <input type="number" class="form-control form-control-sm text-center" value="4">
                    </td>
                    <td class="p-1 align-middle">
                        <input type="number" class="form-control form-control-sm text-center" value="0">
                    </td>

                    <td class="p-1 align-middle">
                        <input type="number" class="form-control form-control-sm text-center">
                    </td>
                    <td class="p-1 align-middle">
                        <input type="number" class="form-control form-control-sm text-center">
                    </td>
                    <td class="p-1 align-middle">
                        <input type="number" class="form-control form-control-sm text-center" value="4">
                    </td>
                    <td class="p-1 align-middle">
                        <input type="number" class="form-control form-control-sm text-center" value="0">
                    </td>
                    <td class="p-1 align-middle text-center">
                        <button type="button" class="btn btn-danger btn-sm">Xóa</button>
                    </td>
                </tr> -->
            </tbody>
        </table>
    </div>
</div>
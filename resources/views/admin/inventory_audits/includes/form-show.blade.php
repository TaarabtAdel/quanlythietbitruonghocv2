<div class="card">
    <div class="card-body">

        <div class="card-title fw-bold text-uppercase">
            {{-- Đảm bảo tiêu đề chỉ hiển thị thông tin --}}
            THÔNG TIN PHIẾU #{{ $item->id }}
        </div>
        <div class="my-3 border-top"></div>

        <div class="row">
            <div class="col-lg-6">
                <div class="form-group">
                    <label class="form-label" for="name">Nội dung / Tiêu đề Phiếu</label>
                    {{-- Hiển thị tĩnh Nội dung / Tiêu đề --}}
                    <p class="form-control-static fw-bold">{{ $item->name ?? 'N/A' }}</p>
                </div>
            </div>

            <div class="col-lg-3">
                <div class="form-group">
                    <label class="form-label">Người Tạo</label>
                    <p class="form-control-static fw-bold">
                        {{ @$item->user->name ?? 'N/A' }}
                    </p>
                </div>
            </div>

            <div class="col-lg-3">
                <div class="form-group">
                    <label class="form-label">Ngày Tạo Phiếu</label>
                    <p class="form-control-static fw-bold">
                        {{ date('d/m/Y H:i', strtotime($item->created_at)) }}
                    </p>
                </div>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-lg-6">
                <div class="form-group input-borrow_purpose">
                    <label class="form-label" for="note">Ghi Chú kiểm kê</label>
                    {{-- Hiển thị tĩnh Ghi chú --}}
                    <p class="form-control-static" style="white-space: pre-wrap;">{{ $item->note ?? 'Không có ghi chú.' }}</p>
                </div>
                {{-- Loại bỏ nút "Thêm Thiết Bị" --}}
            </div>

            <div class="col-lg-3">
                <div class="form-group input-date">
                    <label class="form-label" for="audit_date">Ngày Kiểm Kê</label>
                    {{-- Hiển thị tĩnh Ngày kiểm kê --}}
                    <p class="form-control-static fw-bold">{{ date('d/m/Y', strtotime($item->audit_date)) }}</p>

                    <label class="form-label mt-2" for="school_year">Năm Học</label>
                    {{-- Hiển thị tĩnh Năm học (Giả định x-form-input-school-years có hàm hiển thị tên) --}}
                    <p class="form-control-static fw-bold">{{ $item->school_year ?? 'N/A' }}</p>
                </div>
            </div>

            <div class="col-lg-3">
                <div class="form-group">
                    <label class="form-label">Cập nhật lần cuối</label>
                    <p class="form-control-static fw-bold">
                        {{ date('d/m/Y H:i', strtotime($item->updated_at)) }}
                    </p>

                    <label class="form-label mt-2" for="status">Trạng thái</label>
                    {{-- Hiển thị tĩnh Trạng thái --}}
                    <p class="form-control-static fw-bold">
                        @if($item->status == \App\Models\InventoryAudit::DRAFT)
                            <span class="badge bg-secondary">Nháp</span>
                        @elseif($item->status == \App\Models\InventoryAudit::ACTIVE)
                            <span class="badge bg-success">Đã duyệt</span>
                        @else
                            {{ $item->status }}
                        @endif
                    </p>
                </div>
            </div>
        </div>

    </div>
</div>

{{-- Loại bỏ Ghi chú Cập nhật Kho vì không còn chức năng cập nhật --}}
{{-- <div class="alert alert-light" role="alert">...</div> --}}

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
                    <th scope="col" colspan="4" style="width: 36%;">BIẾN ĐỘNG TRONG NĂM</th>
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
                $stt = 1;
                @endphp

                @foreach ($item->records as $record)
                <tr class="device_item" data-index="{{ $record->id }}">

                    {{-- Cột 1: STT --}}
                    <td class="text-center align-middle">{{ $stt++ }}</td>

                    {{-- Cột 2-6: Dữ liệu tĩnh của thiết bị --}}
                    <td class="align-middle">{{ $record->device->name ?? 'N/A' }}</td>
                    <td class="text-center align-middle">{{ $record->device->year ?? '' }}</td>
                    <td class="text-center align-middle">{{ $record->device->country ?? '' }}</td>
                    <td class="text-center align-middle">{{ $record->device->unit ?? '' }}</td>
                    <td class="text-center align-middle">{{ $record->device->price ? number_format($record->device->price) : 0 }}</td>

                    {{-- Cột 7: Số lượng đầu năm (initial_total) --}}
                    <td class="text-center align-middle fw-bold">{{ $record->initial_total }}</td>

                    {{-- Cột 8: Số lượng hỏng đầu năm (initial_damaged) --}}
                    <td class="text-center align-middle fw-bold text-danger">{{ $record->initial_damaged }}</td>

                    {{-- Cột 9: Tăng thêm (increase) --}}
                    <td class="text-center align-middle fw-bold text-success">{{ $record->increase_quantity }}</td>

                    {{-- Cột 10: Giảm đi (decrease) --}}
                    <td class="text-center align-middle fw-bold text-danger">{{ $record->decrease_quantity }}</td>

                    {{-- Cột 11: Tổng số còn lại cuối năm (final_total) --}}
                    <td class="text-center align-middle fw-bold text-primary">{{ $record->final_total }}</td>

                    {{-- Cột 12: Số lượng hỏng cuối năm (final_damaged) --}}
                    <td class="text-center align-middle fw-bold text-danger">{{ $record->final_damaged }}</td>

                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
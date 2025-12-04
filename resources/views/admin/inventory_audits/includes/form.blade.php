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
            </div>
            
            <div class="col-lg-3">
                <div class="form-group input-date">
                    <label class="form-label" for="audit_date">Ngày Kiểm Kê</label>
                    <input name="audit_date" type="date" class="form-control"
                        value="{{ old('audit_date', $item->audit_date ?? date('Y-m-d')) }}">
                    <span class="input-error text-danger">@error('audit_date') {{ $message }} @enderror</span>

                    <label class="form-label mt-2" for="school_year">Năm Học</label>
                    <x-form-input-school-years name="school_year" selected_id="{{ request()->school_year }}" id="school_year"/>
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
                        <option value="{{ \App\Models\InventoryAudit::DRAFT }}" {{ $currentStatus == \App\Models\InventoryAudit::DRAFT ? 'selected' : '' }}>Nháp</option>
                        <option value="{{ \App\Models\InventoryAudit::ACTIVE }}" {{ $currentStatus == \App\Models\InventoryAudit::ACTIVE ? 'selected' : '' }}>Đã duyệt</option>
                    </select>
                    <span class="input-error text-danger">@error('status') {{ $message }} @enderror</span>
                </div>
            </div>
        </div>
        
    </div>
</div>

<div id="repeater">
    
    @if(isset($audit) && $item->records->count() > 0)
        @foreach( $item->records as $index => $record )
            @include('admin.inventory_audits.includes.borrow-item', [
                'index' => $index, // Dùng index thay cho tiet
                'record' => $record,
            ])
        @endforeach
    @else
        @include('admin.inventory_audits.includes.borrow-item', [
            'index' => 0,
            'record' => null,
        ])
    @endif

    <div class="card mt-3">
        <div class="card-body">
            <div class="d-flex flex-column flex-lg-row align-items-center justify-content-between">
                <p class="mb-0 col-lg">Thêm các loại thiết bị cần kiểm kê chi tiết.
                </p>
                <button type="button"
                    class="btn btn-sm btn-primary repeater-add-btn add-tiet px-4 col-12 col-lg-auto mt-2 mt-lg-0">Thêm
                    Loại Thiết Bị</button>
            </div>
        </div>
    </div>
</div>
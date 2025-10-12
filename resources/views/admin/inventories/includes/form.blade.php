<div class="card">
    <div class="card-body">
        <div class="card-title fw-bold text-uppercase">Thông Tin Phiếu #{{ $item->id ?? '' }} - {!! $item->status_fm !!}</div>
        <div class="my-3 border-top"></div>
        <div class="row">
            <div class="col-lg-6">
                <div class="form-group">
                    <label class="form-label" for="name">Nội dung</label>
                    <input name="name" type="text" class="form-control"  value="{{ $item->name }}">
                    <span class="input-error text-danger"></span>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="form-group">
                    <label class="form-label" for="user_id">Người Tạo</label>
                    <p class="form-control-static fw-bold">{{ @$item->user->name ?? '' }}</p>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="form-group">
                    <label class="form-label" for="created_at">Ngày Tạo Phiếu</label>
                    <p class="form-control-static fw-bold">{{ date('d/m/Y H:i',strtotime($item->created_at)) }}</p>
                </div>
            </div>
            
        </div>
        @if ( request()->routeIs('admin.inventories.edit') )
        <div class="row mt-1">
            <div class="col-lg-6">
                <div class="form-group input-borrow_purpose">
                    <label class="form-label" for="note">Ghi Chú kiểm kê</label>
                    <textarea class="form-control" name="note" id="note">{{ $item->note  }}</textarea>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="form-group input-date">
                    <label class="form-label" for="date">Ngày Kiểm</label>
                    <input name="date" min="{{ date('Y-m-d') }}" type="date" class="form-control"
                        placeholder="Nhập ngày dạy" value="{{ $item->date }}">
                    <span class="input-error text-danger"></span>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="form-group">
                    <label class="form-label" for="created_at">Cập nhật lần cuối</label>
                    <p class="form-control-static fw-bold">{{ date('d/m/Y H:i',strtotime($item->updated_at)) }}</p>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
<div id="repeater">
    @if( count( $item->items ) )
        @foreach( $item->items as $tiet => $items )
            @include('admin.inventories.includes.borrow-item',[
            'tiet' => $tiet,
            'items' => $items,
            'borrow' => $items[0],
            ])
        @endforeach
    @else
        @include('admin.inventories.includes.borrow-item',[
            'tiet' => 0 ,
            'items' => null,
            'borrow' => null,
        ])
    @endif

    <div class="card">
        <div class="card-body">
            <div class="d-flex flex-column flex-lg-row align-items-center justify-content-between">
                <p class="mb-0 col-lg">Một phiếu có thể có nhiều bảng.
                </p>
                <button type="button"
                    class="btn btn-sm btn-primary repeater-add-btn add-tiet px-4 col-12 col-lg-auto mt-2 mt-lg-0">Thêm
                    Tiết Dạy</button>
            </div>
        </div>

    </div>

</div>
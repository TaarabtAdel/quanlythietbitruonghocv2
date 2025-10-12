<div class="card">
    <div class="card-body">
        <div class="card-title fw-bold text-uppercase">Thông Tin Phiếu Mượn #{{ $item->id ?? '' }} - {!! $item->status_fm !!}</div>
        <div class="my-3 border-top"></div>
        <div class="row">
            <div class="col-lg-3">
                <div class="form-group">
                    <label class="form-label" for="user_id">Người Mượn</label>
                    <p class="form-control-static fw-bold">{{ @$item->user->name ?? '' }}</p>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="form-group">
                    <label class="form-label" for="created_at">Ngày Tạo Phiếu</label>
                    <p class="form-control-static fw-bold">{{ date('d/m/Y H:i',strtotime($item->created_at)) }}</p>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="form-group input-borrow_date">
                    <label class="form-label" for="borrow_date">Ngày Dạy</label>
                    <input name="borrow_date" min-bk="{{ date('Y-m-d') }}" type="date" class="form-control"
                        placeholder="Nhập ngày dạy" value="{{ $item->borrow_date }}">
                    <span class="input-error text-danger"></span>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="form-group input-borrow_purpose">
                    <label class="form-label" for="borrow_purpose">Mục Đích</label>
                    <select name="borrow_purpose" class="form-control">
                        @foreach( App\Models\Borrow::get_borrow_purposes() as $kp => $purpose )
                        <option @selected($item->borrow_purpose == $kp) value="{{ $kp }}">{{ $purpose }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        @if ( request()->routeIs('admin.borrows.edit') )
        <div class="row mt-1">
            <div class="col-lg-6">
                <div class="form-group input-borrow_purpose">
                    <label class="form-label" for="borrow_note">Ghi Chú (Tình trạng thiết bị...)</label>
                    <textarea class="form-control" name="borrow_note" id="borrow_note">{{ $item->borrow_note  }}</textarea>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="form-group input-is_returned">
                    <label class="form-label" for="is_returned">Đã Trả</label>
                    <select name="is_returned" class="form-control">
                        @foreach( App\Models\Borrow::RETURN_STATUS as $kp => $return )
                        <option @selected($item->is_returned == $kp) value="{{ $kp }}">{{ $return }}</option>
                        @endforeach
                    </select>
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
    @if( count( $item->borrow_items ) )
        @foreach( $item->borrow_items as $tiet => $borrow_items )
            @include('teacher.borrows.includes.borrow-item',[
            'tiet' => $tiet,
            'borrow_items' => $borrow_items,
            'borrow' => $borrow_items[0],
            ])
        @endforeach
    @else
        @include('teacher.borrows.includes.borrow-item',[
            'tiet' => 0 ,
            'borrow_items' => null,
            'borrow' => null,
        ])
    @endif

    <div class="card">
        <div class="card-body">
            <div class="d-flex flex-column flex-lg-row align-items-center justify-content-between">
                <p class="mb-0 col-lg">Giáo viên có thể thêm nhiều tiết dạy, mỗi tiết dạy không yêu cầu thêm thiết bị.
                </p>
                <button type="button"
                    class="btn btn-sm btn-primary repeater-add-btn add-tiet px-4 col-12 col-lg-auto mt-2 mt-lg-0">Thêm
                    Tiết Dạy</button>
            </div>
        </div>

    </div>

</div>
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
                    <p class="form-control-static fw-bold">{{ date('d/m/Y',strtotime($item->borrow_date)) }}</p>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="form-group input-borrow_date">
                    <label class="form-label" for="borrow_date">Mục Đích</label>
                    <p class="form-control-static fw-bold">{{ App\Models\Borrow::get_borrow_purposes()[$item->borrow_purpose] ?? '' }}</p>
                </div>
            </div>
        </div>

    </div>
</div>
<div id="repeater">
    @if( count( $item->borrow_items ) )
        @foreach( $item->borrow_items as $tiet =>  $borrow_items )
            @include('teacher.borrows.includes.borrow-item-show',[ 
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

</div>
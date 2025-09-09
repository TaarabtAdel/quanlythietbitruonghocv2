<?php
    $borrow = $borrow ? $borrow->toArray() : [
        'lesson_name' => '',
        'session' => '',
        'lecture_name' => '',
        'room_id' => '',
        'lecture_number' => '',
    ];
    $borrow_items = $borrow_items ? $borrow_items : [];
?>
<div class="items" data-group="devices" data-tiet="{{ $tiet }}">
    <input type="hidden" data-name="tiet" name="devices[{{ $tiet }}][tiet]" name="devices[{{ $tiet }}][tiet]" value="{{ $tiet }}">
    <div class="card">
        <div class="card-body">
            <div class="item-content">
                <div class="row mb-4">
                    <div class="col col-lg-3 col-6 input-devices-{{ $tiet }}-lesson_name">
                        <label class="form-label">Tên bài dạy</label>
                        <p class="form-control-static fw-bold">{{ $borrow['lesson_name'] }}</p>
                    </div>
                    <div class="col col-lg-1 col-6">
                        <label class="form-label">Buổi</label>
                        <p class="form-control-static fw-bold">{{ $borrow['session'] }}</p>
                    </div>
                    <div class="col col-lg-2 col-6 input-devices-{{ $tiet }}-lecture_name">
                        <label class="form-label">Tiết PPCT</label>
                        <p class="form-control-static fw-bold">{{ $borrow['lecture_name'] }}</p>
                    </div>
                    <div class="col col-lg-2 col-6">
                        <label class="form-label">Lớp</label>
                        @foreach($rooms as $room)
                            @if( $borrow['room_id'] == $room->id )
                            <p class="form-control-static fw-bold">{{ $room->name }}</p>
                            @break
                            @endif
                        @endforeach
                    </div>
                    <div class="col col-lg-1 col-6">
                        <label class="form-label">Tiết TKB</label>
                        <p class="form-control-static fw-bold">{{ $borrow['lecture_number'] }}</p>
                    </div>
                    <div class="col col-lg-3 col-6 lab-choiced">
                        <label class="form-label">Phòng bộ môn</label>
                        <p class="form-control-static fw-bold">
                            {{ $borrow_items[0]->lab->name ?? '' }}
                        </p>
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-12">
                        <label class="fw-bold" for="">DANH SÁCH THIẾT BỊ TRONG TIẾT NÀY</label>
                    </div>
                    <div class="col-12 mt-2">
                        <div class="table-responsive white-space-nowrap">
                            <table class="table align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>STT</th>
                                        <th width="300px">Tên thiết bị</th>
                                        <th>Số lượng</th>
                                        <th>Loại thiết bị</th>
                                        <th>Bộ môn</th>
                                    </tr>
                                </thead>
                                <tbody class="tiet_devices">
                                    @foreach($borrow_items as $key => $borrow_item)
                                    @if( !$borrow_item->device_id )
                                        @continue
                                    @endif
                                    <tr class="device_item">
                                        <td>{{ $key + 1 }}<input data-name="device_id" name="devices[{{ $tiet }}][device_id]" type="hidden" value="{{ $borrow_item->device_id }}"></td>
                                        <td>{{ $borrow_item->device->name }}</td>
                                        <td width="100px">{{ $borrow_item->quantity }}</td>
                                        <td>{{ @$borrow_item->device->devicetype->name }}</td>
                                        <td>{{ @$borrow_item->device->department->name }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
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
                <div class="input-tiet_{{ $tiet }}_validate">
                    <span class="input-error text-danger"></span>
                </div>
                <div class="input-tiet_{{ $tiet }}_duplicate">
                    <span class="input-error text-danger"></span>
                </div>
                <div class="row mb-4">
                    <div class="col col-lg-1 col-12">
                        <label class="form-label">Buổi</label>
                        <select data-name="session" name="devices[{{ $tiet }}][session]" id="devices_{{ $tiet }}_session"
                            class="form-control">
                            <option @selected($borrow['session'] == 'Sáng') value="Sáng">Sáng</option>
                            <option @selected($borrow['session'] == 'Chiều') value="Chiều">Chiều</option>
                        </select>
                    </div>
                    <div class="col col-lg-1 col-12">
                        <label class="form-label">Tiết TKB</label>
                        <select data-name="lecture_number" data-name="devices[{{ $tiet }}][lecture_number]" name="devices[{{ $tiet }}][lecture_number]"
                            id="devices_{{ $tiet }}_lecture_number" class="form-control">
                            <option @selected($borrow['lecture_number'] == 1) value="1">1</option>
                            <option @selected($borrow['lecture_number'] == 2) value="2">2</option>
                            <option @selected($borrow['lecture_number'] == 3) value="3">3</option>
                            <option @selected($borrow['lecture_number'] == 4) value="4">4</option>
                            <option @selected($borrow['lecture_number'] == 5) value="5">5</option>
                        </select>
                    </div>
                    <div class="col col-lg-2 col-12">
                        <label class="form-label">Lớp</label>
                        <select data-name="room_id"  name="devices[{{ $tiet }}][room_id]" id="devices_{{ $tiet }}_room_id"
                            class="form-control select2">
                            @foreach($rooms as $room)
                            <option @selected($borrow['room_id'] == $room->id) value="{{ $room->id }}">{{ $room->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col col-lg-2 col-12 input-devices-{{ $tiet }}-lecture_name">
                        <label class="form-label">Tiết PPCT</label>
                        <x-form-input-lecture_name tiet="{{ $tiet }}" name="lecture_name" value="{{ $borrow['lecture_name'] }}" target="devices_{{ $tiet }}_lesson_name" />
                        <span class="input-error text-danger"></span>
                    </div>
                    <div class="col col-lg-3 col-12 input-devices-{{ $tiet }}-lesson_name">
                        <label class="form-label">Tên bài dạy</label>
                        <input type="text" class="form-control curriculum-lesson_name" id="devices_{{ $tiet }}_lesson_name"
                            data-name="lesson_name" name="devices[{{ $tiet }}][lesson_name]" value="{{ $borrow['lesson_name'] }}">
                        <span class="input-error text-danger"></span>
                    </div>
                    
                    <div class="col col-lg-3 col-12 lab-choiced">
                        <div>
                            <label class="form-label">Phòng bộ môn</label>
                            <span title="Xóa phòng bộ môn" class="float-end ml-1 delete-lab x{{ $borrow_items[0]->lab_id ?? 'd-none' }}" data-tiet-id="{{ $tiet }}">Xóa</span>
                        </div>
                        <div class="">
                            <input class="lab_id" data-name="lab_id" name="devices[{{ $tiet }}][lab_id]" id="devices_{{ $tiet }}_lab_id" type="hidden" value="{{ $borrow_items[0]->lab_id ?? 0 }}">
                            <button title="Nhấn để chọn lại Phòng Bộ Môn" type="button" class="btn btn-sm btn-info mt-1 show-labs" data-tiet-id="{{ $tiet }}">
                                {{ $borrow_items[0]->lab->name ?? 'Chọn' }}
                            </button>
                        </div>
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-12">
                        <label class="fw-bold" for="">DANH SÁCH THIẾT BỊ TRONG TIẾT NÀY</label>
                        <div class="mt-2">
                            <button type="button" class="btn btn-sm btn-primary show-devices" data-tiet-id="{{ $tiet }}">Thêm Thiết Bị Từ Kho Thiết Bị</button>
                        </div>
                    </div>
                    <div class="col-12 mt-2">
                        <div class="table-responsive white-space-nowrap">
                            <label class="fw-bold" for="">Thiết bị trong kho</label>
                            <table class="table align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th width="50px">STT</th>
                                        <th width="300px">Tên thiết bị</th>
                                        <th>Số lượng</th>
                                        <th>Loại thiết bị</th>
                                        <th>Bộ môn</th>
                                        <th>Hành động</th>
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
                                        <td width="100px">
                                            <input data-device-id="{{ $borrow_item->device_id }}" data-tiet-id="{{ $tiet }}" name="devices[{{ $tiet }}][quantity]" type="number" min="1" value="{{ $borrow_item->quantity }}" class="form-control change-qty-device">
                                            <input name="quantity_devices[{{ $tiet }}][{{ $borrow_item->device_id }}][quantity]" type="hidden" value="{{ $borrow_item->quantity }}">
                                        </td>
                                        <td>{{ @$borrow_item->device->devicetype->name }}</td>
                                        <td>{{ @$borrow_item->device->department->name }}</td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-danger delete-device" data-device-id="{{ $borrow_item->device_id }}" data-tiet-id="{{ $tiet }}">Xóa</button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            @if( \App\Models\Option::get_option_name('enable_fake_device') )
                            <div class="mt-3 mb-2">
                                <button type="button" class="btn btn-sm btn-primary show-device-fakes" data-tiet-id="{{ $tiet }}">Thêm Thiết Bị Tự Chuẩn Bị</button>
                            </div>
                            <label class="fw-bold" for="">Thiết bị tự chuẩn bị</label>
                            <table class="table align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th width="50px">STT</th>
                                        <th width="69%">Tên thiết bị</th>
                                        <th>Số lượng</th>
                                        <th>Hành động</th>
                                    </tr>
                                </thead>
                                <tbody class="tiet_fake_devices">
                                    @if( $borrow_fake_items && count( $borrow_fake_items ) )
                                        @foreach($borrow_fake_items as $key => $borrow_fake_item)
                                        <tr class="device_item">
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $borrow_fake_item->device_name }}</td>
                                            <td width="100px">
                                                <input class="form-control change-qty-fake-device" name="quantity_fake_devices[{{ $borrow_fake_item->id }}][quantity]" type="number" value="{{ $borrow_fake_item->quantity }}">
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-danger delete-fake_device" data-id="{{ $borrow_fake_item->id }}" data-tiet-id="{{ $tiet }}">Xóa</button>
                                            </td>
                                        </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <!-- Repeater Remove Btn -->
            <div class="repeater-remove-btn">
                <button type="button" class="btn btn-danger btn-sm remove-btn px-4 delete-tiet" data-tiet-id="{{ $tiet }}">
                    Xóa tiết dạy này
                </button>
            </div>
        </div>
    </div>
</div>
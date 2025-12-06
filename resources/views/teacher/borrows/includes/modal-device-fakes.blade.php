<div class="modal fade" id="modal-device-fakes" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <form id="form-device-fakes" action="{{ route('borrows.saveFakeDevices',$item->id) }}">
                @csrf
                <div class="modal-header">
                    <div class="modal-title fw-bold">DANH SÁCH THIẾT BỊ TỰ CHUẨN BỊ</div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body device-fake-table-results">
                    <table class="table table-bordered align-middle text-center">
                        <thead class="table-light">
                            <tr>
                                <th style="width:60px">STT</th>
                                <th>Tên thiết bị</th>
                                <th style="width:120px">Số lượng</th>
                                <th style="width:160px">Hành động</th>
                            </tr>
                        </thead>
                        <tbody id="device-fake-list">
                            <tr>
                                <td class="stt">1</td>
                                <td>
                                    <input type="text" name="device_fakes[0][device_name]" class="form-control"
                                        placeholder="Nhập tên thiết bị" required>
                                </td>
                                <td>
                                    <input type="number" name="device_fakes[0][qty]" class="form-control" min="1"
                                        value="1" required>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-success btn-add-row">Thêm</button>
                                    <button type="button" class="btn btn-sm btn-danger btn-remove-row">Xóa</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary btn-sm">Lưu danh sách</button>
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Đóng</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function getFakeDevices(type = 'card', id, tiet_id = 0) {
    let card_tiet = jQuery('.items[data-tiet="' + ( parseInt(tiet_id)  ) + '"]');
    console.log('card_tiet',card_tiet);
    console.log('tiet_id',tiet_id);
    
    $.ajax({
        url: "{{ route('borrows.getFakeDevices') }}",
        type: "GET",
        data: { id: id, tiet_id: tiet_id },
        dataType: "json",
        success: function(res) {
            if (res.success) {
                if (type === 'form') {
                    let html = '';
                    if (res.data.length > 0) {
                        $.each(res.data, function(index, item) {
                            html += `
                                <tr>
                                    <td class="stt">${index + 1}</td>
                                    <td>
                                        <input type="text" name="device_fakes[${index}][device_name]" 
                                            class="form-control" 
                                            placeholder="Nhập tên thiết bị" 
                                            value="${item.device_name ?? ''}" required>
                                    </td>
                                    <td>
                                        <input type="number" name="device_fakes[${index}][qty]" 
                                            class="form-control" 
                                            min="1" value="${item.quantity ?? 1}" required>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-success btn-add-row">Thêm</button>
                                        <button type="button" class="btn btn-sm btn-danger btn-remove-row">Xóa</button>
                                    </td>
                                </tr>
                            `;
                        });
                    } else {
                        html = `
                            <tr>
                                <td class="stt">1</td>
                                <td>
                                    <input type="text" name="device_fakes[0][device_name]" 
                                        class="form-control" 
                                        placeholder="Nhập tên thiết bị" required>
                                </td>
                                <td>
                                    <input type="number" name="device_fakes[0][qty]" 
                                        class="form-control" 
                                        min="1" value="1" required>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-success btn-add-row">Thêm</button>
                                    <button type="button" class="btn btn-sm btn-danger btn-remove-row">Xóa</button>
                                </td>
                            </tr>
                        `;
                    }
                    $('#device-fake-list').html(html);
                }
                if (type === 'card') {
                    let html = '';
                    if (res.data.length > 0) {
                        $.each(res.data, function(index, item) {
                            html += `
                            <tr class="device_item">
                                <td>${index + 1}</td>
                                <td>${item.device_name}</td>
                                <td width="100px">
                                    <input class="form-control change-qty-fake-device" name="quantity_fake_devices[${item.id}][quantity]" type="number" value="${item.quantity}">
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-danger delete-fake_device" data-id="${item.id}" data-tiet-id="${tiet_id}">Xóa</button>
                                </td>
                            </tr>
                            `;
                        }); 
                    }
                    card_tiet.find('.tiet_fake_devices').html(html);

                }
            }
        }
    });
}

jQuery(document).ready(function($) {
    var borrow_id = '{{ $item->id }}';
    // 👉 Thêm dòng mới
    $(document).on('click', '.btn-add-row', function() {
        const $tableBody = $('#device-fake-list');
        const $lastRow = $tableBody.find('tr:last');
        const $newRow = $lastRow.clone();

        // Reset giá trị input
        $newRow.find('input[type="text"]').val('');
        $newRow.find('input[type="number"]').val(1);

        // Gắn vào bảng
        $tableBody.append($newRow);
        updateRowIndex();
    });

    // 👉 Xóa dòng
    $(document).on('click', '.btn-remove-row', function() {
        const $rows = $('#device-fake-list tr');
        if ($rows.length > 1) {
            $(this).closest('tr').remove();
            updateRowIndex();
        } else {
            alert('Phải có ít nhất một thiết bị!');
        }
    });

    // 👉 Cập nhật lại STT và name[]
    function updateRowIndex() {
        $('#device-fake-list tr').each(function(index) {
            $(this).find('.stt').text(index + 1);
            $(this).find('input[name^="device_fakes"]').each(function() {
                const name = $(this).attr('name');
                const newName = name.replace(/device_fakes\[\d+\]/, `device_fakes[${index}]`);
                $(this).attr('name', newName);
            });
        });
    }

    // =================== Xử lý fake devices
    jQuery('body').on('click', ".show-device-fakes", function(e) {
        tiet_id = jQuery(this).data('tiet-id');
        jQuery('#tiet').val(tiet_id);
        getFakeDevices('form',borrow_id,tiet_id);
        jQuery('#modal-device-fakes').modal('show');
    });
    
    // Gửi form
    $('#form-device-fakes').on('submit', function(e) {
        e.preventDefault();
        const device_fakes = [];
        $('#device-fake-list tr').each(function() {
            const device_name = $(this).find('input[name*="[device_name]"]').val();
            const qty = $(this).find('input[name*="[qty]"]').val();
            if (device_name.trim() !== '') {
                device_fakes.push({
                    device_name,
                    qty
                });
            }
        });
        // ✅ Ví dụ gửi AJAX
        $.ajax({
            url: $('#form-device-fakes').attr('action'),
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            dataType:'json',
            data: { 
                _token : $('#form-device-fakes').find('[name="_token"]').val(),
                tiet_id : jQuery('#tiet').val(),
                device_fakes : device_fakes 
            },
            success: function(res) {
                if( res.success ){
                    getFakeDevices('card',borrow_id, jQuery('#tiet').val() );
                    $('#modal-device-fakes').modal('hide');
                }
            }
        });
        
    });

    // Xóa fake device
    jQuery('body').on('click', ".delete-fake_device", function(e) {
        const fake_device_id = $(this).data('id');
        const tiet_id = $(this).data('tiet-id');
        $.ajax({
            url: '{{ route("borrows.delete_fake_device") }}',
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            dataType:'json',
            data: { 
                _token : '{{ csrf_token() }}',
                fake_device_id : fake_device_id,
                tiet_id : tiet_id
            },
            success: function(res) {
                if( res.success ){
                    getFakeDevices('card',borrow_id, tiet_id );
                }
            }
        });
    });
    // Cập nhật số lượng fake device
    jQuery('body').on('change', ".change-qty-fake-device", function(e) {
        const fake_device_id = $(this).closest('tr').find('.delete-fake_device').data('id');
        const tiet_id = $(this).closest('tr').find('.delete-fake_device').data('tiet-id');
        const new_quantity = $(this).val();
        $.ajax({
            url: '{{ route("borrows.update_qty_fake_device") }}',
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            dataType:'json',
            data: { 
                _token : '{{ csrf_token() }}',
                fake_device_id : fake_device_id,
                tiet_id : tiet_id,
                quantity : new_quantity
            },
            success: function(res) {
                if( res.success ){
                    getFakeDevices('card',borrow_id, tiet_id );
                }
            }
        });
    });
    

});
</script>
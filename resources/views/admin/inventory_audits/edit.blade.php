@extends('admin.layouts.master')
@section('title','Cập nhật phiếu kiểm kê #'. $item->id)
@section('content')
@include('globals.breadcrumb',[
'page_title' => 'Cập nhật phiếu kiểm kê #'.$item->id,
])
<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<style>
/* Tùy chỉnh màu vàng nhạt cho hàng đã chỉnh sửa */
.table-warning-light {
    background-color: #fff3cd !important;
    /* Màu vàng nhạt của Bootstrap */
    transition: background-color 0.3s ease;
}
</style>
<form id="inventory_audits-form" action="{{ isset($item) && $item->id 
        ? route($route_prefix.'.update', $item->id) 
        : route($route_prefix.'.store') }}" method="post" enctype="multipart/form-data">

    @csrf
    @if(isset($item) && $item->id)
    @method('PUT')
    @include('admin.inventory_audits.includes.form')
    @endif

</form>
<div class="card">
    <div class="card-body">
        <div class="d-flex align-items-center justify-content-end gap-2 flex-column flex-lg-row">
            <a href="{{ route($route_prefix.'.index') }}" class="btn btn-sm btn-secondary px-4 col-12 col-lg-auto">
                <i class='bx bx-arrow-back'></i> Quay lại
            </a>
            <button type="button" name="submit_request" class="btn btn-sm btn-primary px-4 ml-2 col-12 col-lg-auto submit_request">Lưu lại</button>
            <button type="button"  name="submit_request_update" class="btn btn-sm btn-success px-4 ml-2 col-12 col-lg-auto submit_request">Lưu và cập nhật Tổng Sổ / Hỏng trong kho</button>
        </div>
    </div>
</div>
@include('teacher.borrows.includes.modal-devices')
<!--end row-->
@endsection
@push('footer')
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
<script>
jQuery(document).ready(function($) {

    // --- Biến và Khởi tạo ---
    let deviceCount = $('#devices tr').length;
    // Bắt đầu unique index cao hơn số hàng hiện có để tránh xung đột với dữ liệu cũ
    let uniqueIdCounter = deviceCount > 0 ? deviceCount + 1 : 1;
    const EDITED_BG_CLASS = 'table-warning-light';

    // --- 1. Hàm tạo HTML cho một hàng thiết bị mới ---
    function createNewDeviceRow(stt, data) {
        let index = uniqueIdCounter++;
        let device_id = data.device_id || '';
        let device_name = data.device_name || 'Thiết bị mới';
        let year = data.year || '';
        let country = data.country || '';
        let unit = data.unit || '';
        let price = data.price || '';
        let initial_total = data.initial_total || 1;
        let initial_broken = data.initial_broken || 0;

        let html = `
            <tr class="device_item" data-index="${index}">
                <td class="text-center align-middle">
                    ${stt}
                    <input name="devices[${index}][device_id]" type="hidden" value="${device_id}">
                </td>
                <td class="align-middle">${device_name}</td>
                <td class="text-center align-middle">${year}</td>
                <td class="text-center align-middle">${country}</td>
                <td class="text-center align-middle">${unit}</td>
                <td class="text-center align-middle">${price}</td>

                <td class="p-1 align-middle">
                    <input name="devices[${index}][initial_total]" type="number" min="0" class="form-control form-control-sm text-center initial-total qty-input" value="${initial_total}">
                </td>
                <td class="p-1 align-middle">
                    <input name="devices[${index}][initial_broken]" type="number" min="0" class="form-control form-control-sm text-center initial-broken qty-input" value="${initial_broken}">
                </td>

                <td class="p-1 align-middle">
                    <input name="devices[${index}][increase]" type="number" min="0" class="form-control form-control-sm text-center increase-qty qty-input">
                </td>
                <td class="p-1 align-middle">
                    <input name="devices[${index}][decrease]" type="number" min="0" class="form-control form-control-sm text-center decrease-qty qty-input">
                </td>
                
                <td class="p-1 align-middle">
                    <input name="devices[${index}][final_total]" type="number" min="0" class="form-control form-control-sm text-center final-total-qty" value="${initial_total}" readonly>
                </td>
                <td class="p-1 align-middle">
                    <input name="devices[${index}][final_broken]" type="number" min="0" class="form-control form-control-sm text-center final-broken-qty qty-input" value="${initial_broken}">
                </td>

                <td class="p-1 align-middle text-center">
                    <button type="button" class="btn btn-danger btn-sm delete-device-row">Xóa</button>
                </td>
            </tr>
        `;
        return html;
    }

    // --- 2. Cập nhật STT ---
    function updateSTT() {
        let currentStt = 1;
        $('#devices tr').each(function() {
            // Cập nhật text hiển thị STT (đảm bảo không xóa input hidden)
            $(this).find('td:first').contents().filter(function() {
                // Chỉ chọn Node text (type 3)
                return this.nodeType === 3;
            }).first().replaceWith(currentStt++);
        });
        deviceCount = currentStt - 1;
    }

    // --- 3. Hàm tính toán Tổng số cuối cùng ---
    function calculateFinalTotal(row) {
        let initialTotal = parseInt(row.find('.initial-total').val()) || 0;
        let increase = parseInt(row.find('.increase-qty').val()) || 0;
        let decrease = parseInt(row.find('.decrease-qty').val()) || 0;

        // Công thức: Tổng số cuối = Tổng số ban đầu + Tăng - Giảm
        let finalTotal = initialTotal + increase - decrease;
        if (finalTotal < 0) {
            finalTotal = 0;
        }

        row.find('.final-total-qty').val(finalTotal);
    }

    // --- 4. Logic LƯU DỮ LIỆU ---
    jQuery('body').on('click', ".submit_request", function(e) {
        e.preventDefault();
        let task = jQuery(this).attr('name');

        if( task == 'submit_request_update' ){
            let confirmationMessage = "Bạn có chắc chắn muốn LƯU và cập nhật Tổng Sổ / Hỏng trong kho không? Thao tác này sẽ thay đổi số lượng thiết bị chính thức.";
            if (!confirm(confirmationMessage)) {
                return;
            }
        }

        let devicesData = [];

        // Lặp qua từng hàng thiết bị
        $('#devices tr.device_item').each(function(i, row) {
            let $row = $(row);
            let rowData = {};

            rowData.order_index = i + 1; // Thứ tự hiện tại trên bảng

            // Lấy dữ liệu tĩnh (tên, năm, nước, đơn vị, giá)
            let tds = $row.find('td');
            rowData.device_name = $(tds[1]).text().trim();
            rowData.year = $(tds[2]).text().trim();
            rowData.country = $(tds[3]).text().trim();
            rowData.unit = $(tds[4]).text().trim();
            rowData.price = $(tds[5]).text().trim();

            // Lấy dữ liệu từ các trường input (bao gồm device_id)
            $row.find('input[name^="devices"]').each(function() {
                let inputName = $(this).attr('name');
                let fieldMatch = inputName.match(/\[(\w+)\]$/);
                if (fieldMatch) {
                    let field = fieldMatch[1];
                    rowData[field] = $(this).val();
                }
            });
            devicesData.push(rowData);
        });

        console.log("✅ Dữ liệu sẵn sàng gửi đi (Có device_id và thứ tự):");
        console.log(devicesData);

        // TODO: THỰC HIỆN AJAX SUBMISSION TẠI ĐÂY
        var actionUrl = jQuery('#inventory_audits-form').attr('action');

        let formDataArray = jQuery('#inventory_audits-form').serializeArray();
        let combinedData = {};
        // Chuyển mảng key/value thành đối tượng JavaScript đơn giản
        jQuery.each(formDataArray, function(i, field) {
            combinedData[field.name] = field.value;
        });
        combinedData['task'] = task;
        console.log(combinedData);
        jQuery.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: actionUrl,
            type: "POST",
            dataType:'json',
            data: combinedData,
            success: function (res) {
                showAlertSuccess(res.msg)
            },
            error: function(xhr, status, error) {
                showAlertError('Xử lý không thành công');
            },
        });
    });

    // --- 5. Logic Xử lý sự kiện (Tính toán, Đổi màu nền) ---

    // Gắn sự kiện input cho TẤT CẢ input trong bảng
    jQuery('body').on('input', '#devices input', function() {
        let row = $(this).closest('tr');

        // Chỉ chạy tính toán nếu input thay đổi là các trường số lượng liên quan
        if ($(this).hasClass('initial-total') || $(this).hasClass('increase-qty') || $(this).hasClass(
                'decrease-qty')) {
            calculateFinalTotal(row);
        }

        // Đổi màu nền (cho dù là tính toán hay chỉ nhập số hỏng)
        row.addClass(EDITED_BG_CLASS);
    });

    // --- 6. Chức năng Kéo thả (Sortable) ---
    $("#devices").sortable({
        items: "> tr",
        handle: 'td:first', // Kéo thả bằng cách click vào cột STT
        axis: "y",
        cursor: "grabbing",
        opacity: 0.8,
        update: function(event, ui) {
            updateSTT();
            ui.item.addClass(EDITED_BG_CLASS); // Đánh dấu hàng vừa kéo thả
        }
    }).disableSelection();

    // --- 7. Logic Thêm/Xóa ---

    // Show devices (Mở modal)
    jQuery('body').on('click', ".show-devices", function(e) {
        jQuery('#modal-devices').modal('show');
    });

    // Add device (Thêm hàng mới)
    jQuery('body').on('click', ".add-device", function(e) {
        let device_id = jQuery(this).data('device-id');
        let device_name = jQuery(this).closest('tr').data('name');
        let device_year = jQuery(this).closest('tr').data('year');
        let device_country = jQuery(this).closest('tr').data('country');
        let device_unit = jQuery(this).closest('tr').data('unit');
        let device_price = jQuery(this).closest('tr').data('price');
        let device_total = jQuery(this).closest('tr').data('total');
        let device_broken = jQuery(this).closest('tr').data('broken');

        deviceCount++;
        let newRowData = {
            device_id: device_id,
            device_name: device_name,
            year: device_year,
            country: device_country,
            unit: device_unit,
            price: device_price,
            initial_total: device_total,
            initial_broken: device_broken,
        };

        let device_html = createNewDeviceRow(deviceCount, newRowData);
        jQuery('#devices').append(device_html);

        jQuery(this).closest('td').empty().html('<span class="text-success">Đã Thêm</span>');

        updateSTT();
        calculateFinalTotal($('#devices tr').last()); // Tính toán ngay cho hàng mới
    });

    // Delete device (Xóa hàng)
    jQuery('body').on('click', ".delete-device-row", function(e) {
        e.preventDefault();
        jQuery(this).closest('tr').remove();
        updateSTT();
    });

    // --- Khởi tạo khi trang tải xong ---
    updateSTT();
    $('#devices tr').each(function() {
        calculateFinalTotal($(this));
    });
});
</script>
@endpush
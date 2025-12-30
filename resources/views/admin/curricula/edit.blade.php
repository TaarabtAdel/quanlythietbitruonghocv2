@extends('admin.layouts.master')
@section('title','Cập nhật phân phối chương trình #'. ($item->id ?? 'Mới'))
@section('content')
@include('globals.breadcrumb',[
    'page_title' => isset($item) && $item->id ? 'Cập nhật phân phối chương trình #'.$item->id : 'Thêm mới phân phối chương trình',
])

<form action="{{ isset($item) && $item->id 
        ? route($route_prefix.'update', $item->id) 
        : route($route_prefix.'store') }}" 
      method="post" 
      enctype="multipart/form-data"
      id="curriculum-form">

    @csrf
    @if(isset($item) && $item->id)
        @method('PUT')
    @endif

    <div class="row">
        <div class="col-12 col-lg-9">
            @include($view_path.'.includes.form-left')
        </div>
        <div class="col-12 col-lg-3">
            @include($view_path.'.includes.form-right')
        </div>
    </div>
</form>

<!--end row-->
@endsection

@push('footer')
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<script>
jQuery(document).ready(function($) {
    let detailCount = $('#curriculum-details tbody tr').length;
    
    // Khởi tạo Sortable cho tbody
    $("#curriculum-details tbody").sortable({
        helper: fixHelperModified,
        handle: 'td:first-child', // Chỉ cho phép kéo ở cột STT (hoặc bỏ dòng này để kéo cả hàng)
        update: function(event, ui) {
            updateAllIndexes();
        },
        placeholder: "ui-state-highlight"
    }).disableSelection();

    // Giữ nguyên chiều rộng của các cột khi đang kéo
    var fixHelperModified = function(e, tr) {
        var $originals = tr.children();
        var $helper = tr.clone();
        $helper.children().each(function(index) {
            $(this).width($originals.eq(index).width());
        });
        return $helper;
    };

    // Thêm chi tiết mới
    $(document).on('click', '.add-detail', function(e) {
        e.preventDefault();
        detailCount++;
        let newRow = `
            <tr class="detail-row">
                <td class="text-center align-middle" style="cursor: move;"><i class="bi bi-grip-vertical me-1"></i> <span></span></td>
                <td>
                    <input type="text" name="details[${detailCount}][week]" class="form-control form-control-sm text-center" placeholder="Tuần" min="1">
                </td>
                <td>
                    <input type="text" name="details[${detailCount}][lesson_number]" class="form-control form-control-sm text-center" placeholder="Tiết" min="1">
                </td>
                <td>
                    <input type="text" name="details[${detailCount}][lesson_name]" class="form-control form-control-sm" placeholder="Tên bài học" required>
                </td>
                <td class="text-center align-middle">
                    <button type="button" class="btn btn-sm btn-danger remove-detail">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            </tr>
        `;
        $('#curriculum-details tbody').append(newRow);
        updateAllIndexes();
    });

    // Xóa chi tiết
    $(document).on('click', '.remove-detail', function(e) {
        e.preventDefault();
        if(confirm('Bạn có chắc chắn muốn xóa hàng này?')) {
            $(this).closest('tr').remove();
            updateAllIndexes();
        }
    });

    // Cập nhật lại toàn bộ STT và Name Index để gửi dữ liệu chính xác về Server
    function updateAllIndexes() {
        $('#curriculum-details tbody tr').each(function(index) {
            let row = $(this);
            let displayIndex = index + 1;
            
            // Cập nhật số thứ tự hiển thị
            row.find('td:first span').text(displayIndex);
            
            // Cập nhật thuộc tính name của các input để server nhận mảng liên tục (0, 1, 2...)
            row.find('input').each(function() {
                let oldName = $(this).attr('name');
                if (oldName) {
                    let newName = oldName.replace(/details\[\d+\]/, `details[${index}]`);
                    $(this).attr('name', newName);
                }
            });
        });
    }

    // Chạy cập nhật lần đầu nếu đã có dữ liệu
    updateAllIndexes();

    // Form submit validation
    $('#curriculum-form').on('submit', function(e) {
        let hasDetail = false;
        $('.detail-row').each(function() {
            let lessonName = $(this).find('input[name*="[lesson_name]"]').val();
            if (lessonName && lessonName.trim() !== '') {
                hasDetail = true;
                return false;
            }
        });
        
        if (!hasDetail) {
            e.preventDefault();
            alert('Vui lòng thêm ít nhất một bài học.');
        }
    });
});
</script>

<style>
    /* Style cho hàng khi đang được kéo */
    .ui-state-highlight {
        height: 50px;
        background-color: #f8f9fa;
        border: 1px dashed #ccc;
    }
    .detail-row {
        background: white;
    }
    .ui-sortable-handle {
        cursor: move;
    }
</style>
@endpush

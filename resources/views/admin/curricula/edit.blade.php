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
        <div class="col-12 col-lg-8">
            @include($view_path.'.includes.form-left')
        </div>
        <div class="col-12 col-lg-4">
            @include($view_path.'.includes.form-right')
        </div>
    </div>
</form>

<!--end row-->
@endsection

@push('footer')
<script>
jQuery(document).ready(function($) {
    let detailCount = $('#curriculum-details tbody tr').length;
    
    // Thêm chi tiết mới
    $(document).on('click', '.add-detail', function(e) {
        e.preventDefault();
        detailCount++;
        let newRow = `
            <tr class="detail-row" data-index="${detailCount}">
                <td class="text-center align-middle">${detailCount}</td>
                <td>
                    <input type="number" name="details[${detailCount}][week]" class="form-control form-control-sm text-center" placeholder="Tuần PPCT" min="1">
                </td>
                <td>
                    <input type="number" name="details[${detailCount}][lesson_number]" class="form-control form-control-sm text-center" placeholder="Tiết PPCT" min="1">
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
        updateSTT();
    });

    // Xóa chi tiết
    $(document).on('click', '.remove-detail', function(e) {
        e.preventDefault();
        $(this).closest('tr').remove();
        updateSTT();
    });

    // Cập nhật STT
    function updateSTT() {
        $('#curriculum-details tbody tr').each(function(index) {
            $(this).find('td:first').text(index + 1);
        });
    }

    // Form submit
    $('#curriculum-form').on('submit', function(e) {
        // Validate ít nhất một chi tiết
        let hasDetail = false;
        $('#curriculum-details tbody tr').each(function() {
            let lessonName = $(this).find('input[name*="[lesson_name]"]').val();
            if (lessonName && lessonName.trim() !== '') {
                hasDetail = true;
                return false;
            }
        });
        
        if (!hasDetail) {
            e.preventDefault();
            alert('Vui lòng thêm ít nhất một bài học vào phân phối chương trình.');
            return false;
        }
    });
});
</script>
@endpush

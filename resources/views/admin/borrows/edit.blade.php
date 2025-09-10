@extends('admin.layouts.master')
@section('title','Cập nhật phiếu mượn #'. $item->id)
@section('content')
@include('globals.breadcrumb',[
    'page_title' => 'Chi tiết mã phiếu #'.$item->id,
])

<div class="row">
    <div class="col-12 col-lg-12">
        <form id="borrow-form" action="{{ route('borrows.update',$item->id) }}" method="post">
            <input type="hidden" name="task" id="task">
            <input type="hidden" name="tiet" id="tiet">
            <input type="hidden" name="item_id" id="item_id" value="{{ $item->id }}">
            <input type="hidden" name="device_id" id="device_id">
            <input type="hidden" name="qty" id="qty">
            <input type="hidden" name="status" id="status" value="{{ $item->status }}">
            @csrf
            @method('PUT')
            @include('teacher.borrows.includes.form')
        </form>
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-end gap-2 flex-column flex-lg-row">
                    <a href="{{ route($route_prefix.'index') }}" class="btn btn-sm btn-dark col-12 col-lg-auto">Quay lại</a>
                    @if (Auth::check() && Auth::user()->hasPermission('Borrow_approve'))
                        @if ($item->status == $model::INACTIVE)
                        <a onclick=" return confirm('{{ __('Bạn có chắc chắn duyệt phiếu này !') }}') "
                            class="btn btn-sm btn-success"
                            href="{{ route($route_prefix . 'index', ['task' => 'approve', 'id' => $item->id,'redirect'=>'show']) }}">
                            {{ __('sys.approve') }}
                        </a>
                        @endif
                    @endif
                    <form action="{{ route($route_prefix.'destroy',$item->id) }}" method="post" class="col-12 col-lg-auto">
                        @csrf
                        @method('DELETE')
                        <button onclick=" return confirm('{{ __('sys.confirm_delete') }}') " class="btn btn-sm btn-danger px-4 mr-2 col-12 col-lg-auto">
                            {{ $item->status == $model::CANCELED ? __('sys.force_delete') : 'Hủy phiếu' }}
                        </button>
                    </form>
                    <button id="submit_request" class="btn btn-sm btn-primary px-4 ml-2 col-12 col-lg-auto" >Lưu lại</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!--end row-->
@include('teacher.borrows.includes.modal-devices')
@include('teacher.borrows.includes.modal-labs')
@endsection
@push('footer')
    <script>
        var tiet_id = 0;
        // device-table-results
        var indexUrl = "{{ route('admin.borrows.index') }}";
        var positionUrl = "";
        var params = <?= json_encode(request()->query()); ?>;
        var wrapperResults = '.device-table-results';
        var actionUrl = jQuery('#borrow-form').attr('action');
        jQuery(document).ready(function() {
            //$("#repeater").createRepeater({
            //  showFirstItemToDefault: true,
            // isFirstItemUndeletable: true,
                //initEmpty:false
            //});
            // Handle show devices
            jQuery('body').on('click', ".show-devices", function(e) {
                tiet_id = jQuery(this).data('tiet-id');
                jQuery('#tiet').val(tiet_id);
                saveItem('show-devices');
            });
            jQuery('body').on('click', ".show-labs", function(e) {
                tiet_id = jQuery(this).data('tiet-id');
                jQuery('#tiet').val(tiet_id);
                saveItem('show-labs');
            });
            jQuery('body').on('click', ".delete-device", function(e) {
                tiet_id = jQuery(this).data('tiet-id');
                device_id = jQuery(this).data('device-id');
                jQuery('#tiet').val(tiet_id);
                jQuery('#device_id').val(device_id);
                jQuery(this).closest('.device_item').remove();
                saveItem('delete-device');
            });
            jQuery('body').on('change', ".change-qty-device", function(e) {
                tiet_id = jQuery(this).data('tiet-id');
                device_id = jQuery(this).data('device-id');
                jQuery('#tiet').val(tiet_id);
                jQuery('#device_id').val(device_id);
                jQuery('#qty').val(jQuery(this).val());
                saveItem('change-qty-device');
            });
            jQuery('body').on('click', ".add-tiet", function(e) {
                saveItem('add-tiet');
            });
            jQuery('body').on('click', "#save_draft", function(e) {
                jQuery('#borrow-form').find('#status').val('{{ $model::DRAFT }}');
                saveItem('save-draft');
            });
            jQuery('body').on('click', "#submit_request", function(e) {
                // jQuery('#borrow-form').find('#status').val('{{ $model::INACTIVE }}');
                saveItem('save-form');
            });
            jQuery('body').on('click', ".delete-tiet", function(e) {
                let ask = confirm('Bạn có chắc chắn xóa tiết dạy này không ?')
                if( ask == true ){
                    tiet_id = jQuery(this).data('tiet-id');
                    jQuery('#tiet').val(tiet_id);
                    jQuery(this).closest('.items').remove();
                    saveItem('delete-tiet');
                }
            });
            // Xử lý xóa phòng bộ môn
            jQuery('body').on('click', ".delete-lab", function(e) {
                tiet_id = jQuery(this).data('tiet-id');
                jQuery('#tiet').val(tiet_id);
                let lab_choiced = jQuery(this).closest('.lab-choiced');
                lab_id = lab_choiced.find('[data-name="lab_id"]').val('');
                lab_choiced.find('.lab_id').val('');
                lab_choiced.find('.show-labs').html('Chọn');
                lab_choiced.find('.delete-lab').addClass('d-none');

                tiet_id = jQuery(this).data('tiet-id');
                jQuery('#tiet').val(tiet_id);

                saveItem('delete-lab');
            })
            jQuery('body').on('click', ".add-lab", function(e) {
                tiet_id = jQuery('#tiet').val();
                let lab_name = jQuery(this).data('name');
                let lab_id = jQuery(this).data('lab-id');
                jQuery('.items[data-tiet="'+tiet_id+'"]').find('[data-name="lab_id"]').val(lab_id);
                jQuery('.items[data-tiet="'+tiet_id+'"]').find('.show-labs').html(lab_name);
                jQuery('.items[data-tiet="'+tiet_id+'"]').find('.delete-lab').removeClass('d-none');
                saveItem('add-lab');
            });

            // Handle add device
            jQuery('body').on('click', ".add-device", function(e) {
                let device_id = jQuery(this).data('device-id');
                let device_name = jQuery(this).closest('tr').data('name');
                let device_type_name = jQuery(this).closest('tr').data('device_type_name');
                let department_name = jQuery(this).closest('tr').data('department_name');
                let device_html = `
                    <tr class="device_item">
                        <td><input name="devices[${tiet_id}][device_id]" type="hidden" value="${device_id}"></td>
                        <td>${device_name}</td>
                        <td width="100px">
                            <input name="devices[${tiet_id}][quantity]" type="number" min="1" value="1" class="form-control">
                        </td>
                        <td>${device_type_name}</td>
                        <td>${department_name}</td>
                        <td>
                            <button class="btn btn-sm btn-danger">Xóa</button>
                        </td>
                    </tr>
                `;
                jQuery('.items[data-tiet="'+tiet_id+'"]').find('.tiet_devices').append(device_html);
                saveItem('add-device');
            });

            function saveItem(task = 'show-devices'){
                jQuery('#task').val(task);
                jQuery.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: actionUrl,
                    type: "POST",
                    dataType:'json',
                    data: jQuery('#borrow-form').serialize(),
                    success: function (res) {
                        jQuery('.input-error').empty();
                        if (res.has_errors) {
                            for (const key in res.errors) {
                                let keyClass = key.replaceAll('.','-');
                                jQuery('.input-' + keyClass).find('.input-error').html(res.errors[key][0]);
                            }
                            showAlertError('Vui lòng điền vào các trường bắt buộc !')
                        }else{
                            if (res.success == true) {
                                if(task == 'show-devices'){
                                    jQuery('#modal-devices').modal('show');
                                }
                                if(task == 'save-draft' || task == 'add-tiet' || task == 'delete-tiet'){
                                    showAlertSuccess(res.msg)
                                    window.location.reload();
                                }
                                if(task == 'show-labs'){
                                    jQuery('#modal-labs').modal('show');
                                }
                                if( task == 'save-form' ){
                                    showAlertSuccess(res.msg)
                                    window.location.href = indexUrl;
                                }
                                if( task == 'add-lab' ){
                                    jQuery('#modal-labs').modal('hide');
                                }
                            }else{
                                showAlertError(res.msg)
                            }
                        }
                    }
                });
            }

        });
    </script>
@endpush
@extends('admin.layouts.master')
@section('title','Xuất: ' . __(request()->type) )
@section('content')
    @include('globals.breadcrumb', [
        'page_title' => 'Xuất: ' . __(request()->type),
        'actions' => [],
    ])
    <div class="card mt-4">
        <div class="card-body">
            <form action="{{ route($route_prefix . 'store') }}" method="post" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="type" value="{{ request()->type }}">
                <div class="mb-4">
                    <h5 class="mb-4">Bạn đang chuẩn bị xuất dữ liệu cho: {{ __(request()->type) }}</h5>
                    <p class="mb-0">- Dữ liệu xuất sẽ được lưu vào file excel</p>
                    <p class="mb-0">- Xem mẫu xuất bên phải</p>
                    <p class="mt-0">- Nhấn vào <strong>Tiến Hành</strong> để xuất </p>
                </div>
                @include($view_path.'types.'.$type_slug)
                <div class="mb-4">
                    <div class="d-md-flex d-grid align-items-center gap-3">
                        <a href="{{ route('admin.export.index', ['type' => request()->type]) }}" class="btn btn-dark">Quay lại</a>
                        <button class="btn btn-primary px-4">Tiến Hành</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <script>
        jQuery(document).ready(function () {
            console.log('JavaScript is working!');
            jQuery('#year').on('change', function () {
                var selectedYear = jQuery(this).val();
                if (selectedYear) {
                    var years = selectedYear.split('-');
                    var startDate = years[0] + '-08-01';
                    var endDate = years[1] + '-07-01';
                    jQuery('input[name="start_date"]').val(startDate);
                    jQuery('input[name="end_date"]').val(endDate);
                }
            });

            let default_option = jQuery('.preview-demo option').eq(0);
            if (default_option.length) {
                let default_image_src = default_option.data('img');
                jQuery('#preview-demo-img').html('<img class="img-fluid" src="'+default_image_src+'">');
                jQuery('.preview-demo').on('change', function() {
                    let new_image_src = jQuery(this).find('option:selected').data('img');
                    jQuery('#preview-demo-img').find('img').attr('src', new_image_src);
                });
            }
            
        });

    </script>

@endsection
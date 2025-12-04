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
                    <p class="mb-0">- Nhấn vào <a href="#" data-bs-toggle="modal" data-bs-target="#imageModal">đây</a> để xem mẫu kết quả 1.</p>
                    @if(request()->type == 'BorrowLab')
                     <p class="mb-0">- Nhấn vào <a href="#" data-bs-toggle="modal" data-bs-target="#imageModal2">đây</a> để xem mẫu kết quả 2.</p>
                    @endif
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
    <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content bg-transparent border-0">
                <div class="modal-footer border-0 justify-content-center pt-2">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Đóng</button>
                </div>
                <div class="modal-body p-0" style="text-align:center;">
                    <img src="{{ asset('system/export/preview/'.$type_slug.'.png') }}" alt="Preview" style="width:auto;max-width:100%;" class="img-fluid rounded">
                </div>
                
            </div>
        </div>
    </div>
    <div class="modal fade" id="imageModal2" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content bg-transparent border-0">
                <div class="modal-footer border-0 justify-content-center pt-2">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Đóng</button>
                </div>    
                <div class="modal-body p-0" style="text-align:center;">
                    <img src="{{ asset('system/export/preview/'.$type_slug.'_2.png') }}" alt="Preview" style="width:auto;max-width:100%;" class="img-fluid rounded">
                </div>
            </div>
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
        });

    </script>

@endsection
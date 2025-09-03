@extends('teacher.layouts.master')
@section('title', 'Chi tiết tài liệu')
@section('content')
@include('globals.breadcrumb',[
    'page_title' => 'Chi tiết tài liệu',
    'actions' => []
])

<div class="card mt-4">
    <div class="card-body">
        <div class="row">
            <div class="col-md-8">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="card-title mb-0">{{ $item->name }}</h4>
                    <a href="/{{ $item->image }}" class="btn btn-primary" download>
                        <i class="fas fa-download me-2"></i> Tải xuống
                    </a>
                </div>

                @php
                    $extension = strtolower(pathinfo($item->image, PATHINFO_EXTENSION));
                    $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'];
                    $pdfExtensions = ['pdf'];
                    $videoExtensions = ['mp4', 'webm', 'ogg'];
                    $audioExtensions = ['mp3', 'wav', 'ogg'];
                @endphp

                @if($item->description)
                    <div class="description mt-4">
                        <h5 class="mb-3">Mô tả</h5>
                        <div class="p-3 bg-light rounded">
                            {!! nl2br(e($item->description)) !!}
                        </div>
                    </div>
                @endif

                <div class="document-preview mb-4">
                    @if(in_array($extension, $imageExtensions))
                        <img src="/{{ $item->image }}" class="img-fluid rounded" alt="{{ $item->name }}">
                    @elseif(in_array($extension, $pdfExtensions))
                        <div class="ratio ratio-16x9">
                            <iframe src="/{{ $item->image }}" frameborder="0"></iframe>
                        </div>
                    @elseif(in_array($extension, $videoExtensions))
                        <video class="w-100" controls>
                            <source src="/{{ $item->image }}" type="video/{{ $extension }}">
                            Trình duyệt của bạn không hỗ trợ xem video.
                        </video>
                    @elseif(in_array($extension, $audioExtensions))
                        <audio class="w-100" controls>
                            <source src="/{{ $item->image }}" type="audio/{{ $extension }}">
                            Trình duyệt của bạn không hỗ trợ nghe audio.
                        </audio>
                    @else
                        <div class="text-center p-5 bg-light rounded">
                            <i class="fas fa-file-alt fa-3x text-primary mb-3"></i>
                            <p class="mb-0">File {{ strtoupper($extension) }} không thể xem trực tiếp. Vui lòng tải xuống để xem.</p>
                        </div>
                    @endif
                </div>

                
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title mb-3">Thông tin tài liệu</h5>
                        
                        <div class="mb-3">
                            <small class="text-muted">Định dạng file:</small>
                            <div class="fw-bold text-uppercase">{{ $extension }}</div>
                        </div>

                        <div class="mb-3">
                            <small class="text-muted">Ngày tạo:</small>
                            <div class="fw-bold">{{ $item->created_at->format('d/m/Y H:i') }}</div>
                        </div>

                        <div class="mb-3">
                            <small class="text-muted">Cập nhật lần cuối:</small>
                            <div class="fw-bold">{{ $item->updated_at->format('d/m/Y H:i') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
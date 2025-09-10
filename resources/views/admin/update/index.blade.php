@extends('admin.layouts.auth')
@section('content')
    <h4 class="fw-bold text-center">Cập Nhật Phần Mềm</h4>
    <p class="mb-1 text-center">Phiên bản hiện tại: <span class="fw-bold">{{ $currentVersion }}</span></p>
    <p class="mb-2 text-center">Phiên bản mới nhất: <span class="fw-bold">{{ $lastVersion }}</span></p>

    <div class="separator section-padding">
        <div class="line"></div>
    </div>

    @if($currentVersion != $lastVersion)
    <div class="form-body mt-4">
        <form class="row g-3" method="POST" action="{{ route('admin.system.doUpdate') }}">
            @csrf
            <div class="col-12">
                <div class="text-center">
                    <p class="mb-0">Lưu ý: <br> Không tắt trình duyệt khi quá trình cập nhật đang diễn ra</p>
                </div>
            </div>
            <div class="col-12">
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary">Cập nhật phiên bản mới</button>
                </div>
            </div>
            
        </form>
    </div>
    @else
    <div class="form-body mt-4">
        <form class="row g-3" method="POST" action="{{ route('admin.system.doUpdate') }}">
            @csrf
            <div class="col-12">
                <div class="text-center">
                    <p class="mb-0 text-success">Bạn đang sử dụng phiên bản mới nhất</p>
                </div>
            </div>
            <div class="col-12">
                <div class="d-grid">
                    <a href="{{ route('admin.home') }}" type="submit" class="btn btn-primary">Quay lại</a>
                </div>
            </div>
            
        </form>
    </div>
    @endif
@endsection
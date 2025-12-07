@extends('admin.layouts.master')
@section('title','Xem phiếu kiểm kê #'. $item->id)
@section('content')
@include('globals.breadcrumb',[
    'page_title' => 'Xem phiếu kiểm kê #'.$item->id,
    'actions' => [
        'Export' => route('admin.export.store',['type'=>'InventoryAudit','id'=>$item->id])
    ]
])
<form id="inventory_audits-form" action="{{ isset($item) && $item->id 
        ? route($route_prefix.'.update', $item->id) 
        : route($route_prefix.'.store') }}" method="post" enctype="multipart/form-data">

    @csrf
    @if(isset($item) && $item->id)
    @method('PUT')
    @include('admin.inventory_audits.includes.form-show')
    @endif

</form>

<div class="card">
    <div class="card-body">
        {{-- THAY ĐỔI TẠI ĐÂY: Dùng justify-content-end và chỉ giữ lại khối bên trái --}}
        <div class="d-flex align-items-center justify-content-end flex-column flex-lg-row">
            
            {{-- ⬅️ KHỐI NÚT ĐÃ CĂN PHẢI (Trước đây là khối bên trái) --}}
            <div class="d-flex gap-2 mb-2 mb-lg-0">
                
                {{-- Nút Quay lại --}}
                <a href="{{ route($route_prefix.'.index') }}" class="btn btn-sm btn-secondary px-4 col-12 col-lg-auto">
                    <i class='bx bx-arrow-back'></i> Quay lại
                </a>
                
                {{-- Nút Cập nhật (Chuyển sang trang sửa/edit) --}}
                @if(isset($item) && $item->id)
                <a href="{{ route($route_prefix.'.edit', $item->id) }}" class="btn btn-sm btn-info px-4 col-12 col-lg-auto">
                    <i class='bx bx-edit-alt'></i> Cập nhật
                </a>
                
                {{-- Nút Xuất phiếu (Xuất PDF/Excel) --}}
                <a href="{{ route('admin.export.store',['type'=>'InventoryAudit','id'=>$item->id]) }}" class="btn btn-sm btn-warning px-4 col-12 col-lg-auto"  >
                    <i class='bx bx-download'></i> Xuất phiếu
                </a>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
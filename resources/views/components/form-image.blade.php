@if($upload)
<input class="form-control" name="{{ $name }}" type="file" accept="{{ $accept }}">
@endif
@php
    $extension = strtolower(pathinfo($imageUrl, PATHINFO_EXTENSION));
    $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'];
@endphp

@if(in_array($extension, $imageExtensions))
    <div class="card mt-2">
        <img src="{{ $imageUrl }}" class="card-img">
    </div>
@else
    <div class="mt-2">
        Tên tệp tin: {{ $imageUrl }}
    </div>
    <div class="mt-2">
        <a href="{{ $imageUrl }}" class="btn btn-primary" download>
            <i class="fas fa-download"></i> Tải xuống
        </a>
    </div>
@endif

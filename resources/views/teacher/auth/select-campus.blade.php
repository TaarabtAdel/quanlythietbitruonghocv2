<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Chọn cơ sở - Quản Lý Thiết Bị</title>
    <link href="/admin-assets/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="/admin-assets/css/main.css" rel="stylesheet">
</head>
<body>
    <div class="container-fluid my-5">
        <div class="row">
            <div class="col-12 col-md-8 col-lg-6 col-xl-5 col-xxl-4 mx-auto">
                <div class="card border-3">
                    <div class="card-body p-5">
                        <h3 class="fw-bold text-center">Chọn cơ sở</h3>
                        <p class="text-center text-muted">Trường có nhiều cơ sở. Chọn cơ sở để làm việc.</p>
                        @if (session('error'))
                            <div class="alert alert-danger">{{ session('error') }}</div>
                        @endif
                        <form action="{{ route('campuses.choose') }}" method="POST">
                            @csrf
                            <div class="d-grid gap-2 mt-3">
                                @foreach ($campuses as $campus)
                                    <button type="submit" name="campus_key" value="{{ $campus['key'] }}"
                                        class="btn {{ $currentKey == $campus['key'] ? 'btn-primary' : 'btn-outline-primary' }} text-start py-3">
                                        <strong>{{ $campus['name'] }}</strong>
                                        @if ($campus['is_main'])
                                            <span class="badge bg-light text-dark ms-1">Cơ sở chính</span>
                                        @endif
                                    </button>
                                @endforeach
                            </div>
                        </form>
                        <p class="text-center mt-4 mb-0">
                            <a href="{{ route('auth.logout') }}">Đăng xuất</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

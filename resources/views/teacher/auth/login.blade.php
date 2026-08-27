<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Đăng nhập - Quản Lý Thiết Bị</title>
    <!-- Fonts -->

    <!--Styles-->
    <link href="/admin-assets/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="/admin-assets/css/icons.css">

    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="/admin-assets/css/main.css" rel="stylesheet">

</head>

<body>
    <!--authentication-->

    <div class="container-fluid my-5">
        <div class="row">
            <div class="col-12 col-md-8 col-lg-6 col-xl-5 col-xxl-4 mx-auto">
                <div class="card border-3">
                    <div class="card-body p-5">
                        <h3 class="fw-bold text-center">QUẢN LÍ THIẾT BỊ</h3>
                        <div class="form-body mt-4">
                            <form class="row g-3" action="{{ route('auth.postLogin') }}" method="POST">
                                @csrf
                                @if (session('error'))
                                    <div class="alert alert-danger">
                                        {{ session('error') }}
                                    </div> 
                                @endif
                                @if (!empty($showCampusSelect))
                                <div class="col-12">
                                    <label for="campus_key" class="form-label">Cơ sở <span class="text-danger">*</span></label>
                                    <select class="form-select" id="campus_key" name="campus_key" required>
                                        <option value="">-- Chọn cơ sở --</option>
                                        @foreach ($campuses as $campus)
                                            <option value="{{ $campus['key'] }}" @selected(old('campus_key', count($campuses) === 1 ? 'main' : '') == $campus['key'])>
                                                {{ $campus['name'] }}{{ $campus['is_main'] ? ' (Cơ sở chính)' : '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('campus_key')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                @endif
                                <div class="col-12">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" name="email"
                                        placeholder="jhon@example.com">
                                    @error('email')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-12">
                                    <label for="password" class="form-label">Password</label>
                                    <div class="input-group" id="show_hide_password">
                                        <input type="password" class="form-control border-end-0" id="password"
                                            name="password" value="" placeholder="Enter Password">
                                        <a href="javascript:;" class="input-group-text bg-transparent"><i
                                                class="bi bi-eye-slash-fill"></i></a>
                                    </div>
                                    @error('password')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="remember" name="remember">
                                        <label class="form-check-label" for="remember">Ghi nhớ mật khẩu</label>
                                    </div>
                                </div>
                                <div class="col-md-6 text-end">
                                    
                                </div>
                                <div class="col-12">
                                    <div class="d-grid">
                                        <button type="submit" class="btn btn-primary">Đăng Nhập</button>
                                    </div>
                                    <p class="text-center mt-3 mb-0"><a title="Giới thiệu phần mềm quản lý thiết bị trường học" href="https://quanlythietbitruonghoc.com">Giới thiệu phần mềm</a></p>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--end row-->
    </div>

    <script src="/admin-assets/js/jquery.min.js"></script>


    <script>
    $(document).ready(function() {
        $("#show_hide_password a").on('click', function(event) {
            event.preventDefault();
            if ($('#show_hide_password input').attr("type") == "text") {
                $('#show_hide_password input').attr('type', 'password');
                $('#show_hide_password i').addClass("bi-eye-slash-fill");
                $('#show_hide_password i').removeClass("bi-eye-fill");
            } else if ($('#show_hide_password input').attr("type") == "password") {
                $('#show_hide_password input').attr('type', 'text');
                $('#show_hide_password i').removeClass("bi-eye-slash-fill");
                $('#show_hide_password i').addClass("bi-eye-fill");
            }
        });
    });
    </script>
</body>
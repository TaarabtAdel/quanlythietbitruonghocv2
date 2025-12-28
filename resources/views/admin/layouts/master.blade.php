<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>@yield('title')</title>
    <meta name="description" content="">
    <meta name="keywords" content="">
    <meta name="author" content="">

    <!-- Fonts -->
    <!--plugins-->
    <link href="/admin-assets/plugins/perfect-scrollbar/css/perfect-scrollbar.css" rel="stylesheet">
    <link href="/admin-assets/plugins/metismenu/css/metisMenu.min.css" rel="stylesheet">
    <link href="/admin-assets/plugins/simplebar/css/simplebar.css" rel="stylesheet">
    <!-- loader-->
    <link href="/admin-assets/css/pace.min.css" rel="stylesheet">
    <script src="/admin-assets/js/pace.min.js"></script>
    <!--Styles-->
    <link href="/admin-assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="/admin-assets/plugins/notifications/css/lobibox.min.css" rel="stylesheet">
    <link href="/admin-assets/plugins/select2/css/select2.min.css" rel="stylesheet">
    <link href="/admin-assets/plugins/select2/css/select2-bootstrap-5-theme.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="/admin-assets/css/icons.css">

    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="/admin-assets/css/main.css" rel="stylesheet">
    <link href="/admin-assets/css/dark-theme.css" rel="stylesheet">
    <script src="/admin-assets/js/jquery.min.js"></script>
    <link href="/admin-assets/css/school-week-plugin.css" rel="stylesheet">
    <link href="/admin-assets/css/lecture-name-plugin.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/weekSelect/weekSelect.js"></script>
    <script src="/admin-assets/plugins/select2/js/select2.min.js"></script>
    @stack('header')
</head>

<body>
    
    <!--start header-->
    @include('admin.sections.header')
    <!--end header-->
    <!--start sidebar-->
    @include('admin.sections.sidebar')
    <!--end sidebar-->

    <main class="page-content">
        @yield('content')
    </main>

    <!--start overlay-->
    <div class="overlay btn-toggle-menu"></div>
    <!--end overlay-->

    
    <script src="/admin-assets/plugins/perfect-scrollbar/js/perfect-scrollbar.js">
    </script>
    <script src="/admin-assets/plugins/metismenu/js/metisMenu.min.js">
    </script>
    <script src="/admin-assets/plugins/simplebar/js/simplebar.min.js">
    </script>
    <script src="/admin-assets/plugins/notifications/js/lobibox.min.js">
    </script>
    <script src="/admin-assets/js/school-week-plugin.js"></script>
    <script src="/admin-assets/js/lecture-name-plugin.js"></script>

    <!--BS Scripts-->
    <script src="/admin-assets/js/bootstrap.bundle.min.js"></script>
    <script src="/admin-assets/js/main.js"></script>
    <script src="/admin-assets/js/app.js"></script>

    @stack('footer')
    @if (session('success'))
        <script>
            jQuery(document).ready( function(){
                let msg = "{{ session('success') }}";
                showAlertSuccess(msg)
            })
        </script>
    @endif
    @if (session('error'))
        <script>
            jQuery(document).ready( function(){
                let msg = "{{ session('error') }}";
                showAlertError(msg)
            })
        </script>
    @endif
</body>

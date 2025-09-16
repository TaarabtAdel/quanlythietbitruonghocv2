<header class="top-header">
    <nav class="navbar navbar-expand justify-content-between">
        <div class="btn-toggle-menu">
            <span class="material-symbols-outlined">menu</span>
        </div>
        <div class="d-lg-block d-none search-bar" style="width:80%">
            <marquee behavior="" direction="">Thông báo: Các thầy cô có thể lựa chọn MỤC ĐÍCH tại phiếu báo mượn để tiện
                thống kê sau này, Quản Trị Viên có thể cập nhật ghi chú, trạng thái đã trả hay chưa để tiện theo dõi
            </marquee>
        </div>
        <ul class="navbar-nav top-right-menu gap-2">
            <li class="nav-item d-lg-none d-block" data-bs-toggle="modal" data-bs-target="#exampleModal">
                <a class="nav-link" href="javascript:;"><span class="material-symbols-outlined">
                        search
                    </span></a>
            </li>
            <li class="nav-item dark-mode">
                <a class="nav-link dark-mode-icon" href="javascript:;"><span
                        class="material-symbols-outlined">dark_mode</span></a>
            </li>

            <li class="nav-item dropdown dropdown-large">
                <a class="nav-link dropdown-toggle dropdown-toggle-nocaret" href="javascript:;"
                    data-bs-toggle="dropdown">
                    <div class="position-relative">
                        <span class="notify-badge">0</span>
                        <span class="material-symbols-outlined">
                            notifications_none
                        </span>
                    </div>
                </a>
                <div class="dropdown-menu dropdown-menu-end mt-lg-2">
                    <a href="javascript:;">
                        <div class="msg-header">
                            <p class="msg-header-title">Thông báo</p>
                            <p class="msg-header-clear ms-auto">
                                <a href="/is-read" class="text-decoration-none">
                                    Đánh dấu tất cả là đã đọc
                                </a>
                            </p>

                        </div>
                    </a>
                    <div class="header-notifications-list">

                    </div>
                    <a href="javascript:;">
                        <div class="text-center msg-footer">Xem tất cả</div>
                    </a>
                </div>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="settingsDropdown" role="button"
                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <span class="material-symbols-outlined">
                        settings
                    </span>
                </a>
                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="settingsDropdown">
                    <a class="dropdown-item" href="/">
                        <span class="material-symbols-outlined me-2"></span>
                        <span>Thoát Quản trị</span>
                    </a>
                    <a class="dropdown-item" href="/profile">
                        <span class="material-symbols-outlined me-2"></span>
                        <span>Tài Khoản</span>
                    </a>
                    <a class="dropdown-item" href="/auth/logout">
                        <span class="material-symbols-outlined me-2"></span>
                        <span>Thoát</span>
                    </a>
                </div>
                <style>
                .dropdown-toggle::after {
                    content: none;
                }
                </style>
            </li>
        </ul>
    </nav>
</header>
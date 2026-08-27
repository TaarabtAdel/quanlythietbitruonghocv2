<header class="top-header">
    <nav class="navbar navbar-expand justify-content-between">
        <div class="btn-toggle-menu">
            <span class="material-symbols-outlined">menu</span>
        </div>
        @php $campusLabel = $currentCampusName ?: 'Cơ sở chính'; @endphp
        @if (!empty($canBrowseCampuses) && !empty($campusList))
        <div class="dropdown ms-2">
            <a class="btn btn-sm btn-primary d-inline-flex align-items-center gap-1 px-2 py-1" href="#" role="button" data-bs-toggle="dropdown" title="Cơ sở hiện tại">
                <span class="material-symbols-outlined" style="font-size:18px">apartment</span>
                <span class="fw-semibold text-truncate" style="max-width: min(42vw, 280px);">{{ $campusLabel }}</span>
            </a>
            <ul class="dropdown-menu">
                @foreach ($campusList as $campus)
                <li>
                    <form action="{{ route('admin.campuses.switch') }}" method="post">
                        @csrf
                        <input type="hidden" name="campus_key" value="{{ $campus['key'] }}">
                        <button type="submit" class="dropdown-item {{ ($currentCampusKey ?? '') == $campus['key'] ? 'active' : '' }}">
                            {{ $campus['name'] }}{{ $campus['is_main'] ? ' (chính)' : '' }}
                        </button>
                    </form>
                </li>
                @endforeach
                @if (!empty($canManageCampuses))
                <li><hr class="dropdown-divider"></li>
                <li>
                    <a class="dropdown-item" href="{{ route('admin.campuses.index') }}">Quản lý cơ sở</a>
                </li>
                @endif
            </ul>
        </div>
        @else
        <div class="ms-2 d-inline-flex align-items-center gap-1 px-2 py-1 rounded bg-primary-subtle text-primary" title="Cơ sở hiện tại">
            <span class="material-symbols-outlined" style="font-size:18px">apartment</span>
            <span class="fw-semibold text-truncate" style="max-width: min(42vw, 280px);">{{ $campusLabel }}</span>
        </div>
        @endif
        <div class="d-lg-block d-none search-bar flex-grow-1">
            <marquee behavior="" direction="">{{ env('ADMIN_WELLCOME', 'Chào mừng bạn đến với hệ thống quản trị!') }}</marquee>
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
                    <a class="dropdown-item" href="/teacher/logout">
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
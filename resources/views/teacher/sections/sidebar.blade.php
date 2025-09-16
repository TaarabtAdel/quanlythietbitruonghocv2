<!--start sidebar-->
<aside class="sidebar-wrapper">
    <div class="sidebar-header">
        <div class="logo-name flex-grow-1">
            <h5 class="mb-0">{{ config('app.name') }}</h5>
        </div>
        <div class="sidebar-close ">
            <span class="material-symbols-outlined">close</span>
        </div>
    </div>
    <div class="sidebar-nav" data-simplebar="true">
        <!--navigation-->
        <ul class="metismenu" id="menu">
            <li class="menu-label">Giáo Viên</li>
            <li>
                <a href="{{ route('home') }}">
                    <div class="parent-icon">
                        <span class="material-symbols-outlined">dashboard</span>
                    </div>
                    <div class="menu-title">Trang Chủ</div>
                </a>
            </li>
            <li>
                <a class="has-arrow" href="javascript:;">
                    <div class="parent-icon">
                        <span class="material-symbols-outlined">receipt_long</span>
                    </div>
                    <div class="menu-title">Mượn Thiết Bị</div>
                </a>
                <ul class="mm-collapse">
                    <li>
                        <a href="{{ route('borrows.create') }}">
                            <span class="material-symbols-outlined">arrow_right</span>Tạo Phiếu Mượn
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('borrows.index') }}">
                            <span class="material-symbols-outlined">arrow_right</span>Phiếu Mượn
                        </a>
                    </li>
                </ul>
            </li>
            <li>
                <a href="{{ route('borrows.labs') }}">
                    <div class="parent-icon">
                        <span class="material-symbols-outlined">calendar_month</span>
                    </div>
                    <div class="menu-title">Lịch Sử Dụng Phòng</div>
                </a>
            </li>
            <li>
                <a href="{{ route('documents.index') }}">
                    <div class="parent-icon">
                        <span class="material-symbols-outlined">view_kanban</span>
                    </div>
                    <div class="menu-title">Văn Bản Thiết Bị</div>
                </a>
            </li>
            <li>
                <a class="has-arrow" href="javascript:;">
                    <div class="parent-icon">
                        <span class="material-symbols-outlined">home</span>
                    </div>
                    <div class="menu-title">Trường Học</div>
                </a>
                <ul class="mm-collapse">
                    <li>
                        <a href="{{ route('devices.index') }}">
                            <span class="material-symbols-outlined">arrow_right</span>Thiết Bị
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('labs.index') }}">
                            <span class="material-symbols-outlined">arrow_right</span>Phòng Bộ Môn
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('rooms.index') }}">
                            <span class="material-symbols-outlined">arrow_right</span>Lớp Học
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('users.index') }}">
                            <span class="material-symbols-outlined">arrow_right</span>Giáo Viên
                        </a>
                    </li>
                </ul>
            </li>
            <li>
                <a target="_blank" href="https://huongdan.quanlythietbitruonghoc.com/">
                    <div class="parent-icon">
                        <span class="material-symbols-outlined">help</span>
                    </div>
                    <div class="menu-title">Hướng Dẫn</div>
                </a>
            </li>
        </ul>
        <!--end navigation-->
    </div>
    <div class="sidebar-bottom dropdown dropup-center dropup">
        <div class="dropdown-toggle d-flex align-items-center px-3 gap-3 w-100 h-100" data-bs-toggle="dropdown">
            <div class="user-info">
                <h5 class="mb-0 user-name">{{ Auth::user()->name }}</h5>
            </div>
        </div>
        <ul class="dropdown-menu dropdown-menu-end">
            <li>
                <a class="dropdown-item" href="/admin">
                    <span class="material-symbols-outlined me-2"></span>
                    <span>Vào Quản trị</span>
                </a>
            </li>
            <li>
                <a class="dropdown-item" href="{{route('users.profile')}}">
                    <span class="material-symbols-outlined me-2"></span>
                    <span>Tài khoản</span>
                </a>
            </li>
            <li>
                <a class="dropdown-item" href="{{ route('auth.logout') }}">
                    <span class="material-symbols-outlined me-2"></span>
                    <span>Thoát</span>
                </a>
            </li>
        </ul>
    </div>
</aside>
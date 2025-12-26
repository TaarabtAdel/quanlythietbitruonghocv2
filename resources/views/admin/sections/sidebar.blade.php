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
        <ul class="metismenu" id="menu">
            <li class="menu-label">Quản Lý</li>
            <li>
                <a href="{{ route('admin.home') }}">
                    <div class="parent-icon">
                        <span class="material-symbols-outlined">dashboard</span>
                    </div>
                    <div class="menu-title">Trang Tổng Quan</div>
                </a>
            </li>
            @if (( Auth::check() && Auth::user()->hasPermission( 'BorrowLab_viewAny' )) || ( Auth::check() &&
            Auth::user()->hasPermission( 'BorrowDevice_viewAny' )) || ( Auth::check() && Auth::user()->hasPermission(
            'Borrow_viewAny' )))
            <li>
                <a class="has-arrow" href="javascript:;">
                    <div class="parent-icon">
                        <span class="material-symbols-outlined">backup_table</span>
                    </div>
                    <div class="menu-title">Quản Lý Mượn</div>
                </a>
                <ul class="mm-collapse">
                    @if (Auth::check() && Auth::user()->hasPermission('Borrow_viewAny'))
                    <li>
                        <a href="{{ route('admin.borrows.index') }}">
                            <span class="material-symbols-outlined">arrow_right</span> Phiếu Mượn
                        </a>
                    </li>
                    @endif
                    @if (Auth::check() && Auth::user()->hasPermission('BorrowDevice_viewAny'))
                    <li>
                        <a href="{{ route('admin.borrows.devices') }}">
                            <span class="material-symbols-outlined">arrow_right</span>Thiết Bị Mượn
                        </a>
                    </li>
                    @endif
                    @if (Auth::check() && Auth::user()->hasPermission('BorrowLab_viewAny'))
                    <li>
                        <a href="{{ route('admin.borrows.labs') }}">
                            <span class="material-symbols-outlined">arrow_right</span>Phòng Mượn
                        </a>
                    </li>
                    @endif
                </ul>
            </li>
            @endif
            @if(auth()->user()->CanManagerSchool())
            <li>
                <a class="has-arrow" href="javascript:;">
                    <div class="parent-icon">
                        <span class="material-symbols-outlined">home</span>
                    </div>
                    <div class="menu-title">Trường Học</div>
                </a>
                <ul class="mm-collapse">
                    @if (Auth::check() && Auth::user()->hasPermission('Device_viewAny'))
                    <li>
                        <a href="{{ route('admin.devices.index', ['type' => 'Device']) }}">
                            <span class="material-symbols-outlined">arrow_right</span>Thiết Bị
                        </a>
                    </li>
                    @endif
                    @if (Auth::check() && Auth::user()->hasPermission('Asset_viewAny'))
                    <li>
                        <a href="{{ route('admin.assets.index', ['type' => 'Asset']) }}">
                            <span class="material-symbols-outlined">arrow_right</span>Tài Sản
                        </a>
                    </li>
                    @endif
                    @if (Auth::check() && Auth::user()->hasPermission('Lab_viewAny'))
                    <li>
                        <a href="{{ route('admin.labs.index', ['type' => 'Lab']) }}">
                            <span class="material-symbols-outlined">arrow_right</span>Phòng Bộ Môn
                        </a>
                    </li>
                    @endif
                    @if (Auth::check() && Auth::user()->hasPermission('DeviceType_viewAny'))
                    <li>
                        <a href="{{ route('admin.device-types.index', ['type' => 'DeviceType']) }}">
                            <span class="material-symbols-outlined">arrow_right</span>Nhóm Thiết Bị
                        </a>
                    </li>
                    @endif
                    @if (Auth::check() && Auth::user()->hasPermission('Room_viewAny'))
                    <li>
                        <a href="{{ route('admin.rooms.index', ['type' => 'Room']) }}">
                            <span class="material-symbols-outlined">arrow_right</span>Lớp Học
                        </a>
                    </li>
                    @endif
                    @if (Auth::check() && Auth::user()->hasPermission('Department_viewAny'))
                    <li>
                        <a href="{{ route('admin.departments.index', ['type' => 'Department']) }}">
                            <span class="material-symbols-outlined">arrow_right</span>Bộ Môn
                        </a>
                    </li>
                    @endif
                    @if (Auth::check() && Auth::user()->hasPermission('Nest_viewAny'))
                    <li>
                        <a href="{{ route('admin.nests.index', ['type' => 'Nest']) }}">
                            <span class="material-symbols-outlined">arrow_right</span>Tổ
                        </a>
                    </li>
                    @endif
                    @if (Auth::check() && Auth::user()->hasPermission('BorrowPurpose_viewAny'))
                    <li>
                        <a href="{{ route('admin.borrow-purposes.index', ['type' => 'BorrowPurpose']) }}">
                            <span class="material-symbols-outlined">arrow_right</span>Mục Đích Phiếu Mượn
                        </a>
                    </li>
                    @endif
                    @if(version_compare(env('SYSTEM_VERSION', '1.0'), '2.4', '>='))
                        @if (Auth::check() && Auth::user()->hasPermission('Document_viewAny'))
                        <li>
                            <a href="{{ route('admin.documents.index', ['type' => 'Document']) }}">
                                <span class="material-symbols-outlined">arrow_right</span>Văn Bản Thiết Bị
                            </a>
                        </li>
                        @endif
                    @endif

                    @if(\App\Models\Option::get_option_name('app_verison') >= '2.6')
                        @if (Auth::check() && Auth::user()->hasPermission('InventoryAudit'))
                        <li>
                            <a href="{{ route('admin.inventory_audits.index') }}">
                                <span class="material-symbols-outlined">arrow_right</span>Phiếu Kiểm Kê
                            </a>
                        </li>
                        @endif
                    @endif

                    @if(\App\Models\Option::get_option_name('app_verison') >= '2.7')
                        @if (Auth::check() && Auth::user()->hasPermission('Curriculum'))
                        <li>
                            <a href="{{ route('admin.curricula.index') }}">
                                <span class="material-symbols-outlined">arrow_right</span>Phân Phối Chương Trình
                            </a>
                        </li>
                        @endif
                    @endif

                </ul>
            </li>
            @endif
            @if( Auth::check() && Auth::user()->hasPermission( 'User_viewAny' ) || Auth::check() && Auth::user()->hasPermission(
            'Group_viewAny' ))
            <li>
                <a class="has-arrow" aria-expanded="false" href="javascript:;">
                    <div class="parent-icon">
                        <span class="material-symbols-outlined">account_circle</span>
                    </div>
                    <div class="menu-title">Tài Khoản</div>
                </a>
                <ul class="mm-collapse">
                    @if (Auth::check() && Auth::user()->hasPermission('User_viewAny'))
                    <li>
                        <a href="{{ route('admin.users.index') }}">
                            <span class="material-symbols-outlined">arrow_right</span>Người Dùng
                        </a>
                    </li>
                    @endif
                    @if (Auth::check() && Auth::user()->hasPermission('Group_viewAny'))
                    <a href="{{ route('admin.groups.index') }}">
                        <span class="material-symbols-outlined">arrow_right</span>Nhóm Người Dùng
                    </a>
                    @endif
                </ul>
            </li>
            @endif
            @if(auth()->user()->CanManagerImport())
            <li>
                <a class="has-arrow" href="javascript:;">
                    <div class="parent-icon">
                        <span class="material-symbols-outlined">swipe_up</span>
                    </div>
                    <div class="menu-title">Nhập Dữ Liệu</div>
                </a>
                <ul class="mm-collapse">
                    @if (Auth::check() && Auth::user()->hasPermission('Import_Nest'))
                    <li>
                        <a href="{{ route('admin.import.index', ['type' => 'Nest']) }}">
                            <span class="material-symbols-outlined">arrow_right</span>Tổ
                        </a>
                    </li>
                    @endif
                    @if (Auth::check() && Auth::user()->hasPermission('Import_Department'))
                    <li>
                        <a href="{{ route('admin.import.index', ['type' => 'Department']) }}">
                            <span class="material-symbols-outlined">arrow_right</span>Bộ Môn
                        </a>
                    </li>
                    @endif
                    @if (Auth::check() && Auth::user()->hasPermission('Import_Room'))
                    <li>
                        <a href="{{ route('admin.import.index', ['type' => 'Room']) }}">
                            <span class="material-symbols-outlined">arrow_right</span>Lớp Học
                        </a>
                    </li>
                    @endif
                    @if (Auth::check() && Auth::user()->hasPermission('Import_DeviceType'))
                    <li>
                        <a href="{{ route('admin.import.index', ['type' => 'DeviceType']) }}">
                            <span class="material-symbols-outlined">arrow_right</span>Nhóm Thiết Bị
                        </a>
                    </li>
                    @endif
                    @if (Auth::check() && Auth::user()->hasPermission('Import_Lab'))
                    <li>
                        <a href="{{ route('admin.import.index', ['type' => 'Lab']) }}">
                            <span class="material-symbols-outlined">arrow_right</span>Phòng Bộ Môn
                        </a>
                    </li>
                    @endif
                    @if (Auth::check() && Auth::user()->hasPermission('Import_User'))
                    <li>
                        <a href="{{ route('admin.import.index', ['type' => 'User']) }}">
                            <span class="material-symbols-outlined">arrow_right</span>Giáo Viên
                        </a>
                    </li>
                    @endif
                    @if (Auth::check() && Auth::user()->hasPermission('Import_Asset'))
                    <li>
                        <a href="{{ route('admin.import.index', ['type' => 'Asset']) }}">
                            <span class="material-symbols-outlined">arrow_right</span>Tài Sản
                        </a>
                    </li>
                    @endif
                    @if (Auth::check() && Auth::user()->hasPermission('Import_Device'))
                    <li>
                        <a href="{{ route('admin.import.index', ['type' => 'Device']) }}">
                            <span class="material-symbols-outlined">arrow_right</span>Thiết Bị
                        </a>
                    </li>
                    @endif
                    @if (Auth::check() && Auth::user()->hasPermission('Import_Curriculum'))
                    <li>
                        <a href="{{ route('admin.import.index', ['type' => 'Curriculum']) }}">
                            <span class="material-symbols-outlined">arrow_right</span>Phân Phối C.Trình
                        </a>
                    </li>
                    @endif
                </ul>
            </li>
            @endif
            @if(auth()->user()->CanManagerExport())
            <li>
                <a class="has-arrow" href="javascript:;">
                    <div class="parent-icon">
                        <span class="material-symbols-outlined">swipe_down</span>
                    </div>
                    <div class="menu-title">Xuất Dữ Liệu</div>
                </a>
                <ul class="mm-collapse">
                    @if (Auth::check() && Auth::user()->hasPermission('Export_BorrowDevicesNest'))
                    <li>
                        <a href="{{ route('admin.export.index', ['type' => 'BorrowDevicesNest']) }}"
                            title="Sổ mượn thiết bị theo tổ">
                            <span class="material-symbols-outlined">arrow_right</span>Sổ Mượn TB Theo Tổ
                        </a>
                    </li>
                    @endif
                    @if (Auth::check() && Auth::user()->hasPermission('Export_BorrowDevicesUser'))
                    <li>
                        <a href="{{ route('admin.export.index', ['type' => 'BorrowDevicesUser']) }}">
                            <span class="material-symbols-outlined">arrow_right</span>Sổ Mượn Giáo Viên
                        </a>
                    </li>
                    @endif
                    @if (Auth::check() && Auth::user()->hasPermission('Export_BorrowLab'))
                    <li>
                        <a href="{{ route('admin.export.index', ['type' => 'BorrowLab']) }}">
                            <span class="material-symbols-outlined">arrow_right</span>Sổ Mượn P. Bộ Môn
                        </a>
                    </li>
                    @endif
                    @if (Auth::check() && Auth::user()->hasPermission('Export_BorrowDevice'))
                    <li>
                        <a href="{{ route('admin.export.index', ['type' => 'BorrowDevice']) }}"
                            title="Báo cáo mượn theo mục đích mượn">
                            <span class="material-symbols-outlined">arrow_right</span>BC Mượn theo MĐSD
                        </a>
                    </li>
                    @endif
                    <li>
                        <a href="{{ route('admin.export.index', ['type' => 'BorrowLabs']) }}"
                            title="Báo cáo mượn theo phòng bộ môn">
                            <span class="material-symbols-outlined">arrow_right</span>BC Mượn theo PBM
                        </a>
                    </li>
                    @if (Auth::check() && Auth::user()->hasPermission('Export_BorrowDetail'))
                    <li>
                        <a href="{{ route('admin.export.index', ['type' => 'BorrowDetail']) }}">
                            <span class="material-symbols-outlined">arrow_right</span>Phiếu Báo Mượn
                        </a>
                    </li>
                    @endif

                    @if (Auth::check() && Auth::user()->hasPermission('InventoryAuditCombined') && \App\Models\Option::get_option_name('app_verison') >= '2.6')
                    <li>
                        <a href="{{ route('admin.export.index', ['type' => 'InventoryAuditCombined']) }}">
                            <span class="material-symbols-outlined">arrow_right</span>Sổ Theo Dõi Thiết Bị
                        </a>
                    </li>
                    @endif

                   
                    @if (Auth::check() && Auth::user()->hasPermission('Export_Device'))
                    <li>
                        <a href="{{ route('admin.export.index', ['type' => 'Device']) }}">
                            <span class="material-symbols-outlined">arrow_right</span>Thiết Bị
                        </a>
                    </li>
                    @endif
                    @if (Auth::check() && Auth::user()->hasPermission('Export_Asset'))
                    <li>
                        <a href="{{ route('admin.export.index', ['type' => 'Asset']) }}">
                            <span class="material-symbols-outlined">arrow_right</span>Tài Sản
                        </a>
                    </li>
                    @endif
                    @if (Auth::check() && Auth::user()->hasPermission('Export_Lab'))
                    <li>
                        <a href="{{ route('admin.export.index', ['type' => 'Lab']) }}">
                            <span class="material-symbols-outlined">arrow_right</span>Phòng bộ Môn
                        </a>
                    </li>
                    @endif
                    @if (Auth::check() && Auth::user()->hasPermission('Export_DeviceType'))
                    <li>
                        <a href="{{ route('admin.export.index', ['type' => 'DeviceType']) }}">
                            <span class="material-symbols-outlined">arrow_right</span>Nhóm Thiết Bị
                        </a>
                    </li>
                    @endif
                    @if (Auth::check() && Auth::user()->hasPermission('Export_Room'))
                    <li>
                        <a href="{{ route('admin.export.index', ['type' => 'Room']) }}">
                            <span class="material-symbols-outlined">arrow_right</span>Lớp Học
                        </a>
                    </li>
                    @endif
                    @if (Auth::check() && Auth::user()->hasPermission('Export_Department'))
                    <li>
                        <a href="{{ route('admin.export.index', ['type' => 'Department']) }}">
                            <span class="material-symbols-outlined">arrow_right</span>Bộ Môn
                        </a>
                    </li>
                    @endif
                    @if (Auth::check() && Auth::user()->hasPermission('Export_Nest'))
                    <li>
                        <a href="{{ route('admin.export.index', ['type' => 'Nest']) }}">
                            <span class="material-symbols-outlined">arrow_right</span>Tổ
                        </a>
                    </li>
                    @endif
                </ul>
            </li>
            @endif
            <li>
                <a class="has-arrow" href="javascript:;">
                    <div class="parent-icon">
                        <span class="material-symbols-outlined">settings_applications</span>
                    </div>
                    <div class="menu-title">Hệ Thống</div>
                </a>
                <ul class="mm-collapse">
                    @if (Auth::check() && Auth::user()->hasPermission('System_Option'))
                    <li>
                        <a href="{{ route('admin.options.index', ['type' => 'general']) }}">
                            <span class="material-symbols-outlined">arrow_right</span>Cấu Hình
                        </a>
                    </li>
                    @endif
                    @if (Auth::check() && Auth::user()->hasPermission('System_Update'))
                    <li>
                        <a href="{{ route('admin.system.update') }}">
                            <span class="material-symbols-outlined">arrow_right</span>Cập Nhật
                        </a>
                    </li>
                    @endif
                    <li>
                        <a target="_blank" href="https://huongdan.quanlythietbitruonghoc.com/">
                            <span class="material-symbols-outlined">arrow_right</span>Hướng Dẫn
                        </a>
                    </li>
                </ul>
            </li>
        </ul>
    </div>
    <div class="sidebar-bottom dropdown dropup-center dropup">
        <div class="dropdown-toggle d-flex align-items-center px-3 gap-3 w-100 h-100" data-bs-toggle="dropdown">
            <div class="user-info">
                <h5 class="mb-0 user-name">{{ Auth::user()->name }}</h5>
            </div>
        </div>
        <ul class="dropdown-menu dropdown-menu-end">
            <li>
                <a class="dropdown-item" href="/">
                    <span class="material-symbols-outlined me-2"></span>
                    <span>Thoát Quản trị</span>
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
<!--end sidebar-->
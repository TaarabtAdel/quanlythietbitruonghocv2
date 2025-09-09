<?php

use Illuminate\Support\Facades\Route;

// Admin Controllers
use App\Http\Controllers\Admin\AssetController;
use App\Http\Controllers\Admin\BorrowController;
use App\Http\Controllers\Admin\BorrowDeviceController;
use App\Http\Controllers\Admin\BorrowPurposeController;
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\DeviceController;
use App\Http\Controllers\Admin\DeviceTypeController;
use App\Http\Controllers\Admin\DocumentController;
use App\Http\Controllers\Admin\GroupController;
use App\Http\Controllers\Admin\GroupsRoleController;
use App\Http\Controllers\Admin\LabController;
use App\Http\Controllers\Admin\MigrationController;
use App\Http\Controllers\Admin\NestController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\OptionController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\RoomController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\HomeController;
use App\Http\Controllers\Admin\ImportController;
use App\Http\Controllers\Admin\ExportController;
use App\Http\Controllers\Admin\SystemController;

// Teacher Controllers
use App\Http\Controllers\Teacher\AssetController as TeacherAssetController;
use App\Http\Controllers\Teacher\BorrowController as TeacherBorrowController;
use App\Http\Controllers\Teacher\BorrowDeviceController as TeacherBorrowDeviceController;
use App\Http\Controllers\Teacher\BorrowPurposeController as TeacherBorrowPurposeController;
use App\Http\Controllers\Teacher\DepartmentController as TeacherDepartmentController;
use App\Http\Controllers\Teacher\DeviceController as TeacherDeviceController;
use App\Http\Controllers\Teacher\DeviceTypeController as TeacherDeviceTypeController;
use App\Http\Controllers\Teacher\DocumentController as TeacherDocumentController;
use App\Http\Controllers\Teacher\GroupController as TeacherGroupController;
use App\Http\Controllers\Teacher\GroupsRoleController as TeacherGroupsRoleController;
use App\Http\Controllers\Teacher\LabController as TeacherLabController;
use App\Http\Controllers\Teacher\MigrationController as TeacherMigrationController;
use App\Http\Controllers\Teacher\NestController as TeacherNestController;
use App\Http\Controllers\Teacher\NotificationController as TeacherNotificationController;
use App\Http\Controllers\Teacher\OptionController as TeacherOptionController;
use App\Http\Controllers\Teacher\RoleController as TeacherRoleController;
use App\Http\Controllers\Teacher\RoomController as TeacherRoomController;
use App\Http\Controllers\Teacher\UserController as TeacherUserController;
use App\Http\Controllers\Teacher\HomeController as TeacherHomeController;
use App\Http\Controllers\Teacher\BorrowLabController as TeacherBorrowLabController;

// Routes for Admin Authentication
Route::get('/admin/login', [App\Http\Controllers\Admin\AuthController::class, 'login'])->name('admin.login');
Route::post('/admin/login', [App\Http\Controllers\Admin\AuthController::class, 'authenticate'])->name('admin.authenticate');
Route::post('/admin/logout', [App\Http\Controllers\Admin\AuthController::class, 'logout'])->name('admin.logout');

// Routes for Teacher Authentication
Route::get('/login', [App\Http\Controllers\Teacher\AuthController::class, 'login'])->name('login');
Route::post('/teacher/login', [App\Http\Controllers\Teacher\AuthController::class, 'authenticate'])->name('auth.postLogin');
Route::get('/teacher/logout', [App\Http\Controllers\Teacher\AuthController::class, 'logout'])->name('auth.logout');

Route::middleware(['auth'])->prefix('admin')->as('admin.')->group(function () {
    // Trang chủ quản trị viên
    Route::get('/', [HomeController::class, 'index'])->name('home');

    Route::get('/borrows/devices', [BorrowDeviceController::class, 'index'])->name('borrows.devices');
    Route::get('/borrows/labs', [DeviceController::class, 'index'])->name('borrows.labs');
    Route::resource('borrows', BorrowController::class);
    
    Route::get('/import', [ImportController::class, 'index'])->name('import.index');
    Route::get('/export', [ExportController::class, 'index'])->name('export.index');

    Route::resource('assets', AssetController::class);
    // Route::resource('borrow-devices', BorrowDeviceController::class);
    Route::resource('borrow-purposes', BorrowPurposeController::class);
    Route::resource('departments', DepartmentController::class);
    Route::resource('devices', DeviceController::class);
    Route::resource('device-types', DeviceTypeController::class);
    Route::resource('documents', DocumentController::class);
    Route::resource('groups', GroupController::class);
    // Route::resource('groups-roles', GroupsRoleController::class);
    Route::resource('labs', LabController::class);
    // Route::resource('migrations', MigrationController::class);
    Route::resource('nests', NestController::class);
    // Route::resource('notifications', NotificationController::class);
    Route::resource('options', OptionController::class);
    // Route::resource('roles', RoleController::class);
    Route::resource('rooms', RoomController::class);
    Route::resource('users', UserController::class);

    Route::get('/system/update', [SystemController::class, 'index'])->name('system.update');
});

Route::middleware(['auth'])->group(function () {
    // Trang chủ giáo viên
    Route::get('/', [TeacherHomeController::class, 'index'])->name('home');
    // Danh sách thiết bị
    Route::get('/devices', [TeacherDeviceController::class, 'index'])->name('devices.index');
    // Phòng mượn
    Route::get('/borrows/labs', [TeacherBorrowLabController::class, 'index'])->name('borrows.labs');
    // Phiếu mượn
    Route::get('/borrows/copy/{id}', [TeacherBorrowController::class,'copy'])->name('borrows.copy');
    Route::resource('borrows', TeacherBorrowController::class);
    // Văn bản thiết bị
    Route::resource('documents', TeacherDocumentController::class);
    // Danh sách phòng bộ môn
    Route::resource('labs', TeacherLabController::class);
    Route::resource('users', TeacherUserController::class);
});


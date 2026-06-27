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
use App\Http\Controllers\Admin\BorrowLabController;
use App\Http\Controllers\Admin\UpdateController;
use App\Http\Controllers\Admin\InventoryAuditController;
use App\Http\Controllers\Admin\CurriculumController;

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
use App\Http\Controllers\Teacher\LabController as TeacherLabController;
use App\Http\Controllers\Teacher\NestController as TeacherNestController;
use App\Http\Controllers\Teacher\RoomController as TeacherRoomController;
use App\Http\Controllers\Teacher\UserController as TeacherUserController;
use App\Http\Controllers\Teacher\HomeController as TeacherHomeController;
use App\Http\Controllers\Teacher\BorrowLabController as TeacherBorrowLabController;
use App\Http\Controllers\Teacher\CurriculumController as TeacherCurriculumController;

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
    // Phiếu mượn
    Route::resource('borrows', BorrowController::class);
    // Thiết bị mượn
    Route::get('/borrows-devices', [BorrowDeviceController::class, 'index'])->name('borrows.devices');
    // Phòng mượn
    Route::get('/borrows-labs', [BorrowLabController::class, 'index'])->name('borrows.labs');
    
    Route::get('/import', [ImportController::class, 'index'])->name('import.index');
    Route::post('/import/store', [ImportController::class, 'store'])->name('import.store');

    Route::get('/export', [ExportController::class, 'index'])->name('export.index');
    Route::post('/export/store', [ExportController::class, 'store'])->name('export.store');
    Route::match(['get', 'post'], 'export/store', [ExportController::class, 'store'])->name('export.store');

    // Cấu hình
    Route::get('/options', [OptionController::class, 'index'])->name('options.index');
    Route::post('/options/update', [OptionController::class, 'update'])->name('options.update');

    Route::resource('assets', AssetController::class);
    Route::resource('borrow-purposes', BorrowPurposeController::class);
    Route::resource('departments', DepartmentController::class);

    Route::post('bulkAction',[DeviceController::class,'bulkAction'])->name('devices.bulkAction');
    Route::resource('devices', DeviceController::class);

    Route::resource('device-types', DeviceTypeController::class);
    Route::get('documents/sgd/{sgdDocument}', [DocumentController::class, 'sgdShow'])->name('documents.sgd-show')->whereNumber('sgdDocument');
    Route::resource('documents', DocumentController::class);
    
    // export
    Route::get('inventory_audits/{id}/export', [InventoryAuditController::class, 'export'])->name('inventory_audits.export');
    Route::get('inventory_audits/{id}/copy', [InventoryAuditController::class, 'copy'])->name('inventory_audits.copy');
    Route::resource('inventory_audits', InventoryAuditController::class);

    Route::resource('groups', GroupController::class);
    Route::resource('labs', LabController::class);
    Route::resource('nests', NestController::class);
    Route::resource('rooms', RoomController::class);
    Route::resource('users', UserController::class);

    Route::get('curricula/{id}/copy', [CurriculumController::class, 'copy'])->name('curricula.copy');
    Route::resource('curricula', CurriculumController::class);

    Route::get('/system/update', [UpdateController::class, 'index'])->name('system.update');
    Route::post('/system/doUpdate', [UpdateController::class, 'doUpdate'])->name('system.doUpdate');
});

Route::middleware(['auth'])->group(function () {
    // 1. Trang chủ giáo viên
    Route::get('/', [TeacherHomeController::class, 'index'])->name('home');

    // 3. Phiếu mượn
    Route::get('/borrows/copy/{id}', [TeacherBorrowController::class,'copy'])->name('borrows.copy');
   
    // 4. Lịch sử dụng phòng
    Route::get('/borrows/labs', [TeacherBorrowLabController::class, 'index'])->name('borrows.labs');

    // 2. Tạo phiếu mượn + Phiếu mượn
    Route::post('/borrows/saveFakeDevices/{id}', [TeacherBorrowController::class, 'saveFakeDevices'])->name('borrows.saveFakeDevices');
    Route::get('/borrows/getFakeDevices', [TeacherBorrowController::class, 'getFakeDevices'])->name('borrows.getFakeDevices');
    Route::post('/borrows/delete_fake_device', [TeacherBorrowController::class, 'delete_fake_device'])->name('borrows.delete_fake_device');
    Route::post('/borrows/update_qty_fake_device', [TeacherBorrowController::class, 'update_qty_fake_device'])->name('borrows.update_qty_fake_device');


    Route::resource('borrows', TeacherBorrowController::class);

    // 5. Văn bản thiết bị
    Route::resource('documents', TeacherDocumentController::class);

    // 6. Danh sách phòng bộ môn
    Route::get('labs', [TeacherLabController::class, 'index'])->name('labs.index');

    // 7. Danh sách thiết bị
    Route::get('/devices', [TeacherDeviceController::class, 'index'])->name('devices.index');

    // 8. Mục đích mượn
    Route::get('borrow-purposes', [TeacherBorrowPurposeController::class, 'index'])->name('borrow-purposes.index');

    // 9. Lớp học
    Route::get('rooms', [TeacherRoomController::class, 'index'])->name('rooms.index');
    // 10. Giáo viên
    Route::get('users', [TeacherUserController::class, 'index'])->name('users.index');

    // Tài khoản
    Route::get('profile', [TeacherUserController::class, 'profile'])->name('users.profile');
    Route::get('profileEdit', [TeacherUserController::class, 'profileEdit'])->name('users.profileEdit');
    Route::post('postProfileEdit', [TeacherUserController::class, 'postProfileEdit'])->name('users.postProfileEdit');

    // Chương trình đào tạo
    Route::get('curricula/get-lessons', [TeacherCurriculumController::class, 'getLessonsByFilters'])->name('curricula.get_lessons');
    Route::get('curricula', [TeacherCurriculumController::class, 'index'])->name('curricula.index');
    Route::get('curricula/{id}', [TeacherCurriculumController::class, 'show'])->name('curricula.show');
});


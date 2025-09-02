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

// Routes for Admin Authentication
Route::get('/admin/login', [App\Http\Controllers\Admin\AuthController::class, 'login'])->name('admin.login');
Route::post('/admin/login', [App\Http\Controllers\Admin\AuthController::class, 'authenticate'])->name('admin.authenticate');
Route::post('/admin/logout', [App\Http\Controllers\Admin\AuthController::class, 'logout'])->name('admin.logout');

// Routes for Teacher Authentication
Route::get('/login', [App\Http\Controllers\Teacher\AuthController::class, 'login'])->name('login');
Route::post('/teacher/login', [App\Http\Controllers\Teacher\AuthController::class, 'authenticate'])->name('auth.postLogin');
Route::post('/teacher/logout', [App\Http\Controllers\Teacher\AuthController::class, 'logout'])->name('auth.postLogout');

Route::middleware(['auth'])->prefix('admin')->group(function () {
    Route::resource('assets', AssetController::class);
    Route::resource('borrows', BorrowController::class);
    Route::resource('borrow-devices', BorrowDeviceController::class);
    Route::resource('borrow-purposes', BorrowPurposeController::class);
    Route::resource('departments', DepartmentController::class);
    Route::resource('devices', DeviceController::class);
    Route::resource('device-types', DeviceTypeController::class);
    Route::resource('documents', DocumentController::class);
    Route::resource('groups', GroupController::class);
    Route::resource('groups-roles', GroupsRoleController::class);
    Route::resource('labs', LabController::class);
    Route::resource('migrations', MigrationController::class);
    Route::resource('nests', NestController::class);
    Route::resource('notifications', NotificationController::class);
    Route::resource('options', OptionController::class);
    Route::resource('roles', RoleController::class);
    Route::resource('rooms', RoomController::class);
    Route::resource('users', UserController::class);
});

Route::middleware(['auth'])->group(function () {
    Route::get('/', [TeacherHomeController::class, 'index'])->name('teacher.home');
});
Route::middleware(['auth'])->prefix('teacher')->group(function () {
    Route::resource('assets', TeacherAssetController::class);
    Route::resource('borrows', TeacherBorrowController::class);
    Route::resource('borrow-devices', TeacherBorrowDeviceController::class);
    Route::resource('borrow-purposes', TeacherBorrowPurposeController::class);
    Route::resource('departments', TeacherDepartmentController::class);
    Route::resource('devices', TeacherDeviceController::class);
    Route::resource('device-types', TeacherDeviceTypeController::class);
    Route::resource('documents', TeacherDocumentController::class);
    Route::resource('groups', TeacherGroupController::class);
    Route::resource('labs', TeacherLabController::class);
    Route::resource('nests', TeacherNestController::class);
    Route::resource('notifications', TeacherNotificationController::class);
    Route::resource('options', TeacherOptionController::class);
    Route::resource('roles', TeacherRoleController::class);
    Route::resource('rooms', TeacherRoomController::class);
    Route::resource('users', TeacherUserController::class);
});


<?php

use App\Http\Controllers\Api\Teacher\AuthController;
use App\Http\Controllers\Api\Teacher\BorrowController;
use App\Http\Controllers\Api\Teacher\BorrowLabController;
use App\Http\Controllers\Api\Teacher\ConfigController;
use App\Http\Controllers\Api\Teacher\CurriculumController;
use App\Http\Controllers\Api\Teacher\DeviceController;
use App\Http\Controllers\Api\Teacher\DocumentController;
use App\Http\Controllers\Api\Teacher\FakeDeviceController;
use App\Http\Controllers\Api\Teacher\LabController;
use App\Http\Controllers\Api\Teacher\DashboardController;
use App\Http\Controllers\Api\Teacher\RoomController;
use App\Http\Controllers\Api\Teacher\UserController;
use App\Http\Middleware\AuthenticateTeacherApi;
use App\Http\Middleware\AuthenticateFederationApi;
use App\Http\Controllers\Api\Federation\MetaController as FederationMetaController;
use App\Http\Controllers\Api\Federation\DeviceController as FederationDeviceController;
use App\Http\Controllers\Api\Federation\BorrowController as FederationBorrowController;
use App\Http\Controllers\Api\Federation\LabController as FederationLabController;
use App\Http\Controllers\Api\Federation\RoomController as FederationRoomController;
use App\Http\Controllers\Api\Federation\UserController as FederationUserController;
use App\Http\Controllers\Api\Federation\InventoryAuditController as FederationInventoryAuditController;
use App\Http\Controllers\Api\Federation\NestController as FederationNestController;
use App\Http\Controllers\Api\Federation\BorrowDeviceController as FederationBorrowDeviceController;
use App\Http\Controllers\Api\Federation\DocumentController as FederationDocumentController;
use App\Http\Controllers\Api\Federation\CurriculumController as FederationCurriculumController;
use Illuminate\Support\Facades\Route;

Route::prefix('federation')->middleware(AuthenticateFederationApi::class)->group(function () {
    Route::get('meta', [FederationMetaController::class, 'index']);
    Route::get('stats', [FederationMetaController::class, 'stats']);
    Route::get('devices', [FederationDeviceController::class, 'index']);
    Route::get('borrows', [FederationBorrowController::class, 'index']);
    Route::get('borrows/filters', [FederationBorrowController::class, 'filters']);
    Route::get('borrows/{id}', [FederationBorrowController::class, 'show'])->whereNumber('id');
    Route::get('labs', [FederationLabController::class, 'index']);
    Route::get('rooms', [FederationRoomController::class, 'index']);
    Route::get('users', [FederationUserController::class, 'index']);
    Route::get('nests', [FederationNestController::class, 'index']);
    Route::get('borrow-devices/ledger', [FederationBorrowDeviceController::class, 'ledger']);
    Route::get('borrow-devices/filters', [FederationBorrowDeviceController::class, 'filters']);
    Route::get('inventory-audits', [FederationInventoryAuditController::class, 'index']);
    Route::get('inventory-audits/{id}', [FederationInventoryAuditController::class, 'show'])->whereNumber('id');
    Route::get('documents', [FederationDocumentController::class, 'index']);
    Route::get('documents/{id}', [FederationDocumentController::class, 'show'])->whereNumber('id');
    Route::get('curricula', [FederationCurriculumController::class, 'index']);
    Route::get('curricula/{id}', [FederationCurriculumController::class, 'show'])->whereNumber('id');
});

Route::prefix('teacher')->group(function () {
    Route::post('login', [AuthController::class, 'login']);

    Route::middleware(AuthenticateTeacherApi::class)->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('me', [AuthController::class, 'me']);

        Route::get('config', [ConfigController::class, 'index']);
        Route::get('departments', [ConfigController::class, 'departments']);
        Route::get('device-types', [ConfigController::class, 'deviceTypes']);
        Route::get('school-years', [ConfigController::class, 'schoolYears']);
        Route::get('school-weeks', [ConfigController::class, 'schoolWeeks']);
        Route::get('borrow-purposes', [ConfigController::class, 'borrowPurposes']);
        Route::get('nests', [ConfigController::class, 'nests']);
        Route::get('groups', [ConfigController::class, 'groups']);
        Route::get('grades', [ConfigController::class, 'grades']);

        Route::get('dashboard', [DashboardController::class, 'index']);

        Route::get('borrows', [BorrowController::class, 'index']);
        Route::post('borrows', [BorrowController::class, 'store']);
        Route::get('borrows/{id}', [BorrowController::class, 'show'])->whereNumber('id');
        Route::get('borrows/{id}/form-data', [BorrowController::class, 'formData'])->whereNumber('id');
        Route::put('borrows/{id}', [BorrowController::class, 'update'])->whereNumber('id');
        Route::delete('borrows/{id}', [BorrowController::class, 'destroy'])->whereNumber('id');
        Route::post('borrows/{id}/copy', [BorrowController::class, 'copy'])->whereNumber('id');

        Route::get('borrows/{borrowId}/fake-devices', [FakeDeviceController::class, 'index'])->whereNumber('borrowId');
        Route::post('borrows/{borrowId}/fake-devices', [FakeDeviceController::class, 'store'])->whereNumber('borrowId');
        Route::patch('fake-devices/{id}', [FakeDeviceController::class, 'update'])->whereNumber('id');
        Route::delete('fake-devices/{id}', [FakeDeviceController::class, 'destroy'])->whereNumber('id');

        Route::get('borrow-labs', [BorrowLabController::class, 'index']);
        Route::get('borrow-labs/summary', [BorrowLabController::class, 'summary']);
        Route::get('borrow-labs/{labId}', [BorrowLabController::class, 'show'])->whereNumber('labId');

        Route::get('documents', [DocumentController::class, 'index']);
        Route::get('documents/{id}', [DocumentController::class, 'show'])->whereNumber('id');

        Route::get('curricula/lessons', [CurriculumController::class, 'lessons']);
        Route::get('curricula', [CurriculumController::class, 'index']);
        Route::get('curricula/{id}', [CurriculumController::class, 'show'])->whereNumber('id');

        Route::get('devices', [DeviceController::class, 'index']);
        Route::get('labs', [LabController::class, 'index']);
        Route::get('rooms', [RoomController::class, 'index']);

        Route::get('users', [UserController::class, 'index']);
        Route::get('profile', [UserController::class, 'profile']);
        Route::put('profile', [UserController::class, 'updateProfile']);
    });
});

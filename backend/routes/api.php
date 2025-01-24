<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\LeaveRequestController;

Route::post('/login', [AuthController::class, 'login'])->name('login');

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');;

    Route::prefix('v1')->group(function () {

        Route::prefix('user')->middleware('can:admin_role')->group(function () {
            Route::post('/', [UserController::class, 'createUser'])->name('createUser');
            Route::get('/list', [UserController::class, 'listUsers'])->name('listUsers');
            Route::delete('/{id}', [UserController::class, 'deleteUser'])->name('deleteUser');
            Route::get('/{id}', [UserController::class, 'selectUser'])->name('selectUser');
            Route::put('/{id}', [UserController::class, 'updateUser'])->name('updateUser');
        });

        Route::prefix('leave-request')->group(function () {
            Route::post('/', [LeaveRequestController::class, 'createLeaveRequest'])->name('createLeaveRequest');
            Route::get('/', [LeaveRequestController::class, 'listUserLeaveRequests'])->name('listUserLeaveRequests');
            Route::middleware('can:admin_role')->group(function () {
                Route::get('/list', [LeaveRequestController::class, 'listLeaveRequests'])->name('listLeaveRequests');
                Route::put('/approve-request/{id}', [LeaveRequestController::class, 'updateLeaveRequest'])->name('approveLeaveRequest');
                Route::put('/reject-request/{id}', [LeaveRequestController::class, 'updateLeaveRequest'])->name('rejectLeaveRequest');
            });
        });
    });
});



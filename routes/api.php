<?php

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:api');
    Route::get('me', [AuthController::class, 'me'])->middleware('auth:api');
    Route::get('/check-auth', function (Request $request) {
        return response()->json(['message' => 'Authenticated'], 200);
    });
});

// Phân quyền
Route::middleware(['auth:api', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', function () {
        return response()->json(['message' => 'Welcome Admin!']);
    });
});

Route::middleware(['auth:api', 'role:user'])->group(function () {
    Route::get('/user/dashboard', function () {
        return response()->json(['message' => 'Welcome User!']);
    });
});


// CRUD User
Route::middleware('auth:api')->group(function () {
    Route::prefix('admin')->middleware('isAdmin')->group(function () {
        Route::get('users', [UserController::class, 'index']); // Lấy danh sách người dùng
        Route::get('users/{id}', [UserController::class, 'show']); // Lấy thông tin người dùng
        Route::post('users', [UserController::class, 'store']); // Thêm mới người dùng
        // Route::put('users/{id}', [UserController::class, 'update']); // Cập nhật thông tin người dùng
        Route::put('users/update', [UserController::class, 'updateByEmail']); // Cập nhật thông tin người dùng
        Route::put('users/role/{id}', [UserController::class, 'updateRole']); // Cập nhật thông tin người dùng
        Route::delete('users/{id}', [UserController::class, 'destroy']); // Xóa người dùng
    });
});
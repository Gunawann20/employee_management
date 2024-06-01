<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\RoleAndPermissionController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);

Route::group([
    "middleware" => ['auth:api'],
    "prefix" => 'employee'
], function (){
    Route::post('/create', [EmployeeController::class, 'create_employee']);
});

Route::group([
    'middleware' => ['auth:api', 'role:super-admin'],
    'prefix' => 'admin'
], function (){
    Route::post('/add-role', [RoleAndPermissionController::class, 'create_role']);
    Route::post('/give-role', [RoleAndPermissionController::class, 'give_role']);
    Route::post('/add-permission', [RoleAndPermissionController::class, 'create_permission']);
});

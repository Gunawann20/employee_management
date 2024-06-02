<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\RoleAndPermissionController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/forgot-password', [AuthController::class, 'forget_password']);
Route::post('/reset-password', [AuthController::class, 'submit_reset_password']);

Route::group([
    "middleware" => ['auth:api'],
    "prefix" => 'employee'
], function (){
    Route::post('/create', [EmployeeController::class, 'create_employee'])->middleware(['permission:employee-insert']);
    Route::get('/get-all', [EmployeeController::class, 'list_employee'])->middleware(['permission:employee-list']);
    Route::get('/get', [EmployeeController::class, 'get_employee'])->middleware(['permission:employee-get']);
    Route::get('/get/{userId}', [EmployeeController::class, 'get_by_id'])->middleware(['permission:employee-edit-by-id']);
    Route::post('/update/{userId}', [EmployeeController::class, 'update_employee_by_id'])->middleware(['permission:employee-edit-by-id']);
    Route::post('/update', [EmployeeController::class, 'update_employee'])->middleware(['permission:employee-edit']);
    Route::get('/delete/{id}', [EmployeeController::class, 'delete_employee'])->middleware('permission:employee-delete');
});

Route::group([
    'middleware' => ['auth:api', 'role:super-admin'],
    'prefix' => 'admin'
], function (){
    Route::post('/give-role', [RoleAndPermissionController::class, 'give_role']);
    Route::post('/give-permissions', [RoleAndPermissionController::class, 'give_permissions']);
    Route::get('/get-all-role', [RoleAndPermissionController::class, 'get_all_role']);
    Route::get('/get-all-permission', [RoleAndPermissionController::class, 'get_all_permissions']);
    Route::get('/get-permission-by-role', [RoleAndPermissionController::class, 'get_permissions_by_role']);
});

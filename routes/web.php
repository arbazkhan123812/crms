<?php

use App\Http\Controllers\Admin\AttendanceController;
use App\Http\Controllers\Admin\CompanyController;
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\DesignationController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\EmployeeSalaryController;
use App\Http\Controllers\Admin\HolidayController;
use App\Http\Controllers\Admin\LeaveController;
use App\Http\Controllers\Admin\LeaveTypeController;
use App\Http\Controllers\Admin\PayrollController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\RecruitmentController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SalaryComponentController;
use App\Http\Controllers\Admin\SalaryTemplateController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Employee\DashboardController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::get('/admin/users/index', [UserController::class, 'index']);
Route::post('UserController', [UserController::class, 'save_user'])->name('user.save');
Route::post('UserController/{id}', [UserController::class, 'delete_user'])->name('user.delete');
Route::get('UserController/{id}', [UserController::class, 'get_user'])->name('user.get');

// Roles
Route::get('/admin/roles/index', [RoleController::class, 'index']);
Route::post('RoleController', [RoleController::class, 'save_role'])->name('role.save');
Route::post('RoleController/{id}', [RoleController::class, 'delete_role'])->name('role.delete');
Route::get('RoleController/{id}', [RoleController::class, 'get_role'])->name('role.get');

// Auth

Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {
 Route::get('/permissions', [PermissionController::class, 'index'])->name('permissions.index');
    Route::post('/permissions/get', [PermissionController::class, 'getPermissions'])->name('permissions.get');
    Route::post('/permissions/save', [PermissionController::class, 'savePermissions'])->name('permissions.save');
    Route::get('/permissions/all', [PermissionController::class, 'getAllPermissions'])->name('permissions.all');
        Route::get('/permissions/role-name/{roleId}', [PermissionController::class, 'getRoleName'])->name('permissions.role-name');

});



Route::get('/', [AuthController::class, 'index']);
Route::post('/Auth/login', [AuthController::class, 'process_login'])->name('auth.login.process');
Route::get('/Auth/logout', [AuthController::class, 'logout'])->name('auth.logout');
// Route::get('/Auth/testy', [AuthController::class, 'testy']);

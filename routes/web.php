<?php

use App\Http\Controllers\Admin\LeadController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AuthController;
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

    Route::get('/leads', [LeadController::class, 'index'])->name('leads.index');
    Route::post('/leads/save', [LeadController::class, 'save'])->name('lead.save');
    Route::delete('/leads/{id}', [LeadController::class, 'delete'])->name('lead.delete');
    Route::get('/leads/{id}', [LeadController::class, 'get'])->name('lead.get');
    Route::post('/leads/add-note', [LeadController::class, 'addNote'])->name('lead.addNote');

    Route::get('/permissions', [PermissionController::class, 'index'])->name('permissions.index');
    Route::post('/permissions/get', [PermissionController::class, 'getPermissions'])->name('permissions.get');
    Route::post('/permissions/save', [PermissionController::class, 'savePermissions'])->name('permissions.save');
    Route::get('/permissions/all', [PermissionController::class, 'getAllPermissions'])->name('permissions.all');
    Route::get('/permissions/role-name/{roleId}', [PermissionController::class, 'getRoleName'])->name('permissions.role-name');
    Route::get('/leads/{id}/tasks', [LeadController::class, 'getTasks'])->name('lead.tasks');
    Route::put('/tasks/{id}/status', [LeadController::class, 'updateTaskStatus'])->name('task.updateStatus');
    Route::post('/leads/add-task', [LeadController::class, 'addTask'])->name('lead.addTask');
});



Route::get('/', [AuthController::class, 'index']);
Route::post('/Auth/login', [AuthController::class, 'process_login'])->name('auth.login.process');
Route::get('/Auth/logout', [AuthController::class, 'logout'])->name('auth.logout');
// Route::get('/Auth/testy', [AuthController::class, 'testy']);

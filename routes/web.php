<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\RoleController;
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


Route::get('/', [AuthController::class, 'index']);
Route::post('/Auth/login',[AuthController::class,'process_login'])->name('auth.login.process');
Route::get('/Auth/logout',[AuthController::class,'logout'])->name('auth.logout');

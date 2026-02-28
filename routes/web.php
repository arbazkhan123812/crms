<?php

use App\Http\Controllers\Admin\CompanyController;
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\DesignationController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AuthController;
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
    // Permission management
    Route::get('/permissions', [PermissionController::class, 'index'])->name('permissions.index');
    Route::post('/permissions/get', [PermissionController::class, 'getPermissions'])->name('permissions.get');
    Route::post('/permissions/save', [PermissionController::class, 'savePermissions'])->name('permissions.save');
    Route::get('/permissions/role-name/{roleId}', [PermissionController::class, 'getRoleName'])->name('permissions.role-name');
    Route::post('/permissions/copy-role-to-user', [PermissionController::class, 'copyRoleToUser'])->name('permissions.copy-role-to-user');
    Route::resource('companies', CompanyController::class);

    // Additional company routes
     Route::get('company', [CompanyController::class, 'index'])->name('companies.index');
    Route::post('company/update', [CompanyController::class, 'update'])->name('companies.update');
    Route::get('company/edit', [CompanyController::class, 'edit'])->name('companies.edit');
    Route::post('company/upload-logo', [CompanyController::class, 'uploadLogo'])->name('companies.upload-logo');

    // ============= DEPARTMENT MODULE =============
    Route::resource('departments', DepartmentController::class);

    Route::get('departments/hierarchy/view', [DepartmentController::class, 'hierarchy'])
        ->name('departments.hierarchy');
    Route::post('departments/{department}/assign-manager', [DepartmentController::class, 'assignManager'])
        ->name('departments.assign-manager');
    Route::get('departments/{department}/employees', [DepartmentController::class, 'employees'])
        ->name('departments.employees');

    Route::resource('designations', DesignationController::class);

    // Additional designation routes
    Route::get('designations/by-department/{department}', [DesignationController::class, 'byDepartment'])
        ->name('designations.by-department');
    Route::post('designations/{designation}/update-salary-range', [DesignationController::class, 'updateSalaryRange'])
        ->name('designations.update-salary-range');

    // ============= EMPLOYEE MODULE =============
    Route::resource('employees', EmployeeController::class);

    // Additional employee routes
    Route::get('employees/import/excel', [EmployeeController::class, 'importForm'])
        ->name('employees.import.form');
    Route::post('employees/import/excel', [EmployeeController::class, 'import'])
        ->name('employees.import');
    Route::get('employees/export/excel', [EmployeeController::class, 'export'])
        ->name('employees.export');

    // Employee documents
    Route::post('employees/{employee}/documents', [EmployeeController::class, 'uploadDocument'])
        ->name('employees.documents.upload');
    Route::delete('employees/{employee}/documents/{document}', [EmployeeController::class, 'deleteDocument'])
        ->name('employees.documents.delete');

    // Employee status
    Route::post('employees/{employee}/activate', [EmployeeController::class, 'activate'])
        ->name('employees.activate');
    Route::post('employees/{employee}/deactivate', [EmployeeController::class, 'deactivate'])
        ->name('employees.deactivate');
    Route::post('employees/{employee}/terminate', [EmployeeController::class, 'terminate'])
        ->name('employees.terminate');

    // Employee reporting
    Route::get('employees/{employee}/team', [EmployeeController::class, 'team'])
        ->name('employees.team');
    Route::get('employees/{employee}/reporting-chain', [EmployeeController::class, 'reportingChain'])
        ->name('employees.reporting-chain');

    // Employee search
    Route::get('employees/search/autocomplete', [EmployeeController::class, 'searchAutocomplete'])
        ->name('employees.search.autocomplete');

    // Bulk actions
    Route::post('employees/bulk/delete', [EmployeeController::class, 'bulkDelete'])
        ->name('employees.bulk.delete');
    Route::post('employees/bulk/status-update', [EmployeeController::class, 'bulkStatusUpdate'])
        ->name('employees.bulk.status-update');
});
Route::get('/', [AuthController::class, 'index']);
Route::post('/Auth/login', [AuthController::class, 'process_login'])->name('auth.login.process');
Route::get('/Auth/logout', [AuthController::class, 'logout'])->name('auth.logout');

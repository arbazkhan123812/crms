<?php

use App\Http\Controllers\Admin\AttendanceController;
use App\Http\Controllers\Admin\CompanyController;
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\DesignationController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\HolidayController;
use App\Http\Controllers\Admin\LeaveController;
use App\Http\Controllers\Admin\LeaveTypeController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\RecruitmentController;
use App\Http\Controllers\Admin\RoleController;
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
    // Permission management
    Route::get('/permissions', [PermissionController::class, 'index'])->name('permissions.index');
    Route::post('/permissions/get', [PermissionController::class, 'getPermissions'])->name('permissions.get');
    Route::post('/permissions/save', [PermissionController::class, 'savePermissions'])->name('permissions.save');
    Route::get('/permissions/role-name/{roleId}', [PermissionController::class, 'getRoleName'])->name('permissions.role-name');
    Route::post('/permissions/copy-role-to-user', [PermissionController::class, 'copyRoleToUser'])->name('permissions.copy-role-to-user');
    Route::resource('companies', CompanyController::class);

    // Recruitment Module
    Route::get('recruitment', [RecruitmentController::class, 'index'])->name('recruitment.index');

    // Jobs
    Route::get('recruitment/jobs', [RecruitmentController::class, 'jobs'])->name('recruitment.jobs');
    Route::get('recruitment/jobs/create', [RecruitmentController::class, 'createJob'])->name('recruitment.jobs.create');
    Route::post('recruitment/jobs', [RecruitmentController::class, 'storeJob'])->name('recruitment.jobs.store');
    Route::get('recruitment/jobs/{id}', [RecruitmentController::class, 'showJob'])->name('recruitment.jobs.show');
    Route::get('recruitment/jobs/{id}/edit', [RecruitmentController::class, 'editJob'])->name('recruitment.jobs.edit');
    Route::put('recruitment/jobs/{id}', [RecruitmentController::class, 'updateJob'])->name('recruitment.jobs.update');
    Route::post('recruitment/jobs/{id}/status', [RecruitmentController::class, 'updateJobStatus'])->name('recruitment.jobs.status');

    // Candidates
    Route::get('recruitment/candidates', [RecruitmentController::class, 'candidates'])->name('recruitment.candidates');
    Route::get('recruitment/candidates/create', [RecruitmentController::class, 'createCandidate'])->name('recruitment.candidates.create');
    Route::post('recruitment/candidates', [RecruitmentController::class, 'storeCandidate'])->name('recruitment.candidates.store');
    Route::get('recruitment/candidates/{id}', [RecruitmentController::class, 'showCandidate'])->name('recruitment.candidates.show');
    Route::post('recruitment/candidates/{id}/status', [RecruitmentController::class, 'updateCandidateStatus'])->name('recruitment.candidates.status');
    // Delete routes - using GET for simple href delete
    Route::get('recruitment/jobs/{id}/delete', [RecruitmentController::class, 'destroyJob'])
        ->name('recruitment.jobs.destroy');

    Route::get('recruitment/candidates/{id}/delete', [RecruitmentController::class, 'destroyCandidate'])
        ->name('recruitment.candidates.destroy');

    Route::get('recruitment/interviews/{id}/delete', [RecruitmentController::class, 'destroyInterview'])
        ->name('recruitment.interviews.destroy');

    // Interviews
    Route::get('recruitment/interviews', [RecruitmentController::class, 'interviews'])->name('recruitment.interviews');
    Route::post('recruitment/interviews/schedule', [RecruitmentController::class, 'scheduleInterview'])->name('recruitment.interviews.schedule');
    Route::post('recruitment/interviews/feedback/{id}', [RecruitmentController::class, 'updateInterviewFeedback'])->name('recruitment.interviews.feedback');
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

    Route::delete('employees/delete/{id}', [EmployeeController::class, 'delete'])
        ->name('employees.delete');

    Route::get('get-designations/{departmentId}', [EmployeeController::class, 'getDesignationsByDepartment'])
        ->name('getDesignations');

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

    Route::put('attendance/update', [AttendanceController::class, 'update'])->name('attendance.update');


    Route::delete('attendance/{id}', [AttendanceController::class, 'destroy'])->name('attendance.destroy');
    // Attendance Routes
    Route::get('attendance', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::get('attendance/delete/{id}', [AttendanceController::class, 'delete_attendance'])->name('attendance.delete');
    Route::post('attendance/mark', [AttendanceController::class, 'markAttendance'])->name('attendance.mark');
    Route::post('attendance/bulk', [AttendanceController::class, 'markBulkAttendance'])->name('attendance.bulk');
    Route::get('attendance/monthly', [AttendanceController::class, 'monthlyReport'])->name('attendance.monthly');

    Route::get('attendance/employee/{id}/shift', [AttendanceController::class, 'getEmployeeShift'])
        ->name('attendance.employee.shift');
    Route::get('attendance/today-status', [AttendanceController::class, 'getTodayShiftStatus'])
        ->name('attendance.today-status');
    // Employee Check-in/Check-out (for mobile/web)
    Route::post('attendance/check-in', [AttendanceController::class, 'checkIn'])->name('attendance.checkin');
    Route::post('attendance/check-out', [AttendanceController::class, 'checkOut'])->name('attendance.checkout');
    Route::get('attendance/export', [AttendanceController::class, 'export'])->name('attendance.export');
    Route::resource('leave-types', LeaveTypeController::class);
    
    Route::get('leaves', [LeaveController::class, 'index'])->name('leaves.index');
    Route::get('leaves/create', [LeaveController::class, 'create'])->name('leaves.create');
    Route::post('leaves', [LeaveController::class, 'store'])->name('leaves.store');
    Route::get('leaves/{id}', [LeaveController::class, 'show'])->name('leaves.show');
    Route::post('leaves/{id}/status', [LeaveController::class, 'updateStatus'])->name('leaves.status');
    
    Route::get('leave-balances', [LeaveController::class, 'balances'])->name('leaves.balances');
    Route::post('leave-balances/init', [LeaveController::class, 'initializeBalance'])->name('leaves.balances.init');
    
    Route::get('leave-calendar', [LeaveController::class, 'calendar'])->name('leaves.calendar');
    
    Route::resource('holidays', HolidayController::class);
    Route::resource('leave-types', LeaveTypeController::class);
    Route::post('leave-types/{id}/toggle-status', [LeaveTypeController::class, 'toggleStatus'])
        ->name('leave-types.toggle-status');

    Route::resource('holidays', HolidayController::class);
    Route::post('holidays/bulk-delete', [HolidayController::class, 'bulkDelete'])
        ->name('holidays.bulk-delete');
    Route::get('holidays/yearly/{year}', [HolidayController::class, 'getYearlyHolidays'])
        ->name('holidays.yearly');
    Route::post('holidays/import', [HolidayController::class, 'import'])
        ->name('holidays.import');
    Route::get('holidays/{id}/duplicate', [HolidayController::class, 'duplicate'])
        ->name('holidays.duplicate');
});


Route::prefix('employee')->name('employee.')->middleware(['auth'])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('profile', [DashboardController::class, 'profile'])->name('profile');
    Route::get('attendance', [DashboardController::class, 'attendance'])->name('attendance');
    Route::get('leaves', [DashboardController::class, 'leaves'])->name('leaves');
    Route::post('leaves/apply', [DashboardController::class, 'applyLeave'])->name('leaves.apply');
    Route::post('leaves/{id}/cancel', [DashboardController::class, 'cancelLeave'])->name('leaves.cancel');
});

Route::get('/', [AuthController::class, 'index']);
Route::post('/Auth/login', [AuthController::class, 'process_login'])->name('auth.login.process');
Route::get('/Auth/logout', [AuthController::class, 'logout'])->name('auth.logout');

<?php

use App\Http\Controllers\Admin\AccountController;
use App\Http\Controllers\Admin\ContactController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EmailController;
use App\Http\Controllers\Admin\LeadController;
use App\Http\Controllers\Admin\NotificationController;
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
    Route::get('/leads/{id}/profile', [LeadController::class, 'show'])->name('lead.show');

    Route::get('/accounts', [AccountController::class, 'index'])->name('accounts.index');
    Route::get('/accounts/{id}', [AccountController::class, 'show'])->name('account.show');
    Route::post('/accounts/store', [AccountController::class, 'store'])->name('account.store');
    Route::put('/accounts/{id}', [AccountController::class, 'update'])->name('account.update');
    Route::delete('/accounts/{id}', [AccountController::class, 'destroy'])->name('account.destroy');
    Route::get('/accounts/{id}/edit', [AccountController::class, 'edit'])->name('account.edit');
    
     Route::get('/contacts', [ContactController::class, 'index'])->name('contacts.index');
    Route::post('/contacts/store', [ContactController::class, 'store'])->name('contact.store');
    Route::put('/contacts/{id}', [ContactController::class, 'update'])->name('contact.update');
    Route::delete('/contacts/{id}', [ContactController::class, 'destroy'])->name('contact.destroy');
    Route::get('/contacts/{id}/edit', [ContactController::class, 'edit'])->name('contact.edit');    
    Route::get('/contacts', [ContactController::class, 'index'])->name('contacts.index');
    Route::get('/contacts/{id}/profile', [ContactController::class, 'show'])->name('contact.show');
    Route::post('/contacts', [ContactController::class, 'store'])->name('contacts.store');

    Route::get('/contacts', [ContactController::class, 'index'])->name('contacts.index');
    Route::get('/contacts/{id}/profile', [ContactController::class, 'show'])->name('contact.show');
    Route::post('/contacts', [ContactController::class, 'store'])->name('contacts.store');

    Route::post('/leads/save', [LeadController::class, 'save'])->name('lead.save');
    Route::delete('/leads/{id}', [LeadController::class, 'delete'])->name('lead.delete');
    Route::get('/leads/{id}', [LeadController::class, 'get'])->name('lead.get');
    Route::get('/leads/{id}/notes', [LeadController::class, 'getNotes'])->name('lead.notes');
    Route::post('/leads/add-note', [LeadController::class, 'addNote'])->name('lead.addNote');
    Route::put('/leads/notes/{id}', [LeadController::class, 'updateNote'])->name('lead.updateNote');
    Route::delete('/leads/notes/{id}', [LeadController::class, 'deleteNote'])->name('lead.deleteNote');
    Route::post('/leads/add-call', [LeadController::class, 'addCall'])->name('lead.addCall');
    Route::get('/leads/{id}/calls', [LeadController::class, 'getCalls'])->name('lead.calls');
    Route::get('/leads/{id}/calls', [LeadController::class, 'getLeadCalls'])->name('lead.calls');
    Route::post('/leads/log-call', [LeadController::class, 'logCall'])->name('lead.logCall');
    Route::get('/leads/{id}/convert', [LeadController::class, 'convertToCustomer'])->name('lead.convert');
    Route::post('/leads/{id}/process-conversion', [LeadController::class, 'processConversion'])->name('lead.processConversion');
    Route::post('/leads/schedule-call', [LeadController::class, 'createScheduledCall'])->name('lead.scheduleCall');
    Route::put('/leads/calls/{id}', [LeadController::class, 'updateCallToLogged'])->name('lead.updateCall');
    Route::put('/leads/calls/{id}/full', [LeadController::class, 'updateCall'])->name('lead.updateCallFull');
    Route::delete('/leads/calls/{id}', [LeadController::class, 'deleteLeadCall'])->name('lead.deleteCall');
    Route::post('/leads/schedule-meeting', [LeadController::class, 'scheduleMeeting'])->name('lead.scheduleMeeting');
    Route::get('/leads/{id}/meetings', [LeadController::class, 'getLeadMeetings'])->name('lead.meetings');
    Route::put('/leads/meetings/{id}', [LeadController::class, 'updateMeeting'])->name('lead.updateMeeting');
    Route::delete('/leads/meetings/{id}', [LeadController::class, 'deleteMeeting'])->name('lead.deleteMeeting');
    Route::post('/leads/meetings/{id}/complete', [LeadController::class, 'completeMeeting'])->name('lead.completeMeeting');
    Route::post('/leads/send-email', [LeadController::class, 'sendEmail'])->name('lead.sendEmail');
    
    Route::get('/email-templates', [LeadController::class, 'getEmailTemplates'])->name('email.templates');
    Route::get('/email-template/{id}', [LeadController::class, 'getEmailTemplate'])->name('email.template.get');
    Route::post('/email-template/save', [LeadController::class, 'saveEmailTemplate'])->name('email.template.save');

    Route::get('/permissions', [PermissionController::class, 'index'])->name('permissions.index');
    Route::post('/permissions/get', [PermissionController::class, 'getPermissions'])->name('permissions.get');
    Route::post('/permissions/save', [PermissionController::class, 'savePermissions'])->name('permissions.save');
    Route::get('/permissions/all', [PermissionController::class, 'getAllPermissions'])->name('permissions.all');
    Route::get('/permissions/role-name/{roleId}', [PermissionController::class, 'getRoleName'])->name('permissions.role-name');
    Route::get('/leads/{id}/tasks', [LeadController::class, 'getTasks'])->name('lead.tasks');
    Route::put('/leads/tasks/{id}', [LeadController::class, 'updateTask'])->name('lead.updateTask');
    Route::delete('/leads/tasks/{id}', [LeadController::class, 'deleteTask'])->name('lead.deleteTask');
    Route::put('/tasks/{id}/status', [LeadController::class, 'updateTaskStatus'])->name('task.updateStatus');
    Route::post('/leads/add-task', [LeadController::class, 'addTask'])->name('lead.addTask');
    Route::post('/email/send', [EmailController::class, 'sendEmail'])->name('email.send');
    Route::get('/emails/{entityType}/{entityId}', [EmailController::class, 'getEmails'])->name('emails.get');
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/{id}', [NotificationController::class, 'show'])->name('notifications.show');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.read-all');
});
Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

Route::get('/', [AuthController::class, 'index']);
Route::post('/Auth/login', [AuthController::class, 'process_login'])->name('auth.login.process');
Route::get('/Auth/logout', [AuthController::class, 'logout'])->name('auth.logout');
// Route::get('/Auth/testy', [AuthController::class, 'testy']);

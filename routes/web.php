<?php
// routes/web.php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Doctor\DoctorController;
use App\Http\Controllers\Specialist\SpecialistController;
use App\Http\Controllers\Admin\HospitalController;
use App\Http\Controllers\Admin\SpecializationController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Doctor\PatientController;
use App\Http\Controllers\Doctor\MedicalRecordController;
use App\Http\Controllers\Doctor\CaseController;
use App\Http\Controllers\Admin\CaseAssignmentController;
use App\Http\Controllers\Specialist\SpecialistCaseController;
use App\Http\Controllers\Doctor\DiscussionController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Admin\AuditLogController;

Route::get('/', function () {
    return view('auth.login');
});
// ── Admin routes ──────────────────────────────────────
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {

    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

    // Hospitals
    Route::resource('hospitals', HospitalController::class);
    Route::patch('hospitals/{hospital}/toggle', [HospitalController::class, 'toggle'])->name('hospitals.toggle');

    // Specializations
    Route::resource('specializations', SpecializationController::class);
    Route::patch('specializations/{specialization}/toggle', [SpecializationController::class, 'toggle'])->name('specializations.toggle');


    // Users
    Route::resource('users', UserController::class);
    Route::patch('users/{user}/toggle', [UserController::class, 'toggle'])
        ->name('users.toggle');
    Route::patch('users/{user}/reset-password', [UserController::class, 'resetPassword'])
        ->name('users.reset-password');

    // Case Management
    Route::get('cases',
        [CaseAssignmentController::class, 'index'])->name('cases.index');
    Route::get('cases/{case}',
        [CaseAssignmentController::class, 'show'])->name('cases.show');
    Route::get('cases/{case}/assign',
        [CaseAssignmentController::class, 'assign'])->name('cases.assign');
    Route::post('cases/{case}/assign',
        [CaseAssignmentController::class, 'storeAssignment'])->name('cases.assign.store');
    Route::patch('cases/{case}/resolve',
        [CaseAssignmentController::class, 'resolve'])->name('cases.resolve');
    Route::patch('cases/{case}/close',
        [CaseAssignmentController::class, 'close'])->name('cases.close');




    Route::get('audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');
    Route::get('audit-logs/export', [AuditLogController::class, 'export'])->name('audit-logs.export');
    Route::get('audit-logs/{auditLog}', [AuditLogController::class, 'show'])->name('audit-logs.show');
});

// ── Doctor routes ─────────────────────────────────────

Route::middleware(['auth', 'doctor'])->prefix('doctor')->name('doctor.')->group(function () {

    Route::get('/dashboard', [DoctorController::class, 'dashboard'])->name('dashboard');
    // Patients
    Route::resource('patients', PatientController::class);
    Route::get('patients/{patient}/records', [PatientController::class, 'records'])->name('patients.records');

    // Medical Records
    Route::get('medical-records/{patient}/create', [MedicalRecordController::class, 'create'])->name('medical-records.create');
    Route::post('medical-records/{patient}', [MedicalRecordController::class, 'store'])->name('medical-records.store');
    Route::resource('medical-records', MedicalRecordController::class)->except(['create', 'store']);
    // Cases
    Route::get('patients/{patient}/cases/create',
        [CaseController::class, 'create'])->name('cases.create');
    Route::post('patients/{patient}/cases',
        [CaseController::class, 'store'])->name('cases.store');
    Route::get('cases',[CaseController::class, 'index'])->name('cases.index');Route::get('cases/{case}',
        [CaseController::class, 'show'])->name('cases.show');
    Route::delete('cases/{case}',[CaseController::class, 'destroy'])->name('cases.destroy');

    Route::post('cases/{case}/discussions', [DiscussionController::class, 'store'])->name('discussions.store');
    Route::delete('discussions/{discussion}', [DiscussionController::class, 'destroy'])->name('discussions.destroy');
});

// ── Specialist routes ─────────────────────────────────
Route::middleware(['auth', 'specialist'])->prefix('specialist')->name('specialist.')->group(function () {

    Route::get('/dashboard', [SpecialistController::class, 'dashboard'])->name('dashboard');

    // Cases assigned to this specialist
    Route::get('cases', [SpecialistCaseController::class, 'index'])->name('cases.index');
    Route::get('cases/{case}', [SpecialistCaseController::class, 'show'])->name('cases.show');
    Route::patch('cases/{assignment}/accept', [SpecialistCaseController::class, 'accept'])->name('cases.accept');
    Route::patch('cases/{assignment}/decline', [SpecialistCaseController::class, 'decline'])->name('cases.decline');
    Route::patch('cases/{assignment}/complete', [SpecialistCaseController::class, 'complete'])->name('cases.complete');

    // Discussions
    Route::post('cases/{case}/discussions', [DiscussionController::class, 'store'])->name('discussions.store');
    Route::delete('discussions/{discussion}', [DiscussionController::class, 'destroy'])->name('discussions.destroy');
});


// ── Profile (all auth users) ──────────────────────────
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


Route::middleware('auth')->group(function () {
    // Notifications
    Route::get('notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('notifications/{notification}/read', [NotificationController::class, 'markRead'])->name('notifications.read');
    Route::post('notifications/mark-all-read', [NotificationController::class, 'markAllRead'])->name('notifications.mark-all-read');
    Route::get('notifications/unread-count', [NotificationController::class, 'unreadCount'])->name('notifications.unread-count');
    Route::get('notifications/recent', [NotificationController::class, 'recent'])->name('notifications.recent');
    Route::delete('notifications/{notification}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
    Route::delete('notifications', [NotificationController::class, 'clearAll'])->name('notifications.clear');
});


require __DIR__.'/auth.php';
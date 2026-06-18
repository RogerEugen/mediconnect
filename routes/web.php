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
use App\Http\Controllers\ClinicalCaseController;
use App\Http\Controllers\ClinicalDiscussionController;

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

    Route::get('audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');
    Route::get('audit-logs/export', [AuditLogController::class, 'export'])->name('audit-logs.export');
    Route::get('audit-logs/{auditLog}', [AuditLogController::class, 'show'])->name('audit-logs.show');
});

// ── Doctor routes ─────────────────────────────────────

Route::middleware(['auth', 'doctor'])->prefix('doctor')->name('doctor.')->group(function () {

    Route::get('/dashboard', [DoctorController::class, 'dashboard'])->name('dashboard');
});

// ── Specialist routes ─────────────────────────────────
Route::middleware(['auth', 'specialist'])->prefix('specialist')->name('specialist.')->group(function () {

    Route::get('/dashboard', [SpecialistController::class, 'dashboard'])->name('dashboard');

});


// ── Profile (all auth users) ──────────────────────────
Route::middleware('auth')->group(function () {
    Route::get('clinical-cases', [ClinicalCaseController::class, 'index'])->name('clinical-cases.index');
    Route::get('clinical-cases/create', [ClinicalCaseController::class, 'create'])->name('clinical-cases.create');
    Route::post('clinical-cases', [ClinicalCaseController::class, 'store'])->name('clinical-cases.store');
    Route::get('clinical-cases/{clinicalCase}', [ClinicalCaseController::class, 'show'])->name('clinical-cases.show');
    Route::post('clinical-cases/{clinicalCase}/follow', [ClinicalCaseController::class, 'toggleFollow'])->name('clinical-cases.follow');
    Route::patch('clinical-cases/{clinicalCase}/resolve', [ClinicalCaseController::class, 'resolve'])->name('clinical-cases.resolve');
    Route::patch('clinical-cases/{clinicalCase}/reopen', [ClinicalCaseController::class, 'reopen'])->name('clinical-cases.reopen');
    Route::delete('clinical-cases/{clinicalCase}', [ClinicalCaseController::class, 'destroy'])->name('clinical-cases.destroy');
    Route::post('clinical-cases/{clinicalCase}/discussions', [ClinicalDiscussionController::class, 'store'])->name('clinical-discussions.store');
    Route::get('clinical-cases/{clinicalCase}/discussions/sync', [ClinicalDiscussionController::class, 'sync'])->name('clinical-discussions.sync');
    Route::delete('clinical-discussions/{discussion}', [ClinicalDiscussionController::class, 'destroy'])->name('clinical-discussions.destroy');

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

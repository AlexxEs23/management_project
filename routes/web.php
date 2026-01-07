<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\LaporanController;

// Public routes - Landing Page
Route::get('/', [LandingController::class, 'index'])->name('home');
Route::post('/submit-order', [LandingController::class, 'submit'])->name('landing.submit');

// Authentication routes (Admin & Employee only)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    // Register route removed - no more client self-registration
    
    // Password Reset Routes
    Route::get('/forgot-password', [AuthController::class, 'showForgotPasswordForm'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetPasswordForm'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// Authenticated routes
Route::middleware(['auth'])->group(function () {
    
    // Dashboard routes - role based (Admin & Employee only)
    Route::get('/dashboard', function() {
        $user = auth()->user();
        
        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        } else {
            return redirect()->route('employee.dashboard');
        }
    })->name('dashboard');

    // Admin routes
    Route::middleware(['admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'adminDashboard'])->name('dashboard');
        Route::post('/dashboard/save-target', [DashboardController::class, 'saveTarget'])->name('dashboard.save-target');
        Route::get('/dashboard/calendar-events', [DashboardController::class, 'getCalendarEvents'])->name('dashboard.calendar-events');
        
        // Orders management (now includes pending review from landing page)
        Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
        Route::post('/orders/{order}/confirm', [OrderController::class, 'confirmPayment'])->name('orders.confirm');
        Route::post('/orders/{order}/reject', [OrderController::class, 'rejectOrder'])->name('orders.reject');
        Route::get('/orders/{order}/installment-info', [OrderController::class, 'getInstallmentInfo'])->name('orders.installment-info');
        Route::post('/orders/{order}/update-installment', [OrderController::class, 'updateInstallment'])->name('orders.update-installment');
        
        // Clients management
        Route::get('/clients', [ClientController::class, 'index'])->name('clients.index');
        Route::get('/clients/{client}', [ClientController::class, 'show'])->name('clients.show');
        Route::get('/clients/{client}/edit', [ClientController::class, 'edit'])->name('clients.edit');
        Route::put('/clients/{client}', [ClientController::class, 'update'])->name('clients.update');
        
        // Projects management
        Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');
        Route::get('/projects/create', [ProjectController::class, 'create'])->name('projects.create');
        Route::post('/projects', [ProjectController::class, 'store'])->name('projects.store');
        Route::get('/projects/{project}', [ProjectController::class, 'show'])->name('projects.show');
        Route::patch('/projects/{project}/status', [ProjectController::class, 'updateStatus'])->name('projects.updateStatus');
        Route::patch('/projects/{project}/notes', [ProjectController::class, 'updateNotes'])->name('projects.updateNotes');
        Route::patch('/projects/{project}/mark-completed', [ProjectController::class, 'markCompleted'])->name('projects.markCompleted');
        
        // Project Expenses
        Route::post('/projects/{project}/expenses', [ProjectController::class, 'storeExpense'])->name('projects.expenses.store');
        Route::patch('/projects/{project}/expenses/{expense}', [ProjectController::class, 'updateExpense'])->name('projects.expenses.update');
        Route::delete('/projects/{project}/expenses/{expense}', [ProjectController::class, 'deleteExpense'])->name('projects.expenses.delete');
        
        // Team member management
        Route::post('/projects/{project}/team-members', [ProjectController::class, 'assignTeamMember'])->name('projects.assignTeamMember');
        Route::delete('/projects/{project}/team-members/{member}', [ProjectController::class, 'removeTeamMember'])->name('projects.removeTeamMember');
        
        // Karyawan (Employee) management
        Route::get('/karyawan', [KaryawanController::class, 'index'])->name('karyawan.index');
        Route::get('/karyawan/create', [KaryawanController::class, 'create'])->name('karyawan.create');
        Route::post('/karyawan', [KaryawanController::class, 'store'])->name('karyawan.store');
        Route::get('/karyawan/{karyawan}', [KaryawanController::class, 'show'])->name('karyawan.show');
        Route::get('/karyawan/{karyawan}/edit', [KaryawanController::class, 'edit'])->name('karyawan.edit');
        Route::put('/karyawan/{karyawan}', [KaryawanController::class, 'update'])->name('karyawan.update');
        Route::delete('/karyawan/{karyawan}', [KaryawanController::class, 'destroy'])->name('karyawan.destroy');
        
        // Laporan (Reports) management
        Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
        Route::get('/laporan/export', [LaporanController::class, 'exportCsv'])->name('laporan.export');
        
        // Teams management
        Route::get('/projects/{project}/teams/create', [TeamController::class, 'create'])->name('teams.create');
        Route::post('/projects/{project}/teams', [TeamController::class, 'store'])->name('teams.store');
        Route::delete('/teams/{team}/members/{member}', [TeamController::class, 'removeMember'])->name('teams.removeMember');
    });

    // Employee routes
    Route::middleware(['employee'])->prefix('employee')->name('employee.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'employeeDashboard'])->name('dashboard');
        
        // View assigned projects
        Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');
        Route::get('/projects/{project}', [ProjectController::class, 'show'])->name('projects.show');
    });
});


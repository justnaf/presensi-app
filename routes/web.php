<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\User\DashboardController as UserDashboardController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\InstitutionController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\Events\EventCategoryController;
use App\Http\Controllers\Admin\Events\EventController;
use App\Http\Controllers\User\EventController as UserEventController;
use App\Http\Controllers\Admin\Data\EventAttendeeController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\Admin\Events\ScannerController;
use App\Http\Controllers\Admin\Events\StaticQrController;


Route::get('/', [PublicController::class, 'welcome'])->name('home');
Route::get('/tentang-kami', [PublicController::class, 'about'])->name('tentang.kami');
Route::get('/kegiatan', [PublicController::class, 'indexActivities'])->name('kegiatan');
Route::get('/kegiatan/{kegiatan}', [PublicController::class, 'showActivities'])->name('kegiatan.show');

Route::get('/check-in', [PublicController::class, 'showCheckInForm'])->name('public.checkin.form');
Route::post('/check-in', [PublicController::class, 'processCheckIn'])->name('public.checkin.process');

// Grup untuk Dashboard Administrator
Route::group(['middleware' => ['auth', 'role:Administrator'], 'prefix' => 'admin', 'as' => 'admin.'], function () {
    Route::get('/dashboard', AdminDashboardController::class)->name('dashboard');
    Route::resource('institutions', InstitutionController::class)->except(['create', 'show', 'edit']);
    Route::resource('roles', RoleController::class)->except(['create', 'show', 'edit']);
    Route::resource('users', UserController::class)->except(['create', 'store', 'show', 'edit']);
    Route::resource('event-categories', EventCategoryController::class)->except(['create', 'show', 'edit']);
    Route::resource('events', EventController::class)->except(['show']);
    Route::patch('events/{event}/status', [EventController::class, 'updateStatus'])->name('events.update.status');
    Route::get('/events/scanner', [ScannerController::class, 'index'])->name('events.scanner');
    Route::post('/events/{event}/scan', [ScannerController::class, 'processScan'])->name('events.scan');
    Route::get('/events/static-qrs', [StaticQrController::class, 'index'])->name('events.static-qrs.index');
    Route::post('/events/{event}/static-qrs', [StaticQrController::class, 'store'])->name('events.static-qrs.store');
    Route::delete('/events/static-qrs/{staticQr}', [StaticQrController::class, 'destroy'])->name('events.static-qrs.destroy');

    Route::get('events/attendees', [EventAttendeeController::class, 'showAttendees'])->name('events.attendees');
    Route::get('events/attendees/export', [EventAttendeeController::class, 'export'])->name('events.attendees.export');

    require __DIR__ . '/settings.php';
});

// Grup untuk Dashboard Pengguna
Route::group(['middleware' => ['auth'],], function () {
    Route::get('/dashboard', UserDashboardController::class)->name('dashboard');

    Route::get('/activities', [UserEventController::class, 'index'])->name('activities.index');
    Route::get('/activities/{event}', [UserEventController::class, 'show'])->name('activities.show');
    Route::post('/activities/{event}/join', [UserEventController::class, 'join'])->name('activities.join');
    Route::get('/scanner', [UserEventController::class, 'qrScanner'])->name('scanner');

    Route::get('/histories/my-tickets', [UserEventController::class, 'myTickets'])->name('histories.my-tickets');
    Route::get('/histories/my-tickets/attendances', [UserEventController::class, 'attendanceHistories'])->name('histories.my-tickets.attendances');
    Route::get('/histories/my-tickets/{ticket}', [UserEventController::class, 'showTickets'])->name('histories.my-tickets.show');
    // ... rute pengguna lainnya di sini ...
});


require __DIR__ . '/auth.php';

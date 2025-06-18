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
use App\Http\Controllers\Admin\Events\QrPresenterController;
use App\Http\Controllers\PublicController;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

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
    Route::get('events/attendees', [EventAttendeeController::class, 'showAttendees'])->name('events.attendees');
    Route::get('events/attendees/export', [EventAttendeeController::class, 'export'])->name('events.attendees.export');
    Route::get('/events/qr-code', [QrPresenterController::class, 'index'])->name('events.qrcode.presenter');
    Route::get('/events/{event}/check-scan', [QrPresenterController::class, 'checkScanStatus'])->name('events.qrcode.presenter.status');


    require __DIR__ . '/settings.php';
});

// Grup untuk Dashboard Pengguna
Route::group(['middleware' => ['auth'],], function () {
    Route::get('/dashboard', UserDashboardController::class)->name('dashboard');
    Route::get('/activities', [UserEventController::class, 'index'])->name('activities.index');
    Route::get('/histories/my-tickets', [UserEventController::class, 'myTickets'])->name('histories.my-tickets');
    Route::get('/histories/my-tickets/attendances', [UserEventController::class, 'attendanceHistories'])->name('histories.my-tickets.attendances');
    Route::get('/histories/my-tickets/{ticket}', [UserEventController::class, 'showTickets'])->name('histories.my-tickets.show');
    Route::get('/activities/{event}', [UserEventController::class, 'show'])->name('activities.show');
    Route::post('/activities/{event}/join', [UserEventController::class, 'join'])->name('activities.join');
    // ... rute pengguna lainnya di sini ...
});


require __DIR__ . '/auth.php';

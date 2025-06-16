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

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');


// Grup untuk Dashboard Administrator
Route::group(['middleware' => ['auth', 'role:Administrator'], 'prefix' => 'admin', 'as' => 'admin.'], function () {
    Route::get('/dashboard', AdminDashboardController::class)->name('dashboard');
    Route::resource('institutions', InstitutionController::class)->except(['create', 'show', 'edit']);
    Route::resource('roles', RoleController::class)->except(['create', 'show', 'edit']);
    Route::resource('users', UserController::class)->except(['create', 'store', 'show', 'edit']);
    Route::resource('event-categories', EventCategoryController::class)->except(['create', 'show', 'edit']);
    Route::resource('events', EventController::class)->except(['show']);

    require __DIR__ . '/settings.php';
});

// Grup untuk Dashboard Pengguna
Route::group(['middleware' => ['auth'],], function () {
    Route::get('/dashboard', UserDashboardController::class)->name('dashboard');
    // ... rute pengguna lainnya di sini ...
});


require __DIR__ . '/auth.php';

<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\User\DashboardController as UserDashboardController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\InstitutionController;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');


// Grup untuk Dashboard Administrator
Route::group(['middleware' => ['auth', 'role:Administrator'], 'prefix' => 'admin', 'as' => 'admin.'], function () {
    Route::get('/dashboard', AdminDashboardController::class)->name('dashboard');
    Route::resource('institutions', InstitutionController::class);
    require __DIR__ . '/settings.php';
});

// Grup untuk Dashboard Pengguna
Route::group(['middleware' => ['auth'],], function () {
    Route::get('/dashboard', UserDashboardController::class)->name('dashboard');
    // ... rute pengguna lainnya di sini ...
});


require __DIR__ . '/auth.php';

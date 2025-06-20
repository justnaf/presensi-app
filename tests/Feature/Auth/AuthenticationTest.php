<?php

use App\Models\User;
use Spatie\Permission\Models\Role;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

// Blok ini akan dijalankan sebelum setiap tes.
// Kita siapkan role yang dibutuhkan oleh controller.
beforeEach(function () {
    Role::create(['name' => 'Administrator']);
    Role::create(['name' => 'Pengguna']);
});

test('login screen can be rendered', function () {
    $this->get('/login')->assertStatus(200);
});

// ===================================================================
// TES LOGIN SUKSES DIPERBARUI DI SINI
// ===================================================================

test('administrator can authenticate and is redirected to admin dashboard', function () {
    // 1. Arrange: Buat user dan berikan role Administrator
    $adminUser = User::factory()->create();
    $adminUser->assignRole('Administrator');

    // 2. Act: Lakukan percobaan login
    $response = $this->post('/login', [
        'username' => $adminUser->username,
        'password' => 'password', // 'password' adalah password default dari factory
    ]);

    // 3. Assert: Pastikan user terautentikasi dan diarahkan ke rute yang benar
    $this->assertAuthenticatedAs($adminUser);
    $response->assertRedirect(route('admin.dashboard'));
});

test('regular user (pengguna) can authenticate and is redirected to general dashboard', function () {
    // 1. Arrange: Buat user dan berikan role Pengguna
    $regularUser = User::factory()->create();
    $regularUser->assignRole('Pengguna');

    // 2. Act: Lakukan percobaan login
    $response = $this->post('/login', [
        'username' => $regularUser->username,
        'password' => 'password',
    ]);

    // 3. Assert: Pastikan user terautentikasi dan diarahkan ke rute yang benar
    $this->assertAuthenticatedAs($regularUser);
    $response->assertRedirect(route('dashboard'));
});


// ===================================================================
// TES LAINNYA TIDAK PERLU DIUBAH
// ===================================================================

test('users can not authenticate with invalid password', function () {
    $user = User::factory()->create();

    $this->post('/login', [
        'username' => $user->username,
        'password' => 'wrong-password',
    ]);

    $this->assertGuest(); // Memastikan pengguna tidak berhasil login
});

test('users can logout', function () {
    $user = User::factory()->create();

    // Langsung login sebagai user ini dan lakukan logout
    $response = $this->actingAs($user)->post('/logout');

    $this->assertGuest();
    $response->assertRedirect('/'); // Pastikan diarahkan ke halaman utama
});

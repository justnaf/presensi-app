<?php

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

use App\Models\User;
use App\Models\Institution;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Illuminate\Auth\Events\Registered;
use Spatie\Permission\Models\Role;


// Blok ini akan dijalankan sebelum setiap 'test' di file ini
beforeEach(function () {
    // Membuat role "Pengguna" yang dibutuhkan oleh controller
    Role::create(['name' => 'Pengguna']);

    // Membuat satu institusi palsu untuk digunakan dalam tes
    $this->institution = Institution::factory()->create();
});

test('halaman registrasi dapat ditampilkan', function () {
    $this->get('/register')->assertStatus(200);
});

test('pengguna baru dapat mendaftar dengan sukses (termasuk avatar)', function () {
    // 1. Persiapan (Arrange)
    Event::fake(); // Mencegah event dikirim secara nyata
    Storage::fake('public'); // Menggunakan storage palsu untuk avatar

    $userData = [
        'name' => 'John Doe',
        'username' => 'johndoe',
        'email' => 'john.doe@example.com',
        'password' => 'Password123!',
        'password_confirmation' => 'Password123!',
        'institution_id' => $this->institution->id,
        'avatar' => UploadedFile::fake()->image('avatar.jpg'),
    ];

    // 2. Eksekusi (Act)
    $response = $this->post(route('register'), $userData); // Asumsi route bernama 'register'

    // 3. Pengecekan (Assert)
    $response->assertRedirect(route('dashboard')); // Pastikan redirect berhasil

    $this->assertDatabaseCount('users', 1); // Pastikan hanya ada 1 user dibuat
    $user = User::first();

    // Cek data user di database
    $this->assertDatabaseHas('users', [
        'name' => 'John Doe',
        'username' => 'johndoe',
        'email' => 'john.doe@example.com',
    ]);

    // Cek file avatar tersimpan
    $this->assertNotNull($user->avatar);
    $this->assertTrue(Storage::disk('public')->exists($user->avatar));

    // Cek relasi dengan institusi di pivot table
    $this->assertDatabaseHas('institution_user', [
        'user_id' => $user->id,
        'institution_id' => $this->institution->id,
    ]);

    // Cek apakah role "Pengguna" berhasil diberikan
    $this->assertTrue($user->hasRole('Pengguna'));

    // Cek apakah event Registered terkirim
    Event::assertDispatched(Registered::class);
});

test('pengguna baru dapat mendaftar tanpa mengirimkan avatar', function () {
    $userData = [
        'name' => 'Jane Doe',
        'username' => 'janedoe',
        'email' => 'jane.doe@example.com',
        'password' => 'Password123!',
        'password_confirmation' => 'Password123!',
        'institution_id' => $this->institution->id,
        // Tidak ada field 'avatar'
    ];

    $this->post(route('register'), $userData)
        ->assertRedirect(route('dashboard'));

    $this->assertDatabaseCount('users', 1);
    $user = User::first();
    $this->assertNull($user->avatar); // Pastikan kolom avatar kosong (null)
});


test('validasi gagal jika username sudah ada', function () {
    // Buat user terlebih dahulu
    User::factory()->create(['username' => 'existinguser']);

    $userData = [
        'name' => 'Another User',
        'username' => 'existinguser', // Gunakan username yang sama
        'email' => 'another@example.com',
        'password' => 'Password123!',
        'password_confirmation' => 'Password123!',
        'institution_id' => $this->institution->id,
    ];

    $this->post(route('register'), $userData)
        ->assertSessionHasErrors('username'); // Harapkan ada error validasi untuk username
});

test('validasi gagal jika institution_id tidak valid', function () {
    $userData = [
        'name' => 'Test User',
        'username' => 'testuser',
        'email' => 'test@example.com',
        'password' => 'Password123!',
        'password_confirmation' => 'Password123!',
        'institution_id' => 999, // ID yang tidak ada di database
    ];

    $this->post(route('register'), $userData)
        ->assertSessionHasErrors('institution_id');
});


# Sistem Manajemen Event & Presensi Digital PDM Kota Magelang
<img src="https://github.com/user-attachments/assets/0dc62ed7-49d5-4f81-8615-a0f0e7577e20" height="400" />

Aplikasi ini adalah solusi digital terpadu yang dirancang khusus untuk memenuhi kebutuhan Pimpinan Daerah Muhammadiyah (PDM) Kota Magelang dalam mengelola seluruh rangkaian kegiatan, mulai dari perencanaan hingga pelaporan kehadiran.

Dengan antarmuka yang modern dan responsif, platform ini bertujuan untuk menggantikan proses manual dengan alur kerja digital yang cepat, akurat, dan mudah diakses baik oleh admin maupun oleh peserta.


## **Fitur Utama**
1. **Manajemen Event Komprehensif**:
- Admin dapat dengan mudah membuat, mengedit, dan mengelola detail acara, termasuk jadwal (rundown), lokasi, pembicara, dan kategori kegiatan.
- Status event (Draft, Pendaftaran, Sedang Berlangsung, Selesai) dapat diubah secara dinamis untuk mengontrol siklus hidup setiap acara.

2. **Sistem Presensi Fleksibel**:
 - **Mode Tiket**: Untuk acara yang memerlukan pendaftaran, sistem dapat secara otomatis menerbitkan e-tiket dengan kode unik dan QR Code statis yang dapat ditunjukkan oleh peserta.
- **Mode Barcode** (Absensi di Lokasi): Untuk acara terbuka, sistem menyediakan dua metode absensi canggih:
     - Presenter QR: Admin menampilkan QR Code dinamis di layar yang terus berubah, di mana peserta memindai untuk check-in. Ini mencegah penyebaran QR Code.
     - Scanner QR: Admin menggunakan kamera perangkat mereka untuk memindai QR Code statis milik peserta (dari e-tiket) untuk proses check-in yang sangat cepat.
3. **Portal Pengguna & Admin Terpisah**:
- **Dasbor Admin**: Menyediakan kontrol penuh atas manajemen pengguna, role, institusi, event, dan melihat data pendaftar serta riwayat kehadiran.
- **Dasbor Pengguna**: Pengguna dapat dengan mudah menelusuri kegiatan, mendaftar, melihat tiket, dan mengakses riwayat aktivitas mereka.

4. **Pelaporan dan Ekspor Data**:
- Admin dapat dengan mudah mengekspor data pendaftar dan data kehadiran dari setiap event ke dalam format Excel untuk keperluan pelaporan dan arsip.

Dengan sistem ini, PDM Kota Magelang dapat meningkatkan profesionalisme acara, mengurangi beban kerja manual panitia, dan memberikan pengalaman yang lebih baik bagi seluruh peserta.
## **Documentation**

### 1. Persyaratan Sistem

Pastikan lingkungan pengembangan Anda memenuhi persyaratan berikut:
-   PHP 8.2 atau lebih baru
-   Composer 2.x
-   Node.js 20.x (LTS) & npm
-   Database (MySQL, PostgreSQL, atau SQLite)
-   Server Web (Nginx atau Apache, opsional jika menggunakan `php artisan serve`)

---

### 2. Instalasi & Setup

Ikuti langkah-langkah berikut untuk menjalankan proyek ini secara lokal.

#### a. Clone & Setup Awal
1.  **Clone Repositori**
    ```bash
    git clone https://github.com/justnaf/presensi-app
    cd presensi-app
    ```
2.  **Instal Dependensi PHP**
    ```bash
    composer install
    ```
3.  **Instal Dependensi JavaScript**
    ```bash
    npm install
    ```
4.  **Buat File `.env`**
    Salin file `.env.example` menjadi `.env`.
    ```bash
    cp .env.example .env
    ```
5.  **Generate Kunci Aplikasi**
    ```bash
    php artisan key:generate
    ```

#### b. Konfigurasi Database
1.  Buka file `.env` yang baru Anda buat.
2.  Sesuaikan variabel koneksi database (`DB_CONNECTION`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`) sesuai dengan pengaturan lokal Anda.
    ```env
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=pdm_events
    DB_USERNAME=root
    DB_PASSWORD=
    ```

#### c. Migrasi & Seeder
1.  **Jalankan Migrasi Database**
    Perintah ini akan membuat semua tabel yang dibutuhkan oleh aplikasi.
    ```bash
    php artisan migrate
    ```
2.  **Jalankan Seeder (Penting!)**
    Seeder akan mengisi data awal, termasuk **Roles & Permissions** yang krusial untuk fungsi aplikasi.
    ```bash
    php artisan db:seed
    ```
    *Pastikan `DatabaseSeeder.php` Anda memanggil seeder untuk Roles & Permissions.*

3.  **Buat Symbolic Link untuk Storage**
    Ini penting agar file yang diunggah (seperti avatar dan poster event) bisa diakses dari browser.
    ```bash
    php artisan storage:link
    ```

#### d. Jalankan Aplikasi
Anda perlu menjalankan dua proses secara bersamaan di dua terminal terpisah.

1.  **Terminal 1: Jalankan Vite Server**
    Proses ini meng-compile aset frontend (Vue, CSS).
    ```bash
    npm run dev
    ```
2.  **Terminal 2: Jalankan Server Laravel**
    Proses ini menjalankan backend aplikasi.
    ```bash
    php artisan serve
    ```

Sekarang, aplikasi Anda seharusnya sudah bisa diakses di `http://127.0.0.1:8000`.

---

### 3. Struktur Proyek Penting

-   **Backend (Laravel):**
    -   `app/Http/Controllers/Admin/`: Berisi semua controller untuk dasbor admin.
    -   `app/Http/Controllers/User/`: Berisi controller untuk halaman pengguna yang sudah login.
    -   `app/Http/Controllers/PublicController.php`: Mengatur halaman publik yang bisa diakses tanpa login.
    -   `app/Models/`: Berisi semua model Eloquent (User, Event, Institution, dll).
    -   `routes/web.php`: Pusat definisi semua route aplikasi.

-   **Frontend (Inertia + Vue):**
    -   `resources/js/Pages/`: Berisi semua komponen halaman Vue, terstruktur sesuai dengan namespace controller (e.g., `Admin/Events/Index.vue`).
    -   `resources/js/layouts/`: Berisi komponen layout utama (`AppLayout.vue` untuk admin, `MobileLayout.vue` untuk user, dll).
    -   `resources/js/composables/`: Berisi logika reusable seperti `useSweetAlert.ts`.
    -   `resources/js/types/`: Berisi definisi tipe global TypeScript.

---




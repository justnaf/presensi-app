<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventAttendance;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;


class PublicController extends Controller
{
    /**
     * Menampilkan form check-in setelah pengguna memindai QR Code.
     * Fungsi ini memvalidasi ID dari QR code sebelum menampilkan form.
     */
    public function showCheckInForm(Request $request): Response|RedirectResponse
    {
        // 1. Validasi input dasar dari URL
        $validated = $request->validate([
            'event_id' => 'required|exists:events,id',
            'scan_id' => 'required|string|uuid', // Memastikan formatnya UUID
        ]);

        $event = Event::find($validated['event_id']);
        $scanId = $validated['scan_id'];

        // 2. Validasi Aturan Bisnis
        // Cek apakah event sedang berlangsung
        if ($event->status !== 'ongoing') {
            return redirect()->route('home')->with('error', 'Maaf, event ini belum dimulai atau sudah selesai.');
        }

        // Cek apakah event ini menggunakan mode barcode
        if ($event->attendance_mode !== 'barcode') {
            return redirect()->route('home')->with('error', 'Metode absensi untuk event ini tidak valid.');
        }

        // 3. Validasi "One-Time Use" (Paling Penting)
        // Cek apakah QR Code (scan_id) ini sudah pernah digunakan untuk event ini
        $isUsed = EventAttendance::where('event_id', $event->id)
            ->where('scanned_barcode_value', $scanId)
            ->exists();

        if ($isUsed) {
            return redirect()->route('home')->with('error', 'QR Code ini sudah digunakan. Silakan minta QR Code baru.');
        }

        // 4. Jika semua validasi lolos, tampilkan halaman form check-in
        return Inertia::render('Public/CheckIn', [
            'event' => $event->only('id', 'name'),
            'scan_id' => $scanId, // Kirim scan_id ke frontend
        ]);
    }

    /**
     * Memproses data check-in dari pengguna.
     */
    public function processCheckIn(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required_if:is_guest,true|string|max:255',
            'origin_institution' => 'nullable|string|max:255',
            'is_guest' => 'required|boolean',
            'scan_id' => 'required|string|uuid|unique:event_attendance,scanned_barcode_value',
            'event_id' => 'required|exists:events,id',
        ]);

        try {
            EventAttendance::create([
                'event_id' => $validated['event_id'],
                'user_id' => Auth::id(), // Akan null jika tamu
                'scanned_barcode_value' => $validated['scan_id'],
                'name' => $validated['is_guest'] ? $validated['name'] : Auth::user()->name,
                'origin_institution' => $validated['origin_institution'],
            ]);

            return redirect()->route('home')->with('success', 'Check-in berhasil! Selamat datang.');
        } catch (\Exception $e) {
            // Log::error($e); // Sebaiknya log error
            return back()->with('error', 'Terjadi kesalahan. Silakan coba pindai ulang.');
        }
    }
}

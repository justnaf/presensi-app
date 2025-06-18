<?php

namespace App\Http\Controllers\Admin\Events;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\RedirectsWithFlash;
use App\Models\Event;
use App\Models\EventStaticQr;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class StaticQrController extends Controller
{
    use RedirectsWithFlash;

    public function __construct()
    {
        $this->middleware('permission:manage static qrs');
    }

    /**
     * Menampilkan halaman manajemen QR Statis.
     */
    public function index(Request $request): Response
    {
        $filters = $request->validate([
            'event_id' => 'nullable|exists:events,id',
        ]);

        // Ambil semua event mode barcode untuk dropdown
        $events = Event::where('attendance_mode', 'barcode')
            ->select('id', 'name')
            ->orderBy('name', 'asc')
            ->get();

        $selectedEvent = null;
        if (!empty($filters['event_id'])) {
            // Jika event dipilih, muat event tersebut beserta QR statisnya
            $selectedEvent = Event::with('staticQrs')->find($filters['event_id']);
        }

        return Inertia::render('Admin/Events/Event/StaticQR', [
            'events' => $events,
            'selectedEvent' => $selectedEvent, // Kirim event yang dipilih (atau null)
            'filters' => $filters,
        ]);
    }

    /**
     * Menyimpan QR statis baru untuk event yang dipilih.
     */
    public function store(Request $request, Event $event): RedirectResponse
    {
        $validated = $request->validate([
            'label' => 'required|string|max:255',
        ]);

        try {
            EventStaticQr::create([
                'event_id' => $event->id,
                'label' => $validated['label'],
                'code' => Str::uuid()->toString(),
            ]);
            // Redirect kembali ke halaman yang sama dengan event_id yang sudah dipilih
            return back()->with('success', 'QR Code statis berhasil dibuat.');
        } catch (\Exception $e) {
            return $this->redirectError($e, 'Gagal membuat QR Code.');
        }
    }

    /**
     * Menghapus QR statis.
     */
    public function destroy(EventStaticQr $staticQr): RedirectResponse
    {
        try {
            $staticQr->delete();
            return back()->with('success', 'QR Code berhasil dihapus.');
        } catch (\Exception $e) {
            return $this->redirectError($e, 'Gagal menghapus QR Code.');
        }
    }
}

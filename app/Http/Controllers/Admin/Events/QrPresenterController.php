<?php

namespace App\Http\Controllers\Admin\Events;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\Event;
use App\Models\EventAttendance;
use Illuminate\Http\JsonResponse;

class QrPresenterController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:present qr-code');
    }

    /**
     * Menampilkan halaman Presenter dengan dropdown pemilihan event.
     */
    public function index(): Response
    {
        // Ambil semua event yang menggunakan mode barcode untuk dropdown
        $events = Event::where('attendance_mode', 'barcode')
            ->select('id', 'name')
            ->orderBy('name', 'asc')
            ->get();

        return Inertia::render('Admin/Events/Event/Qrpresenter', [
            'events' => $events,
        ]);
    }

    /**
     * API untuk memeriksa apakah sebuah scan ID sudah digunakan untuk event tertentu.
     */
    public function checkScanStatus(Request $request, Event $event): JsonResponse
    {
        $request->validate(['scan_id' => 'required|string']);
        $scanId = $request->scan_id;

        $attendance = EventAttendance::where('scanned_barcode_value', $scanId)
            ->where('event_id', $event->id)
            ->first();

        if ($attendance) {
            return response()->json([
                'status' => 'used',
                'attendee_name' => $attendance->name ?? 'Peserta',
            ]);
        }

        return response()->json(['status' => 'pending']);
    }
}

<?php

namespace App\Http\Controllers\Admin\Events;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\RedirectsWithFlash;
use App\Models\Event;
use App\Models\EventAttendee;
use App\Models\EventAttendance;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ScannerController extends Controller
{
    use RedirectsWithFlash;

    public function __construct()
    {
        $this->middleware('permission:scan attendance');
    }

    /**
     * Display the scanner page with an event selection dropdown.
     */
    public function index(): Response
    {
        $events = Event::where('attendance_mode', 'ticketing')
            ->select('id', 'name')
            ->orderBy('name', 'asc')
            ->get();

        return Inertia::render('Admin/Events/Event/Scanner', [
            'events' => $events,
        ]);
    }

    /**
     * API to process the scanned QR Code ticket.
     */
    public function processScan(Request $request, Event $event): JsonResponse
    {
        $validated = $request->validate([
            'ticket_code' => 'required|string',
        ]);

        $ticketCode = $validated['ticket_code'];

        // 1. Find the ticket for this specific event
        $attendee = EventAttendee::where('ticket_code', $ticketCode)
            ->where('event_id', $event->id)
            ->with('user:id,name')
            ->first();

        if (!$attendee) {
            return response()->json(['message' => 'Tiket tidak valid untuk event ini.'], 404);
        }

        // 2. Check if this ticket has already been used for attendance
        $isAlreadyScanned = EventAttendance::where('event_id', $event->id)
            ->where('attendee_id', $attendee->id)
            ->exists();

        if ($isAlreadyScanned) {
            return response()->json([
                'message' => 'Tiket ini sudah pernah digunakan.',
                'attendee_name' => $attendee->user->name
            ], 409); // 409 Conflict
        }

        // 3. If valid, record the attendance
        EventAttendance::create([
            'event_id' => $event->id,
            'user_id' => $attendee->user_id,
            'attendee_id' => $attendee->id,
            'scanned_barcode_value' => $ticketCode, // Save the scanned code
            'name' => $attendee->user->name,
        ]);

        return response()->json([
            'message' => 'Check-in Berhasil!',
            'attendee_name' => $attendee->user->name,
        ]);
    }
}

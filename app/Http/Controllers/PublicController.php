<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\RedirectsWithFlash;
use App\Models\Event;
use App\Models\EventAttendance;
use App\Models\EventStaticQr;
use App\Models\Institution; // 1. Import the Institution model
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class PublicController extends Controller
{
    use RedirectsWithFlash;

    /**
     * Shows the check-in form after a user scans a static QR code.
     */
    public function showCheckInForm(Request $request): Response|RedirectResponse
    {
        $validated = $request->validate(['code' => 'required|string|uuid']);
        $staticQr = EventStaticQr::where('code', $validated['code'])->first();

        if (!$staticQr) {
            return $this->flashRedirect('route', 'home', [], 'error', 'QR Code tidak valid atau tidak ditemukan.');
        }

        $event = Event::find($staticQr->event_id);

        if (!$event || !in_array($event->status, ['registration', 'ongoing'])) {
            return $this->flashRedirect('route', 'home', [], 'error', 'Event ini tidak aktif atau pendaftaran telah ditutup.');
        }

        if ($event->attendance_mode !== 'barcode') {
            return $this->flashRedirect('route', 'home', [], 'error', 'Metode absensi untuk event ini tidak valid.');
        }

        if (Auth::check()) {
            if (EventAttendance::where('event_id', $event->id)->where('user_id', Auth::id())->exists()) {
                return $this->flashRedirect('route', 'home', [], 'success', 'Anda sudah tercatat hadir di event ini. Terima kasih!');
            }
        }

        return Inertia::render('Public/CheckIn', [
            'event' => $event->only('id', 'name'),
            'staticQr' => $staticQr->only('id', 'code', 'label'),
            'institutions' => Institution::all(['id', 'name']),
        ]);
    }

    /**
     * Processes the check-in data from the user.
     */
    public function processCheckIn(Request $request): RedirectResponse
    {
        // 1. Correctly validate all fields sent from the form
        $validated = $request->validate([
            'name' => 'required_if:is_guest,true|string|max:255',
            'is_guest' => 'required|boolean',
            'code' => 'required|string|uuid|exists:event_static_qrs,code',
            'event_id' => 'required|exists:events,id',
            'institution_id' => 'nullable|exists:institutions,id',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $staticQr = EventStaticQr::where('code', $validated['code'])->first();
        $user = Auth::user();

        // Security re-check to prevent race conditions
        if ($user && EventAttendance::where('event_id', $validated['event_id'])->where('user_id', $user->id)->exists()) {
            return $this->redirectBackWithError('Anda sudah tercatat hadir.');
        }

        // --- START: Refactored Logic ---
        $attendeeName = '';
        $attendeeInstitution = null;

        if ($validated['is_guest']) {
            // Logic for guest users
            $attendeeName = $validated['name'];
            if (!empty($validated['institution_id'])) {
                $institution = Institution::find($validated['institution_id']);
                $attendeeInstitution = $institution?->name;
            }
        } elseif ($user) {
            // Logic for authenticated users
            $attendeeName = $user->name;
            // You could optionally fetch the user's institution here if they have one
            // $attendeeInstitution = $user->institution?->name;
        } else {
            // Fallback case if something goes wrong (e.g., user logs out mid-process)
            return $this->redirectBackWithError('Sesi Anda tidak valid. Silakan coba lagi.');
        }
        // --- END: Refactored Logic ---

        try {
            $scannedValue = sprintf(
                '%s - BRCD - %s',
                $staticQr->label,
                strtoupper(Str::random(10))
            );

            EventAttendance::create([
                'event_id' => $validated['event_id'],
                'user_id' => $user?->id,
                'scanned_barcode_value' => $scannedValue,
                'name' => $attendeeName, // Use the prepared name
                'origin_institution' => $attendeeInstitution, // Use the prepared institution
                'latitude' => $validated['latitude'],
                'longitude' => $validated['longitude'],
            ]);

            return $this->flashRedirect('route', 'home', [], 'success', 'Check-in berhasil! Selamat datang.');
        } catch (\Exception $e) {
            return $this->redirectError($e, 'Terjadi kesalahan saat pendaftaran. Silakan coba pindai ulang.');
        }
    }
}

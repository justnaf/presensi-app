<?php

namespace App\Http\Controllers\Admin\Data;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Exports\EventAttendeesExport;
use App\Models\Event;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Str;
use App\Http\Controllers\Traits\RedirectsWithFlash;
use App\Models\EventAttendee;
use Inertia\Inertia;
use Inertia\Response;



class EventAttendeeController extends Controller
{
    use RedirectsWithFlash;
    public function __construct()
    {
        // Middleware untuk mengatur izin akses
        $this->middleware('permission:view events', ['only' => ['showAttendees']]);
        $this->middleware('permission:export event attendees', ['only' => ['export']]);
    }
    /**
     * Menampilkan halaman daftar peserta dengan dropdown event.
     */
    public function showAttendees(Request $request): Response
    {
        $filters = $request->only('search', 'event_id');

        // Ambil semua event yang menggunakan tiket untuk dropdown
        $events = Event::where('attendance_mode', 'ticketing')
            ->select('id', 'name')
            ->orderBy('name', 'asc')
            ->get();

        // Query dasar untuk peserta, awalnya kosong
        $attendeesQuery = EventAttendee::query();

        // Jika event dipilih dari dropdown, filter pesertanya
        if ($request->filled('event_id')) {
            $attendeesQuery->where('event_id', $request->event_id)
                ->with('user:id,name,email,avatar')
                ->when($request->input('search'), function ($query, $search) {
                    $query->whereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
                })
                ->latest('registered_at');
        } else {
            // Jika tidak ada event yang dipilih, jangan tampilkan apa-apa
            $attendeesQuery->whereRaw('1 = 0');
        }

        return Inertia::render('Admin/Data/Attendees', [
            'events' => $events,
            'attendees' => $attendeesQuery->paginate(15)->withQueryString(),
            'filters' => $filters,
        ]);
    }

    /**
     * Memicu unduhan file Excel untuk peserta event.
     */
    public function export(Request $request)
    {
        // Validasi bahwa event_id ada di request
        $request->validate([
            'event_id' => 'required|exists:events,id',
        ]);

        $eventId = $request->event_id;
        $event = Event::find($eventId);
        $fileName = 'daftar-peserta-' . Str::slug($event->name) . '.xlsx';

        return Excel::download(new EventAttendeesExport($eventId), $fileName);
    }
}

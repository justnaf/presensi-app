<?php

namespace App\Http\Controllers\Admin\Data;

use App\Http\Controllers\Controller;
use App\Exports\EventAttendanceExport; // Kita akan buat file ini
use App\Models\Event;
use App\Models\EventAttendance;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Str;

class EventAttendanceController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view attendance');
        $this->middleware('permission:export attendance');
    }

    /**
     * Menampilkan halaman riwayat kehadiran per event.
     */
    public function index(Request $request): Response
    {
        $filters = $request->validate([
            'event_id' => 'nullable|exists:events,id',
        ]);

        $events = Event::select('id', 'name')->orderBy('name', 'asc')->get();

        $attendanceQuery = EventAttendance::query();

        if (!empty($filters['event_id'])) {
            $attendanceQuery->where('event_id', $filters['event_id'])
                // Eager load relasi yang dibutuhkan
                ->with([
                    'user:id,name,email,avatar',
                    'user.institutions:id,name' // Ambil data institusi dari user
                ])
                ->latest('scanned_at');
        } else {
            $attendanceQuery->whereRaw('1 = 0');
        }

        return Inertia::render('Admin/Data/Attendance', [
            'events' => $events,
            'attendance' => $attendanceQuery->paginate(15)->withQueryString(),
            'filters' => $filters,
        ]);
    }

    public function export(Request $request)
    {
        $request->validate(['event_id' => 'required|exists:events,id']);
        $eventId = $request->event_id;
        $event = Event::find($eventId);
        $fileName = 'kehadiran-' . Str::slug($event->name) . '.xlsx';
        return Excel::download(new EventAttendanceExport($eventId), $fileName);
    }
}

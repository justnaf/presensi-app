<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\RedirectsWithFlash;
use App\Models\Event;
use App\Models\EventAttendee;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\EventAttendance;

class EventController extends Controller
{
    use RedirectsWithFlash;

    // ... (Your __construct, index, and show methods remain the same) ...
    public function __construct()
    {
        $this->middleware('permission:view activities', ['only' => ['index', 'show']]);
        $this->middleware('permission:join activities', ['only' => ['join']]);
    }

    public function index(Request $request): Response
    {
        // 1. Fetch ongoing and upcoming events (kode Anda sudah bagus)
        $ongoingEvents = Event::where('status', 'ongoing')
            ->select('id', 'name', 'poster_image', 'start_date', 'end_date', 'attendance_mode')
            ->orderBy('start_date', 'asc')
            ->get();

        $upcomingEvents = Event::where('status', 'registration')
            ->select('id', 'name', 'poster_image', 'start_date', 'end_date', 'attendance_mode')
            ->orderBy('start_date', 'asc')
            ->get();

        // 2. Jika user login, tambahkan informasi tiket dan status registrasi
        if (Auth::check()) {
            // Gabungkan ID dari kedua jenis event untuk satu query efisien
            $allEventIds = $ongoingEvents->pluck('id')->merge($upcomingEvents->pluck('id'));

            if ($allEventIds->isNotEmpty()) {
                // Ambil informasi tiket user dalam satu query.
                // Hasilnya adalah koleksi: [event_id => ticket_id]
                $userTicketsInfo = EventAttendee::where('user_id', Auth::id())
                    ->whereIn('event_id', $allEventIds)
                    ->pluck('id', 'event_id');

                // Fungsi untuk menambahkan atribut ke event
                $addRegistrationInfo = function ($event) use ($userTicketsInfo) {
                    // Cek apakah user terdaftar (apakah event_id ada di keys $userTicketsInfo)
                    $event->is_registered = $userTicketsInfo->has($event->id);
                    // Tambahkan user_ticket_id jika ada, jika tidak, nilainya null
                    $event->user_ticket_id = $userTicketsInfo->get($event->id);
                };

                // Terapkan fungsi di atas ke kedua koleksi event
                $ongoingEvents->each($addRegistrationInfo);
                $upcomingEvents->each($addRegistrationInfo);
            }
        }

        // 3. Render view dengan data yang sudah diperkaya
        return Inertia::render('User/Activities/Index', [
            'ongoingEvents' => $ongoingEvents,
            'upcomingEvents' => $upcomingEvents,
        ]);
    }

    public function show(Event $event): Response
    {
        $event->load(['category:id,name', 'rundowns']);
        $isRegistered = EventAttendee::where('event_id', $event->id)->where('user_id', Auth::id())->exists();

        return Inertia::render('User/Activities/Show', [
            'event' => $event,
            'isRegistered' => $isRegistered,
        ]);
    }


    /**
     * Mendaftarkan pengguna ke event bertiket.
     */
    public function join(Request $request, Event $event): RedirectResponse
    {
        // 1. Validasi Keadaan Event
        if ($event->status !== 'registration') {
            return $this->redirectBackWithError('Pendaftaran untuk event ini belum dibuka atau sudah ditutup.');
        }
        if ($event->attendance_mode !== 'ticketing') {
            return $this->redirectBackWithError('Event ini tidak menggunakan sistem tiket.');
        }

        // 2. Cek apakah sudah terdaftar
        if (EventAttendee::where('event_id', $event->id)->where('user_id', Auth::id())->exists()) {
            return $this->redirectBackWithError('Anda sudah terdaftar di event ini.');
        }

        // 3. Cek Kuota
        if ($event->max_attendees > 0 && EventAttendee::where('event_id', $event->id)->count() >= $event->max_attendees) {
            return $this->redirectBackWithError('Mohon maaf, kuota untuk event ini sudah penuh.');
        }

        // 4. Proses Pendaftaran
        try {
            DB::transaction(function () use ($event) {
                EventAttendee::create([
                    'event_id' => $event->id,
                    'user_id' => Auth::id(),
                    'ticket_code' => 'EVT' . $event->id . 'U' . Auth::id() . '-' . strtoupper(Str::random(12)),
                ]);
            });

            return $this->redirectSuccess('acitivities.index', 'Pendaftaran berhasil! Tiket Anda sudah diterbitkan.', ['event' => $event->id]);
        } catch (\Exception $e) {
            return $this->redirectError($e, 'Terjadi kesalahan saat pendaftaran. Silakan coba lagi.');
        }
    }

    /**
     * Menampilkan daftar tiket yang dimiliki pengguna.
     */
    public function myTickets(Request $request): Response
    {
        $tickets = EventAttendee::where('user_id', Auth::id())
            // Gunakan whereHas untuk memfilter berdasarkan relasi event
            ->whereHas('event', function ($query) {
                $query->where('status', '!=', 'completed');
            })
            // Eager load data event yang dibutuhkan untuk ditampilkan
            ->with('event:id,name,poster_image,start_date,end_date')
            ->latest('registered_at')
            ->paginate(10);

        return Inertia::render('User/Activities/History/Tickets', [
            'tickets' => $tickets,
        ]);
    }

    /**
     * Menampilkan detail tiket yang dimiliki pengguna.
     */
    public function showTickets(Request $request, EventAttendee $ticket): Response
    {
        // Security Check: Ensure the user owns this ticket.
        if ($ticket->user_id !== Auth::id()) {
            abort(403, 'Unauthorized Action');
        }

        // Eager load necessary event and user details
        $ticket->load(['event:id,name,start_date', 'user:id,name']);

        return Inertia::render('User/Activities/History/ShowTickets', [
            'ticket' => $ticket,
        ]);
    }

    /**
     * Menampilkan riwayat kehadiran milik pengguna.
     */
    public function attendanceHistories(Request $request): Response
    {
        $history = EventAttendance::where('user_id', Auth::id())
            // Eager load data event yang dibutuhkan
            ->with('event:id,name')
            ->select('id', 'event_id', 'scanned_at')
            ->orderBy('scanned_at', 'desc')
            ->paginate(15);

        return Inertia::render('User/Activities/History/AttendanceHistories', [
            'history' => $history,
        ]);
    }

    /**
     * Display the QR code scanner page for the user.
     */
    public function qrScanner(Request $request): Response
    {
        return Inertia::render('User/Activities/Scanner');
    }
}

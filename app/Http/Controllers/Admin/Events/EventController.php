<?php

namespace App\Http\Controllers\Admin\Events;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\RedirectsWithFlash;
use App\Http\Requests\Admin\Events\StoreEventRequest;
use App\Http\Requests\Admin\Events\UpdateEventRequest;
use App\Models\Event;
use App\Models\EventCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\EventAttendee;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class EventController extends Controller
{
    use RedirectsWithFlash;

    public function __construct()
    {
        // Apply permissions for each action
        $this->middleware('permission:view events', ['only' => ['index']]);
        $this->middleware('permission:create events', ['only' => ['create', 'store']]);
        $this->middleware('permission:edit events', ['only' => ['edit', 'update', 'updateStatus']]);
        $this->middleware('permission:delete events', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of all events.
     */
    public function index(Request $request): Response
    {
        $filters = $request->only('search');
        $events = Event::with('category:id,name') // Eager load category name
            ->when($request->input('search'), function ($query, $search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Admin/Events/Event/Index', [
            'events' => $events,
            'filters' => $filters,
        ]);
    }

    /**
     * Show the form for creating a new event.
     */
    public function create(): Response
    {
        return Inertia::render('Admin/Events/Event/CreateEdit', [
            'categories' => EventCategory::all(['id', 'name']),
        ]);
    }

    /**
     * Store a newly created event and its rundowns in storage.
     */
    public function store(StoreEventRequest $request): RedirectResponse
    {
        $validatedData = $request->validated();
        $eventData = collect($validatedData)->except('rundowns')->toArray();
        $rundownsData = $validatedData['rundowns'] ?? [];

        if ($request->hasFile('poster_image')) {
            $eventData['poster_image'] = $request->file('poster_image')->store('event_posters', 'public');
        }

        try {
            DB::transaction(function () use ($eventData, $rundownsData) {
                $event = Event::create($eventData);
                if (!empty($rundownsData)) {
                    $event->rundowns()->createMany($rundownsData);
                }
            });
        } catch (\Exception $e) {
            if (isset($eventData['poster_image'])) {
                Storage::disk('public')->delete($eventData['poster_image']);
            }
            // `return` di sini akan menghentikan eksekusi dan mengirim respons error
            return $this->redirectError($e, 'Failed to create event.');
        }

        // FIX: Pindahkan redirect sukses ke sini, di luar blok try...catch.
        // Baris ini hanya akan tercapai jika transaksi berhasil.
        return $this->redirectSuccess('admin.events.index',  "Event created successfully.");
    }

    /**
     * Show the form for editing the specified event.
     */
    public function edit(Event $event): Response
    {
        // Eager load relationships for the edit form
        $event->load('rundowns');
        // Prepare the data for the form, formatting dates correctly
        $eventDataForForm = [
            'id' => $event->id,
            'name' => $event->name,
            'description' => $event->description,
            'about' => $event->about,
            'location_name' => $event->location_name,
            'location_url' => $event->location_url,
            'longitude' => $event->longitude,
            'latitude' => $event->latitude,
            'speaker' => $event->speaker,
            'poster_image' => $event->poster_image,
            'max_attendees' => $event->max_attendees,
            'category_id' => $event->category_id,
            'type' => $event->type,
            'attendance_mode' => $event->attendance_mode,
            // Format dates to YYYY-MM-DD for <input type="date">
            'start_date' => $event->start_date->format('Y-m-d'),
            'end_date' => $event->end_date->format('Y-m-d'),
            // Transform rundowns to format datetimes correctly
            'rundowns' => $event->rundowns->map(function ($rundown) {
                return [
                    'title' => $rundown->title,
                    'description' => $rundown->description,
                    // Format to YYYY-MM-DDTHH:mm for <input type="datetime-local">
                    'start_time' => $rundown->start_time->format('Y-m-d\TH:i'),
                    'end_time' => $rundown->end_time->format('Y-m-d\TH:i'),
                ];
            }),
        ];
        return Inertia::render('Admin/Events/Event/CreateEdit', [
            'event' => $eventDataForForm,
            'categories' => EventCategory::all(['id', 'name']),
        ]);
    }

    /**
     * Update the specified event and sync its rundowns.
     */
    public function update(UpdateEventRequest $request, Event $event): RedirectResponse
    {
        $validatedData = $request->validated();
        $eventData = collect($validatedData)->except(['rundowns', 'poster_image'])->toArray();
        $rundownsData = $validatedData['rundowns'] ?? [];

        if ($request->hasFile('poster_image')) {
            if ($event->poster_image) {
                Storage::disk('public')->delete($event->poster_image);
            }
            $eventData['poster_image'] = $request->file('poster_image')->store('event_posters', 'public');
        }

        try {
            DB::transaction(function () use ($event, $eventData, $rundownsData) {
                $event->update($eventData);
                $event->rundowns()->delete();
                if (!empty($rundownsData)) {
                    $event->rundowns()->createMany($rundownsData);
                }
            });
        } catch (\Exception $e) {
            return $this->redirectError($e, 'Failed to update event.');
        }

        // FIX: Pindahkan redirect sukses ke sini.
        return $this->redirectSuccess('admin.events.index',  'Event updated successfully.');
    }

    /**
     * Remove the specified event from storage.
     */
    public function destroy(Event $event): RedirectResponse
    {
        try {
            if ($event->poster_image) {
                Storage::disk('public')->delete($event->poster_image);
            }
            $event->delete();
            return $this->redirectSuccess('admin.events.index', 'Event deleted successfully.');
        } catch (\Exception $e) {
            return $this->redirectError($e, 'Failed to delete event.');
        }
    }

    /**
     * Update only the status of a specific event.
     */
    public function updateStatus(Request $request, Event $event): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:draft,registration,ongoing,completed',
        ]);

        try {
            $event->update(['status' => $validated['status']]);
            return back()->with('success', 'Status event berhasil diperbarui.');
        } catch (\Exception $e) {
            return $this->redirectError($e, 'Gagal memperbarui status event.');
        }
    }
}

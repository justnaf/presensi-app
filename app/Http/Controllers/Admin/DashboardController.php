<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;
use App\Models\Institution;
use App\Models\User;
use App\Models\Event;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        // Fetch only the distinct start dates of all events
        $eventsForCalendar = Event::select('id', 'name', 'start_date')
            ->orderBy('start_date', 'asc')
            ->get()
            // Format data agar mudah digunakan di frontend
            ->map(function ($event) {
                return [
                    'id' => $event->id,
                    'name' => $event->name,
                    'date' => $event->start_date->format('Y-m-d'), // Format YYYY-MM-DD
                ];
            });

        return Inertia::render('Admin/Dashboard', [
            'stats' => [
                'rolesCount' => Role::count(),
                'institutionsCount' => Institution::count(),
                'usersCount' => User::whereDoesntHave('roles', function ($query) {
                    $query->where('name', 'Administrator');
                })->count(),
            ],
            // Pass the event dates to the frontend
            'eventsForCalendar' => $eventsForCalendar,
        ]);
    }
}

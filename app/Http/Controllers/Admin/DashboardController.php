<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;
use App\Models\Institution;


class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        return Inertia::render('Admin/Dashboard', [
            'stats' => [
                'rolesCount' => Role::count(),
                'institutionsCount' => Institution::count(),
                // You can add more stats here in the future
                // 'permissionsCount' => \Spatie\Permission\Models\Permission::count(),
                // 'usersCount' => \App\Models\User::count(),
            ]
        ]);
    }
}

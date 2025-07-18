<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Event;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {

        return Inertia::render('User/Dashboard');
    }
}

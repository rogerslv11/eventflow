<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $organization = $request->user()->organizations()->first();

        return view('dashboard.index', [
            'organization' => $organization,
            'title' => 'Dashboard',
        ]);
    }
}

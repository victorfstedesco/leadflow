<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\Client;
use App\Models\Planning;

class DashboardController extends Controller
{
    public function index()
    {
        $clientIds = Client::where('user_id', auth()->id())->pluck('id');

        $clients = Client::where('user_id', auth()->id())
            ->withCount('posts')
            ->latest()
            ->get();

        $stats = [
            'clients'   => $clients->count(),
            'posts'     => $clients->sum('posts_count'),
            'campaigns' => Campaign::whereIn('client_id', $clientIds)->count(),
            'campaigns_active' => Campaign::whereIn('client_id', $clientIds)->where('meta_status', 'ACTIVE')->count(),
        ];

        $recentPlannings = Planning::whereIn('client_id', $clientIds)
            ->with(['client', 'goals'])
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard', compact('clients', 'stats', 'recentPlannings'));
    }
}

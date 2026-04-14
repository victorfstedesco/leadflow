<?php

namespace App\Http\Controllers;

use App\Models\Client;

class DashboardController extends Controller
{
    public function index()
    {
        $clients = Client::where('user_id', auth()->id())
            ->withCount('posts')
            ->latest()
            ->get();

        $totalPosts = $clients->sum('posts_count');

        // Dados mockados para KPIs globais
        $stats = [
            'clients' => $clients->count(),
            'posts' => $totalPosts,
            'campaigns' => 8,
            'avg_engagement' => '4.7%',
        ];

        // Atividade recente mockada (últimos posts reais ou fallback mockado)
        $recentPosts = \App\Models\Post::whereIn('client_id', $clients->pluck('id'))
            ->with('client')
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard', compact('clients', 'stats', 'recentPosts'));
    }
}

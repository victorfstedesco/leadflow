<?php

namespace App\Http\Controllers;

use App\Models\Client;

class AnalyticsController extends Controller
{
    public function show(Client $client)
    {
        abort_unless($client->user_id === auth()->id(), 403);

        $client->load(['stages.leads', 'leads']);

        $totalLeads = $client->leads->count();
        $lastStage = $client->stages->last();
        $converted = $lastStage ? $lastStage->leads->count() : 0;
        $conversionRate = $totalLeads > 0 ? round(($converted / $totalLeads) * 100, 1) : 0;

        $bySource = $client->leads->groupBy(fn ($l) => $l->source ?: 'Sem canal')
            ->map->count()
            ->sortDesc();

        $byStage = $client->stages->mapWithKeys(fn ($s) => [$s->name => $s->leads->count()]);

        $stale = $client->leads()
            ->where('updated_at', '<', now()->subDays(7))
            ->where('funnel_stage_id', '!=', $lastStage?->id)
            ->count();

        return view('clients.analytics', compact(
            'client', 'totalLeads', 'converted', 'conversionRate', 'bySource', 'byStage', 'stale'
        ));
    }
}

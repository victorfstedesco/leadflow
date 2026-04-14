<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\FunnelStage;
use App\Models\Lead;
use App\Models\LeadHistory;
use Illuminate\Http\Request;

class LeadController extends Controller
{
    public function create(Client $client)
    {
        abort_unless($client->user_id === auth()->id(), 403);
        $client->load('stages');

        return view('leads.create', compact('client'));
    }

    public function store(Request $request, Client $client)
    {
        abort_unless($client->user_id === auth()->id(), 403);

        $data = $request->validate([
            'name' => 'required|string|max:120',
            'email' => 'nullable|email|max:150',
            'phone' => 'nullable|string|max:30',
            'source' => 'nullable|string|max:60',
            'notes' => 'nullable|string',
            'funnel_stage_id' => 'nullable|exists:funnel_stages,id',
        ]);

        $stageId = $data['funnel_stage_id'] ?? $client->stages()->orderBy('position')->value('id');

        $lead = Lead::create([
            'client_id' => $client->id,
            'funnel_stage_id' => $stageId,
            'name' => $data['name'],
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'] ?? null,
            'source' => $data['source'] ?? null,
            'notes' => $data['notes'] ?? null,
            'entered_at' => now(),
        ]);

        LeadHistory::create([
            'lead_id' => $lead->id,
            'from_stage_id' => null,
            'to_stage_id' => $stageId,
            'moved_at' => now(),
        ]);

        return redirect()->route('clients.show', $client)->with('status', 'Lead adicionado.');
    }

    public function show(Lead $lead)
    {
        abort_unless($lead->client->user_id === auth()->id(), 403);
        $lead->load(['client.stages', 'stage', 'interactions', 'histories.fromStage', 'histories.toStage']);

        return view('leads.show', compact('lead'));
    }

    public function move(Request $request, Lead $lead)
    {
        abort_unless($lead->client->user_id === auth()->id(), 403);

        $data = $request->validate([
            'to_stage_id' => 'required|exists:funnel_stages,id',
        ]);

        $toStage = FunnelStage::findOrFail($data['to_stage_id']);
        abort_unless($toStage->client_id === $lead->client_id, 403);

        if ($lead->funnel_stage_id !== $toStage->id) {
            LeadHistory::create([
                'lead_id' => $lead->id,
                'from_stage_id' => $lead->funnel_stage_id,
                'to_stage_id' => $toStage->id,
                'moved_at' => now(),
            ]);
            $lead->update(['funnel_stage_id' => $toStage->id]);
        }

        return back()->with('status', 'Lead movido para '.$toStage->name.'.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\Client;
use App\Models\Planning;
use App\Models\PlanningGoal;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PlanningController extends Controller
{
    public function index(Client $client)
    {
        abort_unless($client->user_id === auth()->id(), 403);

        $plannings = $client->plannings()
            ->withCount(['goals', 'campaigns'])
            ->with('goals')
            ->latest()
            ->get();

        return view('plannings.index', compact('client', 'plannings'));
    }

    public function create(Client $client)
    {
        abort_unless($client->user_id === auth()->id(), 403);

        $campaigns = $client->campaigns()->orderBy('name')->get();

        return view('plannings.create', compact('client', 'campaigns'));
    }

    public function store(Request $request, Client $client)
    {
        abort_unless($client->user_id === auth()->id(), 403);

        $data = $request->validate([
            'name'         => 'required|string|max:120',
            'period_start' => 'nullable|date',
            'period_end'   => 'nullable|date|after_or_equal:period_start',
            'notes'        => 'nullable|string',
            'campaigns'    => 'required|array|min:1',
            'campaigns.*'  => ['integer', Rule::exists('campaigns', 'id')->where('client_id', $client->id)],
            'goals'        => 'nullable|array',
            'goals.*.category'     => ['required_with:goals', 'string', Rule::in(array_keys(PlanningGoal::CATEGORIES))],
            'goals.*.target_value' => 'required_with:goals|numeric|min:0',
        ]);

        $planning = $client->plannings()->create([
            'name'         => $data['name'],
            'period_start' => $data['period_start'] ?? null,
            'period_end'   => $data['period_end'] ?? null,
            'status'       => 'ativo',
            'notes'        => $data['notes'] ?? null,
        ]);

        // Attach campaigns
        $payload = [];
        foreach ($data['campaigns'] as $id) {
            $payload[$id] = ['local_status' => 'em_execucao'];
        }
        $planning->campaigns()->sync($payload);

        // Create goals
        foreach ($data['goals'] ?? [] as $goal) {
            $planning->goals()->create([
                'category'    => $goal['category'],
                'target_value' => $goal['target_value'],
                'current_value' => 0,
            ]);
        }

        return redirect()->route('plannings.show', [$client, $planning])
            ->with('status', 'Planejamento criado.');
    }

    public function show(Client $client, Planning $planning)
    {
        $this->authorizeAccess($client, $planning);

        $planning->load(['goals', 'campaigns']);

        $availableCampaigns = $client->campaigns()
            ->whereNotIn('id', $planning->campaigns->pluck('id'))
            ->orderBy('name')
            ->get();

        return view('plannings.show', compact('client', 'planning', 'availableCampaigns'));
    }

    public function edit(Client $client, Planning $planning)
    {
        $this->authorizeAccess($client, $planning);

        return view('plannings.edit', compact('client', 'planning'));
    }

    public function update(Request $request, Client $client, Planning $planning)
    {
        $this->authorizeAccess($client, $planning);

        $data = $request->validate([
            'name'         => 'required|string|max:120',
            'period_start' => 'nullable|date',
            'period_end'   => 'nullable|date|after_or_equal:period_start',
            'status'       => 'required|in:ativo,pausado,concluido,arquivado',
            'notes'        => 'nullable|string',
        ]);

        $planning->update($data);

        return redirect()->route('plannings.show', [$client, $planning])
            ->with('status', 'Planejamento atualizado.');
    }

    public function destroy(Client $client, Planning $planning)
    {
        $this->authorizeAccess($client, $planning);

        $planning->delete();

        return redirect()->route('plannings.index', $client)
            ->with('status', 'Planejamento removido.');
    }

    public function attachCampaign(Request $request, Client $client, Planning $planning)
    {
        $this->authorizeAccess($client, $planning);

        $data = $request->validate([
            'campaign_ids'   => 'required|array|min:1',
            'campaign_ids.*' => ['integer', Rule::exists('campaigns', 'id')->where('client_id', $client->id)],
        ]);

        $payload = [];
        foreach ($data['campaign_ids'] as $id) {
            $payload[$id] = ['local_status' => 'em_execucao'];
        }
        $planning->campaigns()->syncWithoutDetaching($payload);

        return redirect()->route('plannings.show', [$client, $planning])
            ->with('status', 'Campanha(s) vinculada(s).');
    }

    public function detachCampaign(Client $client, Planning $planning, Campaign $campaign)
    {
        $this->authorizeAccess($client, $planning);
        abort_unless($campaign->client_id === $client->id, 404);

        $planning->campaigns()->detach($campaign->id);

        return redirect()->route('plannings.show', [$client, $planning])
            ->with('status', 'Campanha desvinculada.');
    }

    public function updateCampaignStatus(Request $request, Client $client, Planning $planning, Campaign $campaign)
    {
        $this->authorizeAccess($client, $planning);
        abort_unless($campaign->client_id === $client->id, 404);

        $data = $request->validate([
            'local_status' => 'required|in:em_execucao,pausada,concluida',
            'notes'        => 'nullable|string|max:500',
        ]);

        $planning->campaigns()->updateExistingPivot($campaign->id, $data);

        return redirect()->route('plannings.show', [$client, $planning])
            ->with('status', 'Status da campanha atualizado.');
    }

    private function authorizeAccess(Client $client, Planning $planning): void
    {
        abort_unless($client->user_id === auth()->id(), 403);
        abort_unless($planning->client_id === $client->id, 404);
    }
}

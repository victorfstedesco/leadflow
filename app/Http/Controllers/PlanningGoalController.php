<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Planning;
use App\Models\PlanningGoal;
use Illuminate\Http\Request;

class PlanningGoalController extends Controller
{
    public function store(Request $request, Client $client, Planning $planning)
    {
        $this->authorizeAccess($client, $planning);

        $data = $this->validatePayload($request);
        $planning->goals()->create($data);

        return redirect()->route('plannings.show', [$client, $planning])
            ->with('status', 'Meta adicionada.');
    }

    public function update(Request $request, Client $client, Planning $planning, PlanningGoal $goal)
    {
        $this->authorizeAccess($client, $planning);
        abort_unless($goal->planning_id === $planning->id, 404);

        $data = $this->validatePayload($request);
        $goal->update($data);

        return redirect()->route('plannings.show', [$client, $planning])
            ->with('status', 'Meta atualizada.');
    }

    public function destroy(Client $client, Planning $planning, PlanningGoal $goal)
    {
        $this->authorizeAccess($client, $planning);
        abort_unless($goal->planning_id === $planning->id, 404);

        $goal->delete();

        return redirect()->route('plannings.show', [$client, $planning])
            ->with('status', 'Meta removida.');
    }

    private function validatePayload(Request $request): array
    {
        return $request->validate([
            'title' => 'required|string|max:160',
            'unit' => 'nullable|string|max:32',
            'target_value' => 'nullable|numeric|min:0',
            'current_value' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:500',
        ]);
    }

    private function authorizeAccess(Client $client, Planning $planning): void
    {
        abort_unless($client->user_id === auth()->id(), 403);
        abort_unless($planning->client_id === $client->id, 404);
    }
}

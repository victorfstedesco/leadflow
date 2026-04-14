<?php

namespace App\Http\Controllers;

use App\Models\Interaction;
use App\Models\Lead;
use Illuminate\Http\Request;

class InteractionController extends Controller
{
    public function store(Request $request, Lead $lead)
    {
        abort_unless($lead->client->user_id === auth()->id(), 403);

        $data = $request->validate([
            'type' => 'required|string|max:40',
            'description' => 'required|string',
        ]);

        Interaction::create([
            'lead_id' => $lead->id,
            'type' => $data['type'],
            'description' => $data['description'],
        ]);

        return back()->with('status', 'Interação registrada.');
    }
}

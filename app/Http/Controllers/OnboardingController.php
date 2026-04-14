<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\FunnelStage;
use Illuminate\Http\Request;

class OnboardingController extends Controller
{
    public function index()
    {
        return view('onboarding.index');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'agency_name' => 'required|string|max:120',
            'first_client' => 'required|string|max:120',
            'channels' => 'nullable|array',
        ]);

        $user = auth()->user();
        $user->update(['name' => $data['agency_name']]);

        $client = Client::create([
            'user_id' => $user->id,
            'name' => $data['first_client'],
            'channels' => $data['channels'] ?? [],
        ]);

        foreach (['Novo', 'Contatado', 'Qualificado', 'Proposta', 'Fechado'] as $i => $stageName) {
            FunnelStage::create([
                'client_id' => $client->id,
                'name' => $stageName,
                'position' => $i,
            ]);
        }

        return redirect()->route('clients.show', $client)->with('status', 'Onboarding concluído!');
    }
}

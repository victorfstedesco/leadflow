<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\Client;
use App\Services\CampaignSyncService;
use Illuminate\Http\Request;
use Throwable;

class CampaignController extends Controller
{
    public function __construct(private CampaignSyncService $sync) {}

    public function index(Client $client)
    {
        abort_unless($client->user_id === auth()->id(), 403);

        $campaigns = $client->campaigns()
            ->withCount('posts')
            ->orderByDesc('start_date')
            ->get();

        $unlinkedPosts = $client->posts()->whereNull('campaign_id')->get();

        return view('clients.campaigns', compact('client', 'campaigns', 'unlinkedPosts'));
    }

    public function sync(Request $request, Client $client)
    {
        abort_unless($client->user_id === auth()->id(), 403);

        if (! $client->isMetaConnected()) {
            return redirect()->route('clients.settings', $client)
                ->with('status', 'Conecte sua conta Meta antes de sincronizar.');
        }

        try {
            $count = $this->sync->syncForClient($client);
            return redirect()->route('clients.campaigns', $client)
                ->with('status', "{$count} campanha(s) sincronizada(s) do Meta.");
        } catch (Throwable $e) {
            return redirect()->route('clients.campaigns', $client)
                ->with('status', 'Erro ao sincronizar com Meta: ' . $e->getMessage());
        }
    }

    public function show(Client $client, Campaign $campaign)
    {
        abort_unless($client->user_id === auth()->id(), 403);
        abort_unless($campaign->client_id === $client->id, 404);

        $campaign->load('posts');

        return view('campaigns.show', compact('client', 'campaign'));
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\Client;
use App\Models\Presentation;
use App\Services\MetaGraphService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PresentationController extends Controller
{
    public function index(Client $client)
    {
        abort_if($client->user_id !== auth()->id(), 403);

        $presentations = Presentation::where('client_id', $client->id)
            ->latest()
            ->get();

        return view('clients.presentations', compact('client', 'presentations'));
    }

    public function store(Request $request, Client $client)
    {
        abort_if($client->user_id !== auth()->id(), 403);

        $request->validate([
            'campaign_ids'   => ['required', 'array', 'min:1'],
            'campaign_ids.*' => ['integer', 'exists:campaigns,id'],
            'title'          => ['nullable', 'string', 'max:120'],
            'since'          => ['nullable', 'date', 'date_format:Y-m-d'],
            'until'          => ['nullable', 'date', 'date_format:Y-m-d', 'after_or_equal:since'],
            'expires_in'     => ['nullable', 'in:1,3,7,30'],
        ]);

        $count = Campaign::whereIn('id', $request->campaign_ids)
            ->where('client_id', $client->id)
            ->count();

        abort_if($count !== count($request->campaign_ids), 403);

        $campaigns = Campaign::whereIn('id', $request->campaign_ids)
            ->where('client_id', $client->id)
            ->get();

        $insights = [];
        if ($request->since && $request->until && $client->isMetaConnected()) {
            $metaService = app(MetaGraphService::class);
            foreach ($campaigns as $campaign) {
                try {
                    $insights[$campaign->id] = $metaService->fetchInsightsForPeriod(
                        $client,
                        $campaign->meta_campaign_id,
                        $request->since,
                        $request->until
                    );
                } catch (\Throwable) {
                    $insights[$campaign->id] = $campaign->insights;
                }
            }
        } else {
            foreach ($campaigns as $campaign) {
                $insights[$campaign->id] = $campaign->insights;
            }
        }

        $presentation = Presentation::create([
            'client_id'    => $client->id,
            'created_by'   => auth()->id(),
            'token'        => Str::random(48),
            'title'        => $request->title,
            'campaign_ids' => $request->campaign_ids,
            'since'        => $request->since,
            'until'        => $request->until,
            'insights'     => $insights,
            'active'       => true,
            'expires_at'   => $request->expires_in
                ? now()->addDays((int) $request->expires_in)
                : null,
        ]);

        return response()->json([
            'url' => route('presentation.show', $presentation->token),
        ]);
    }

    public function deactivate(Client $client, Presentation $presentation)
    {
        abort_if($client->user_id !== auth()->id(), 403);
        abort_if($presentation->client_id !== $client->id, 403);

        $presentation->update(['active' => false]);

        return back()->with('success', 'Apresentação desativada.');
    }

    public function show(string $token)
    {
        $presentation = Presentation::where('token', $token)
            ->with('client')
            ->firstOrFail();

        if (!$presentation->isAvailable()) {
            abort(410);
        }

        $campaigns = Campaign::whereIn('id', $presentation->campaign_ids)
            ->where('client_id', $presentation->client_id)
            ->get();

        return view('presentation', compact('presentation', 'campaigns'));
    }
}

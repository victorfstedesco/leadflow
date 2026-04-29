<?php

namespace App\Services;

use App\Models\Campaign;
use App\Models\Client;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CampaignSyncService
{
    public function __construct(private MetaGraphService $meta) {}

    /**
     * Sync campaigns for a client. Upserts by (client_id, meta_campaign_id).
     *
     * @return int Number of campaigns synced.
     */
    public function syncForClient(Client $client): int
    {
        $remote = $this->meta->fetchCampaigns($client);
        $now = now();

        DB::transaction(function () use ($client, $remote, $now) {
            foreach ($remote as $row) {
                $insights = $this->meta->fetchCampaignInsights($client, $row['id']);

                Campaign::updateOrCreate(
                    [
                        'client_id' => $client->id,
                        'meta_campaign_id' => $row['id'],
                    ],
                    [
                        'name' => $row['name'] ?? 'Sem nome',
                        'objective' => $row['objective'] ?? null,
                        'meta_status' => $row['status'] ?? null,
                        'start_date' => isset($row['start_time']) ? Carbon::parse($row['start_time'])->toDateString() : null,
                        'stop_date' => isset($row['stop_time']) ? Carbon::parse($row['stop_time'])->toDateString() : null,
                        'daily_budget' => isset($row['daily_budget']) ? ((float) $row['daily_budget']) / 100 : null,
                        'lifetime_budget' => isset($row['lifetime_budget']) ? ((float) $row['lifetime_budget']) / 100 : null,
                        'insights' => $insights ?: null,
                        'last_synced_at' => $now,
                    ]
                );
            }

            $client->forceFill(['meta_last_synced_at' => $now])->save();
        });

        return count($remote);
    }

    /**
     * Refresh insights for a single campaign.
     */
    public function syncInsights(Campaign $campaign): void
    {
        $insights = $this->meta->fetchCampaignInsights($campaign->client, $campaign->meta_campaign_id);

        $campaign->update([
            'insights' => $insights ?: null,
            'last_synced_at' => now(),
        ]);
    }
}

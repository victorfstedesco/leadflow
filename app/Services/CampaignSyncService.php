<?php

namespace App\Services;

use App\Models\Campaign;
use App\Models\Client;
use App\Models\PlanningGoal;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CampaignSyncService
{
    public function __construct(private MetaGraphService $meta) {}

    public function syncForClient(Client $client): int
    {
        $remote = $this->meta->fetchCampaigns($client);
        $now = now();

        DB::transaction(function () use ($client, $remote, $now) {
            foreach ($remote as $row) {
                $insights = $this->meta->fetchCampaignInsights($client, $row['id']);

                $campaign = Campaign::updateOrCreate(
                    [
                        'client_id'        => $client->id,
                        'meta_campaign_id' => $row['id'],
                    ],
                    [
                        'name'             => $row['name'] ?? 'Sem nome',
                        'objective'        => $row['objective'] ?? null,
                        'meta_status'      => $row['status'] ?? null,
                        'start_date'       => isset($row['start_time']) ? Carbon::parse($row['start_time'])->toDateString() : null,
                        'stop_date'        => isset($row['stop_time']) ? Carbon::parse($row['stop_time'])->toDateString() : null,
                        'daily_budget'     => isset($row['daily_budget']) ? ((float) $row['daily_budget']) / 100 : null,
                        'lifetime_budget'  => isset($row['lifetime_budget']) ? ((float) $row['lifetime_budget']) / 100 : null,
                        'insights'         => $insights ?: null,
                        'last_synced_at'   => $now,
                    ]
                );

                if ($insights) {
                    $this->updatePlanningGoals($campaign, $insights);
                }
            }

            $client->forceFill(['meta_last_synced_at' => $now])->save();
        });

        return count($remote);
    }

    public function syncInsights(Campaign $campaign): void
    {
        $insights = $this->meta->fetchCampaignInsights($campaign->client, $campaign->meta_campaign_id);

        $campaign->update([
            'insights'       => $insights ?: null,
            'last_synced_at' => now(),
        ]);

        if ($insights) {
            $this->updatePlanningGoals($campaign, $insights);
        }
    }

    private function updatePlanningGoals(Campaign $campaign, array $insights): void
    {
        // Load plannings that have this campaign linked
        $planningIds = $campaign->plannings()->pluck('plannings.id');
        if ($planningIds->isEmpty()) {
            return;
        }

        // For each planning, get all campaign insights to aggregate
        foreach ($planningIds as $planningId) {
            $goals = PlanningGoal::where('planning_id', $planningId)
                ->whereNotNull('category')
                ->get();

            if ($goals->isEmpty()) {
                continue;
            }

            // Aggregate insights across ALL campaigns linked to this planning
            $campaignInsights = DB::table('planning_campaign')
                ->join('campaigns', 'campaigns.id', '=', 'planning_campaign.campaign_id')
                ->where('planning_campaign.planning_id', $planningId)
                ->whereNotNull('campaigns.insights')
                ->pluck('campaigns.insights');

            $aggregated = $this->aggregateInsights($campaignInsights->map(fn($i) => json_decode($i, true))->filter()->values()->all());

            foreach ($goals as $goal) {
                $field = \App\Models\PlanningGoal::CATEGORIES[$goal->category]['field'] ?? null;
                if ($field && isset($aggregated[$field])) {
                    $goal->update(['current_value' => $aggregated[$field]]);
                }
            }
        }
    }

    private function aggregateInsights(array $insightsList): array
    {
        if (empty($insightsList)) {
            return [];
        }

        $sums = ['reach' => 0, 'impressions' => 0, 'clicks' => 0, 'spend' => 0];
        $ctrTotal = 0;
        $cpcTotal = 0;
        $ctrWeight = 0; // weighted by impressions
        $cpcWeight = 0; // weighted by clicks
        $count = 0;

        foreach ($insightsList as $ins) {
            $sums['reach']       += (float) ($ins['reach'] ?? 0);
            $sums['impressions'] += (float) ($ins['impressions'] ?? 0);
            $sums['clicks']      += (float) ($ins['clicks'] ?? 0);
            $sums['spend']       += (float) ($ins['spend'] ?? 0);

            $imp = (float) ($ins['impressions'] ?? 0);
            $clk = (float) ($ins['clicks'] ?? 0);

            if (isset($ins['ctr']) && $imp > 0) {
                $ctrTotal  += (float) $ins['ctr'] * $imp;
                $ctrWeight += $imp;
            }
            if (isset($ins['cpc']) && $clk > 0) {
                $cpcTotal  += (float) $ins['cpc'] * $clk;
                $cpcWeight += $clk;
            }
            $count++;
        }

        $result = $sums;
        $result['ctr'] = $ctrWeight > 0 ? round($ctrTotal / $ctrWeight, 4) : 0;
        $result['cpc'] = $cpcWeight > 0 ? round($cpcTotal / $cpcWeight, 4) : 0;

        return $result;
    }
}

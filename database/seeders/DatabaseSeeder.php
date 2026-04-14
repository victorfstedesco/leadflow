<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\FunnelStage;
use App\Models\Interaction;
use App\Models\Lead;
use App\Models\LeadHistory;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::create([
            'name' => 'Agência Demo',
            'email' => 'admin@leadflow.test',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        $clientsData = [
            [
                'name' => 'E-commerce Moda Urbana',
                'channels' => ['Meta Ads', 'Google Ads', 'TikTok Ads'],
            ],
            [
                'name' => 'Clínica Dr. João',
                'channels' => ['Meta Ads', 'Indicação', 'WhatsApp'],
            ],
        ];

        $stageNames = ['Novo', 'Contatado', 'Qualificado', 'Proposta', 'Fechado'];
        $sources = ['Meta Ads', 'Google Ads', 'TikTok Ads', 'Indicação', 'Orgânico'];
        $firstNames = ['Ana', 'Bruno', 'Carla', 'Daniel', 'Eduarda', 'Felipe', 'Gabriela', 'Heitor', 'Isabela', 'João', 'Karina', 'Lucas', 'Mariana', 'Nathan', 'Olívia'];
        $lastNames = ['Silva', 'Souza', 'Oliveira', 'Costa', 'Pereira', 'Almeida', 'Ribeiro', 'Martins'];

        foreach ($clientsData as $data) {
            $client = Client::create([
                'user_id' => $user->id,
                'name' => $data['name'],
                'channels' => $data['channels'],
            ]);

            $stages = [];
            foreach ($stageNames as $i => $stageName) {
                $stages[] = FunnelStage::create([
                    'client_id' => $client->id,
                    'name' => $stageName,
                    'position' => $i,
                ]);
            }

            for ($i = 0; $i < 15; $i++) {
                $stage = $stages[array_rand($stages)];
                $enteredAt = now()->subDays(rand(0, 30))->subHours(rand(0, 23));

                $lead = Lead::create([
                    'client_id' => $client->id,
                    'funnel_stage_id' => $stages[0]->id,
                    'name' => $firstNames[array_rand($firstNames)].' '.$lastNames[array_rand($lastNames)],
                    'email' => strtolower(substr(str_replace(' ', '.', $firstNames[array_rand($firstNames)]), 0, 10)).rand(10, 99).'@email.com',
                    'phone' => '(11) 9'.rand(1000, 9999).'-'.rand(1000, 9999),
                    'source' => $sources[array_rand($sources)],
                    'entered_at' => $enteredAt,
                    'created_at' => $enteredAt,
                    'updated_at' => $enteredAt,
                ]);

                LeadHistory::create([
                    'lead_id' => $lead->id,
                    'from_stage_id' => null,
                    'to_stage_id' => $stages[0]->id,
                    'moved_at' => $enteredAt,
                ]);

                $targetPosition = $stage->position;
                $currentStage = $stages[0];
                for ($p = 1; $p <= $targetPosition; $p++) {
                    $movedAt = $enteredAt->copy()->addDays($p);
                    LeadHistory::create([
                        'lead_id' => $lead->id,
                        'from_stage_id' => $currentStage->id,
                        'to_stage_id' => $stages[$p]->id,
                        'moved_at' => $movedAt,
                    ]);
                    $currentStage = $stages[$p];
                }

                $lead->update(['funnel_stage_id' => $stage->id]);

                if (rand(0, 1)) {
                    Interaction::create([
                        'lead_id' => $lead->id,
                        'type' => ['Ligação', 'WhatsApp', 'E-mail'][rand(0, 2)],
                        'description' => 'Contato inicial realizado. Cliente demonstrou interesse.',
                        'created_at' => $enteredAt->copy()->addHours(2),
                        'updated_at' => $enteredAt->copy()->addHours(2),
                    ]);
                }
            }
        }
    }
}

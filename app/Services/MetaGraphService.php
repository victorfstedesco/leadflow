<?php

namespace App\Services;

use App\Models\Client;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class MetaGraphService
{
    public function __construct(
        private ?string $version = null,
    ) {
        $this->version = $this->version ?: config('services.meta.graph_version', 'v21.0');
    }

    private function baseUrl(): string
    {
        return "https://graph.facebook.com/{$this->version}";
    }

    /**
     * Build the OAuth authorization URL for Facebook Login.
     */
    public function buildAuthorizationUrl(string $state, ?string $redirectUri = null): string
    {
        $params = http_build_query([
            'client_id' => config('services.meta.app_id'),
            'redirect_uri' => $redirectUri ?: config('services.meta.redirect_uri'),
            'state' => $state,
            'scope' => 'ads_read,ads_management,pages_show_list,business_management',
            'response_type' => 'code',
        ]);

        return "https://www.facebook.com/{$this->version}/dialog/oauth?{$params}";
    }

    /**
     * Exchange a short-lived OAuth code for an access token.
     *
     * @return array{access_token:string, token_type?:string, expires_in?:int}
     */
    public function exchangeCodeForToken(string $code, ?string $redirectUri = null): array
    {
        $response = Http::get($this->baseUrl() . '/oauth/access_token', [
            'client_id' => config('services.meta.app_id'),
            'client_secret' => config('services.meta.app_secret'),
            'redirect_uri' => $redirectUri ?: config('services.meta.redirect_uri'),
            'code' => $code,
        ]);

        if ($response->failed()) {
            throw new RuntimeException('Falha ao trocar code por token: ' . $response->body());
        }

        return $response->json();
    }

    /**
     * Convert a short-lived token into a long-lived one (~60 days).
     *
     * @return array{access_token:string, token_type?:string, expires_in?:int}
     */
    public function getLongLivedToken(string $shortToken): array
    {
        $response = Http::get($this->baseUrl() . '/oauth/access_token', [
            'grant_type' => 'fb_exchange_token',
            'client_id' => config('services.meta.app_id'),
            'client_secret' => config('services.meta.app_secret'),
            'fb_exchange_token' => $shortToken,
        ]);

        if ($response->failed()) {
            throw new RuntimeException('Falha ao trocar token de longa duração: ' . $response->body());
        }

        return $response->json();
    }

    /**
     * List ad accounts for the authenticated user.
     *
     * @return array<int, array{id:string, account_id:string, name?:string, currency?:string}>
     */
    public function listAdAccounts(string $accessToken): array
    {
        $response = Http::get($this->baseUrl() . '/me/adaccounts', [
            'access_token' => $accessToken,
            'fields' => 'id,account_id,name,currency,account_status',
        ]);

        if ($response->failed()) {
            throw new RuntimeException('Falha ao listar contas de anúncio: ' . $response->body());
        }

        return $response->json('data', []);
    }

    /**
     * Fetch /me to discover the Meta user id.
     */
    public function fetchMe(string $accessToken): array
    {
        $response = Http::get($this->baseUrl() . '/me', [
            'access_token' => $accessToken,
            'fields' => 'id,name,email',
        ]);

        if ($response->failed()) {
            throw new RuntimeException('Falha ao buscar usuário: ' . $response->body());
        }

        return $response->json();
    }

    /**
     * Fetch all campaigns for the client's selected ad account.
     *
     * @return array<int, array<string, mixed>>
     */
    public function fetchCampaigns(Client $client): array
    {
        if (! $client->isMetaConnected()) {
            throw new RuntimeException('Cliente não está conectado ao Meta.');
        }

        $adAccountId = $client->meta_ad_account_id;
        $response = Http::get($this->baseUrl() . "/{$adAccountId}/campaigns", [
            'access_token' => $client->meta_access_token,
            'fields' => 'id,name,objective,status,start_time,stop_time,daily_budget,lifetime_budget',
            'limit' => 100,
        ]);

        if ($response->failed()) {
            throw new RuntimeException('Falha ao buscar campanhas: ' . $response->body());
        }

        return $response->json('data', []);
    }

    /**
     * Fetch insights (reach, impressions, clicks, ctr, cpc, spend) for a single campaign.
     */
    public function fetchCampaignInsights(Client $client, string $metaCampaignId): array
    {
        if (! $client->isMetaConnected()) {
            throw new RuntimeException('Cliente não está conectado ao Meta.');
        }

        $response = Http::get($this->baseUrl() . "/{$metaCampaignId}/insights", [
            'access_token' => $client->meta_access_token,
            'fields' => 'reach,impressions,clicks,ctr,cpc,spend,frequency',
            'date_preset' => 'lifetime',
        ]);

        if ($response->failed()) {
            return [];
        }

        $rows = $response->json('data', []);

        return $rows[0] ?? [];
    }
}

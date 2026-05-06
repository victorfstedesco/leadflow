<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Services\MetaGraphService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MetaAuthController extends Controller
{
    public function __construct(private MetaGraphService $meta) {}

    /**
     * Redirect the user to Facebook OAuth authorization.
     */
    public function redirect(Client $client)
    {
        abort_unless($client->user_id === auth()->id(), 403);

        $state = Str::random(40);
        session([
            'meta_oauth_state' => $state,
            'meta_oauth_client_id' => $client->id,
        ]);

        return redirect()->away($this->meta->buildAuthorizationUrl($state));
    }

    /**
     * Handle the OAuth callback from Facebook.
     */
    public function callback(Request $request)
    {
        $expectedState = session('meta_oauth_state');
        $clientId = session('meta_oauth_client_id');

        abort_unless($expectedState && $request->query('state') === $expectedState, 403, 'State inválido.');
        abort_unless($clientId, 400, 'Sessão OAuth expirada.');

        $client = Client::where('id', $clientId)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        if ($request->query('error')) {
            return redirect()->route('clients.settings', $client)
                ->with('status', 'Conexão com Meta cancelada: ' . $request->query('error_description'));
        }

        $code = $request->query('code');
        abort_unless($code, 400, 'Code ausente.');

        $short = $this->meta->exchangeCodeForToken($code);
        $long = $this->meta->getLongLivedToken($short['access_token']);

        $me = $this->meta->fetchMe($long['access_token']);

        $client->update([
            'meta_user_id' => $me['id'] ?? null,
            'meta_access_token' => $long['access_token'],
            'meta_token_expires_at' => isset($long['expires_in']) ? now()->addSeconds((int) $long['expires_in']) : null,
        ]);

        session()->forget(['meta_oauth_state', 'meta_oauth_client_id']);

        return redirect()->route('clients.settings', $client)
            ->with('status', 'Conta Meta conectada. Selecione a conta de anúncios.')
            ->with('meta_select_ad_account', true);
    }

    /**
     * Save the chosen ad account id.
     */
    public function selectAdAccount(Request $request, Client $client)
    {
        abort_unless($client->user_id === auth()->id(), 403);

        $data = $request->validate([
            'meta_ad_account_id' => 'required|string|max:80',
        ]);

        $client->update(['meta_ad_account_id' => $data['meta_ad_account_id']]);

        return redirect()->route('clients.settings', $client)
            ->with('status', 'Conta de anúncios selecionada.');
    }

    /**
     * List ad accounts (used by settings view via Ajax/inline).
     */
    public function listAdAccounts(Client $client)
    {
        abort_unless($client->user_id === auth()->id(), 403);
        abort_unless($client->meta_access_token, 400, 'Cliente não conectado ao Meta.');

        $accounts = $this->meta->listAdAccounts($client->meta_access_token);

        return response()->json(['data' => $accounts]);
    }

    /**
     * Disconnect the Meta account, clearing credentials. Campaigns are kept.
     */
    public function disconnect(Client $client)
    {
        abort_unless($client->user_id === auth()->id(), 403);

        $client->update([
            'meta_user_id' => null,
            'meta_access_token' => null,
            'meta_ad_account_id' => null,
            'meta_token_expires_at' => null,
        ]);

        return redirect()->route('clients.settings', $client)
            ->with('status', 'Conta Meta desconectada.');
    }
}

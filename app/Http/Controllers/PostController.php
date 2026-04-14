<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function create(Client $client)
    {
        abort_unless($client->user_id === auth()->id(), 403);

        $campaigns = $this->getCampaignNames($client);

        return view('posts.create', compact('client', 'campaigns'));
    }

    public function store(Request $request, Client $client)
    {
        abort_unless($client->user_id === auth()->id(), 403);

        $data = $request->validate([
            'title' => 'required|string|max:200',
            'copy' => 'nullable|string',
            'content_type' => 'required|string|in:imagem,video,carrossel,story,reels',
            'objective' => 'nullable|string|in:engajamento,conversao,branding,educacao',
            'campaign' => 'nullable|string|max:120',
        ]);

        $client->posts()->create($data);

        return redirect()->route('clients.posts', $client)->with('status', 'Postagem criada com sucesso.');
    }

    public function edit(Client $client, Post $post)
    {
        abort_unless($client->user_id === auth()->id(), 403);
        abort_unless($post->client_id === $client->id, 404);

        $campaigns = $this->getCampaignNames($client);

        return view('posts.edit', compact('client', 'post', 'campaigns'));
    }

    public function update(Request $request, Client $client, Post $post)
    {
        abort_unless($client->user_id === auth()->id(), 403);
        abort_unless($post->client_id === $client->id, 404);

        $data = $request->validate([
            'title' => 'required|string|max:200',
            'copy' => 'nullable|string',
            'content_type' => 'required|string|in:imagem,video,carrossel,story,reels',
            'objective' => 'nullable|string|in:engajamento,conversao,branding,educacao',
            'campaign' => 'nullable|string|max:120',
        ]);

        $post->update($data);

        return redirect()->route('clients.posts', $client)->with('status', 'Postagem atualizada com sucesso.');
    }

    public function destroy(Client $client, Post $post)
    {
        abort_unless($client->user_id === auth()->id(), 403);
        abort_unless($post->client_id === $client->id, 404);

        $post->delete();

        return redirect()->route('clients.posts', $client)->with('status', 'Postagem removida.');
    }

    /**
     * Link or unlink a post to a campaign (used from the campaigns page).
     */
    public function linkCampaign(Request $request, Client $client, Post $post)
    {
        abort_unless($client->user_id === auth()->id(), 403);
        abort_unless($post->client_id === $client->id, 404);

        $data = $request->validate([
            'campaign' => 'nullable|string|max:120',
        ]);

        $post->update(['campaign' => $data['campaign'] ?: null]);

        return redirect()->route('clients.campaigns', $client)->with('status', 'Postagem atualizada.');
    }

    /**
     * Returns campaign names for a given client based on niche.
     */
    private function getCampaignNames(Client $client): array
    {
        $niche = $client->niche ?? 'Geral';

        return match ($niche) {
            'Saúde' => ['Campanha Prevenção — Outubro', 'Lançamento Teleconsulta'],
            'Gastronomia' => ['Festival de Inverno', 'Delivery Launch'],
            'Moda' => ['Coleção Verão 2026', 'Black Friday Antecipada'],
            default => ['Campanha de Lançamento', 'Campanha Institucional'],
        };
    }
}

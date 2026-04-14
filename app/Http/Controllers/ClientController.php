<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\FunnelStage;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index()
    {
        $clients = Client::where('user_id', auth()->id())
            ->withCount('leads')
            ->with(['stages' => fn ($q) => $q->withCount('leads')])
            ->latest()
            ->get();

        return view('clients.index', compact('clients'));
    }

    public function create()
    {
        return view('clients.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:120',
            'channels' => 'nullable|array',
            'notes' => 'nullable|string',
        ]);

        $client = Client::create([
            'user_id' => auth()->id(),
            'name' => $data['name'],
            'channels' => $data['channels'] ?? [],
            'notes' => $data['notes'] ?? null,
        ]);

        $defaultStages = ['Novo', 'Contatado', 'Qualificado', 'Proposta', 'Fechado'];
        foreach ($defaultStages as $i => $stageName) {
            FunnelStage::create([
                'client_id' => $client->id,
                'name' => $stageName,
                'position' => $i,
            ]);
        }

        return redirect()->route('clients.show', $client)->with('status', 'Cliente criado com sucesso.');
    }

    public function show(Client $client)
    {
        abort_unless($client->user_id === auth()->id(), 403);

        $client->load(['stages.leads', 'leads']);

        // Calculate metrics
        $totalLeads = $client->leads->count();
        $lastStage = $client->stages->last();
        $converted = $lastStage ? $lastStage->leads->count() : 0;
        $conversionRate = $totalLeads > 0 ? round(($converted / $totalLeads) * 100, 1) : 0;

        // Static posts for now (in the future this will come from a Post model)
        $activePosts = $this->getStaticPosts($client);

        return view('clients.show', compact('client', 'totalLeads', 'conversionRate', 'activePosts'));
    }

    public function posts(Client $client)
    {
        abort_unless($client->user_id === auth()->id(), 403);

        $posts = $this->getStaticPosts($client, true);

        return view('clients.posts', compact('client', 'posts'));
    }

    public function settings(Client $client)
    {
        abort_unless($client->user_id === auth()->id(), 403);

        return view('clients.settings', compact('client'));
    }

    public function update(Request $request, Client $client)
    {
        abort_unless($client->user_id === auth()->id(), 403);

        $data = $request->validate([
            'name' => 'required|string|max:120',
            'channels' => 'nullable|array',
            'notes' => 'nullable|string',
        ]);

        $client->update([
            'name' => $data['name'],
            'channels' => $data['channels'] ?? [],
            'notes' => $data['notes'] ?? null,
        ]);

        return redirect()->route('clients.settings', $client)->with('status', 'Cliente atualizado com sucesso.');
    }

    public function destroy(Client $client)
    {
        abort_unless($client->user_id === auth()->id(), 403);

        $client->delete();

        return redirect()->route('clients.index')->with('status', 'Cliente removido.');
    }

    /**
     * Returns static placeholder posts for demonstration.
     * In the future, replace this with a Post model query.
     */
    private function getStaticPosts(Client $client, bool $includeAll = false): array
    {
        $allPosts = [
            [
                'title' => 'Carousel: 5 dicas para aumentar vendas',
                'description' => 'Conteúdo educativo em formato carousel com dicas práticas de vendas para o público-alvo.',
                'platform' => 'Instagram',
                'platform_icon' => 'photo_camera',
                'platform_bg' => 'bg-gradient-to-br from-pink-50 to-purple-50 border border-purple-100',
                'gradient' => 'from-pink-50 to-purple-100',
                'status' => 'Publicada',
                'date' => '12 Abr 2026',
                'likes' => 284,
                'comments' => 43,
            ],
            [
                'title' => 'Vídeo: Depoimento de cliente satisfeito',
                'description' => 'Vídeo curto com depoimento real de cliente, focado em prova social e credibilidade.',
                'platform' => 'Instagram',
                'platform_icon' => 'photo_camera',
                'platform_bg' => 'bg-gradient-to-br from-pink-50 to-purple-50 border border-purple-100',
                'gradient' => 'from-orange-50 to-pink-100',
                'status' => 'Agendada',
                'date' => '15 Abr 2026',
                'likes' => 0,
                'comments' => 0,
            ],
            [
                'title' => 'Post: Promoção de lançamento',
                'description' => 'Anúncio de promoção especial com CTA direto para WhatsApp comercial.',
                'platform' => 'Facebook',
                'platform_icon' => 'thumb_up',
                'platform_bg' => 'bg-gradient-to-br from-blue-50 to-indigo-50 border border-blue-100',
                'gradient' => 'from-blue-50 to-indigo-100',
                'status' => 'Em produção',
                'date' => '16 Abr 2026',
                'likes' => 0,
                'comments' => 0,
            ],
            [
                'title' => 'Story: Bastidores do atendimento',
                'description' => 'Conteúdo de bastidores mostrando o dia a dia, humanizando a marca.',
                'platform' => 'Instagram',
                'platform_icon' => 'photo_camera',
                'platform_bg' => 'bg-gradient-to-br from-pink-50 to-purple-50 border border-purple-100',
                'gradient' => 'from-green-50 to-emerald-100',
                'status' => 'Rascunho',
                'date' => '17 Abr 2026',
                'likes' => 0,
                'comments' => 0,
            ],
            [
                'title' => 'Campanha: Black Friday antecipada',
                'description' => 'Série de anúncios para Meta Ads com foco em conversão direta.',
                'platform' => 'Meta Ads',
                'platform_icon' => 'campaign',
                'platform_bg' => 'bg-gradient-to-br from-sky-50 to-blue-50 border border-sky-100',
                'gradient' => 'from-sky-50 to-blue-100',
                'status' => 'Em produção',
                'date' => '18 Abr 2026',
                'likes' => 0,
                'comments' => 0,
            ],
            [
                'title' => 'Vídeo: Tutorial do produto',
                'description' => 'Vídeo demonstrativo mostrando funcionalidades do produto para TikTok.',
                'platform' => 'TikTok',
                'platform_icon' => 'smart_display',
                'platform_bg' => 'bg-gray-50 border border-gray-200',
                'gradient' => 'from-gray-50 to-gray-200',
                'status' => 'Publicada',
                'date' => '10 Abr 2026',
                'likes' => 1520,
                'comments' => 87,
            ],
        ];

        if ($includeAll) {
            return $allPosts;
        }

        // For dashboard, return only active (non-published) posts
        return array_values(array_filter($allPosts, fn ($p) => $p['status'] !== 'Publicada'));
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index()
    {
        $clients = Client::where('user_id', auth()->id())
            ->withCount('posts')
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
            'niche' => 'nullable|string|max:60',
            'channels' => 'nullable|array',
            'notes' => 'nullable|string',
        ]);

        Client::create([
            'user_id' => auth()->id(),
            'name' => $data['name'],
            'niche' => $data['niche'] ?? null,
            'channels' => $data['channels'] ?? [],
            'notes' => $data['notes'] ?? null,
        ]);

        return redirect()->route('clients.index')->with('status', 'Cliente criado com sucesso.');
    }

    public function show(Client $client)
    {
        abort_unless($client->user_id === auth()->id(), 403);

        $client->load('posts');

        $totalPosts = $client->posts->count();
        $linkedPosts = $client->posts->whereNotNull('campaign_id')->count();
        $recentPosts = $client->posts->sortByDesc('created_at')->take(5)->values();

        // Métricas mockadas de social media
        $metrics = [
            'reach' => '24.8K',
            'reach_change' => '+12%',
            'engagement' => '4.3%',
            'engagement_change' => '+0.8%',
            'posts_count' => $totalPosts,
            'campaigns_active' => $client->campaigns()->where('meta_status', 'ACTIVE')->count(),
        ];

        return view('clients.show', compact('client', 'totalPosts', 'linkedPosts', 'recentPosts', 'metrics'));
    }

    public function posts(Client $client)
    {
        abort_unless($client->user_id === auth()->id(), 403);

        $posts = $client->posts()->latest()->get();
        $suggestions = $this->getPostSuggestions($client->niche);

        return view('clients.posts', compact('client', 'posts', 'suggestions'));
    }

    public function insights(Client $client)
    {
        abort_unless($client->user_id === auth()->id(), 403);

        $client->load('posts');

        // KPIs mockados
        $kpis = [
            'best_content' => 'Carrossel',
            'best_time' => '18h–20h',
            'avg_engagement' => '4.7%',
            'follower_growth' => '+320',
        ];

        // Performance por tipo de conteúdo (mockado)
        $contentPerformance = [
            ['type' => 'Carrossel', 'engagement' => 6.2, 'reach' => 8400],
            ['type' => 'Reels', 'engagement' => 5.8, 'reach' => 12300],
            ['type' => 'Imagem', 'engagement' => 3.1, 'reach' => 4200],
            ['type' => 'Story', 'engagement' => 2.4, 'reach' => 3100],
        ];

        // Insights contextuais baseados no nicho
        $contextualInsights = $this->getNicheInsights($client->niche);

        return view('clients.insights', compact('client', 'kpis', 'contentPerformance', 'contextualInsights'));
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
            'niche' => 'nullable|string|max:60',
            'channels' => 'nullable|array',
            'notes' => 'nullable|string',
        ]);

        $client->update([
            'name' => $data['name'],
            'niche' => $data['niche'] ?? null,
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

    private function getNicheInsights(?string $niche): array
    {
        $insights = [
            'Saúde' => [
                ['icon' => 'local_hospital', 'title' => 'Conteúdo educativo performa melhor', 'text' => 'Posts educativos sobre prevenção e cuidados têm 3x mais engajamento que posts promocionais. Invista em carrosséis com dicas de saúde.'],
                ['icon' => 'schedule', 'title' => 'Melhor horário: 7h–9h', 'text' => 'O público de saúde engaja mais no início da manhã. Agende posts informativos para esse período.'],
                ['icon' => 'videocam', 'title' => 'Depoimentos em vídeo convertem', 'text' => 'Vídeos de depoimentos de pacientes têm taxa de conversão 45% maior para agendamento de consultas.'],
            ],
            'Gastronomia' => [
                ['icon' => 'restaurant', 'title' => 'Bastidores da cozinha engajam', 'text' => 'Reels mostrando o preparo dos pratos performam 45% melhor que fotos estáticas do menu. Aposte em conteúdo autêntico.'],
                ['icon' => 'schedule', 'title' => 'Poste entre 11h–13h', 'text' => 'Conteúdos publicados no horário de almoço geram 2x mais interações e salvamentos.'],
                ['icon' => 'trending_up', 'title' => 'Promoções temáticas funcionam', 'text' => 'Campanhas vinculadas a datas comemorativas (Dia dos Namorados, inverno) tiveram ROI 60% superior.'],
            ],
            'Moda' => [
                ['icon' => 'checkroom', 'title' => 'Carrosséis de looks geram saves', 'text' => 'Carrosséis com montagem de looks geram 2.5x mais salvamentos que fotos de produto isoladas.'],
                ['icon' => 'schedule', 'title' => 'Melhor horário: 18h–20h', 'text' => 'O público de moda engaja mais no fim do dia. Priorize Reels e carrosséis nesse horário.'],
                ['icon' => 'smart_display', 'title' => 'Try-on hauls no Reels', 'text' => 'Vídeos de "try-on" têm taxa de compartilhamento 3x maior e impulsionam tráfego para o e-commerce.'],
            ],
        ];

        return $insights[$niche] ?? [
            ['icon' => 'lightbulb', 'title' => 'Diversifique os formatos', 'text' => 'Alternar entre carrosséis, reels e stories mantém o algoritmo ativo e aumenta o alcance orgânico em até 35%.'],
            ['icon' => 'schedule', 'title' => 'Consistência é chave', 'text' => 'Contas que postam ao menos 4x por semana têm crescimento de seguidores 2x mais rápido.'],
            ['icon' => 'analytics', 'title' => 'Analise e adapte', 'text' => 'Revise as métricas semanalmente e ajuste a estratégia de conteúdo com base nos tipos que mais performam.'],
        ];
    }

    private function getPostSuggestions(?string $niche): array
    {
        $suggestions = [
            'Saúde' => [
                ['type' => 'carrossel', 'title' => '7 sinais de que você precisa ir ao médico', 'reason' => 'Carrosséis educativos sobre saúde preventiva geram 3.2x mais salvamentos nesse nicho. Aposte em linguagem acessível.', 'objective' => 'Educação'],
                ['type' => 'reels', 'title' => 'Um dia na rotina do consultório', 'reason' => 'Conteúdos de bastidores humanizam a marca e aumentam a confiança. Reels desse tipo têm 48% mais compartilhamentos.', 'objective' => 'Branding'],
                ['type' => 'imagem', 'title' => 'Mito ou verdade: série semanal', 'reason' => 'Séries com formato fixo criam hábito no público. Posts de "mito ou verdade" geram alta taxa de comentários no nicho saúde.', 'objective' => 'Engajamento'],
                ['type' => 'video', 'title' => 'Depoimento de paciente satisfeito', 'reason' => 'Prova social em vídeo converte 45% mais agendamentos. Use legendas — 85% dos vídeos são assistidos sem som.', 'objective' => 'Conversão'],
            ],
            'Gastronomia' => [
                ['type' => 'reels', 'title' => 'Receita do prato mais pedido em 60s', 'reason' => 'Reels de receitas rápidas são o formato nº1 em gastronomia. Vídeos de até 60s têm 2x mais views que longos.', 'objective' => 'Engajamento'],
                ['type' => 'carrossel', 'title' => 'Novo cardápio de inverno — pratos e histórias', 'reason' => 'Carrosséis que contam a história por trás dos pratos geram 35% mais salvamentos. Vincule a uma campanha sazonal.', 'objective' => 'Branding'],
                ['type' => 'story', 'title' => 'Enquete: qual sabor novo você quer?', 'reason' => 'Enquetes nos stories geram interação direta e fornecem dados para o menu. Taxa de resposta média: 25%.', 'objective' => 'Engajamento'],
                ['type' => 'video', 'title' => 'Chef prepara prato especial ao vivo', 'reason' => 'Conteúdo ao vivo ou estilo "ao vivo" transmite autenticidade. Bastidores da cozinha performam 45% melhor que fotos de menu.', 'objective' => 'Branding'],
            ],
            'Moda' => [
                ['type' => 'carrossel', 'title' => '3 looks com uma peça só', 'reason' => 'Carrosséis de combinação de looks geram 2.5x mais salvamentos. É o formato com maior ROI para e-commerce de moda.', 'objective' => 'Conversão'],
                ['type' => 'reels', 'title' => 'Try-on haul: novidades da semana', 'reason' => 'Try-on hauls têm taxa de compartilhamento 3x maior e geram tráfego direto para o e-commerce.', 'objective' => 'Conversão'],
                ['type' => 'story', 'title' => 'Votação: look A ou look B?', 'reason' => 'Stories com votação geram engajamento imediato e dados sobre preferências do público.', 'objective' => 'Engajamento'],
                ['type' => 'imagem', 'title' => 'Tendências da estação com peças da loja', 'reason' => 'Associar tendências globais a peças da loja posiciona a marca como referência. Posts assim têm 40% mais alcance.', 'objective' => 'Branding'],
            ],
            'Tecnologia' => [
                ['type' => 'carrossel', 'title' => '5 ferramentas que todo profissional precisa', 'reason' => 'Listas de ferramentas são altamente salvas e compartilhadas no nicho tech. Ideal para posicionamento de autoridade.', 'objective' => 'Educação'],
                ['type' => 'reels', 'title' => 'Antes x Depois: automação na prática', 'reason' => 'Demonstrações visuais de impacto geram curiosidade e conversão. Reels comparativos performam 55% acima da média.', 'objective' => 'Conversão'],
                ['type' => 'video', 'title' => 'Tutorial rápido: funcionalidade mais pedida', 'reason' => 'Tutoriais curtos reduzem churn e posicionam a marca como autoridade. Ideal para retenção.', 'objective' => 'Educação'],
            ],
            'Fitness' => [
                ['type' => 'reels', 'title' => 'Treino de 15 min sem equipamento', 'reason' => 'Treinos rápidos e acessíveis são o conteúdo mais salvo no nicho fitness. Reels desse tipo viralizam organicamente.', 'objective' => 'Engajamento'],
                ['type' => 'carrossel', 'title' => 'O que comer antes e depois do treino', 'reason' => 'Nutrição + treino é a combinação com maior taxa de compartilhamento. Carrosséis educativos funcionam muito bem.', 'objective' => 'Educação'],
                ['type' => 'video', 'title' => 'Transformação: 30 dias de desafio', 'reason' => 'Conteúdo de transformação gera prova social poderosa. Use com depoimento real para máximo impacto.', 'objective' => 'Conversão'],
            ],
            'Beleza' => [
                ['type' => 'reels', 'title' => 'Get ready with me: rotina matinal', 'reason' => 'GRWM é o formato nº1 em beleza no Instagram e TikTok. Alta taxa de retenção e compartilhamento.', 'objective' => 'Engajamento'],
                ['type' => 'carrossel', 'title' => 'Skincare: 5 erros que você não sabia', 'reason' => 'Conteúdo educativo em formato "erros comuns" gera curiosidade e alta taxa de salvamento.', 'objective' => 'Educação'],
                ['type' => 'story', 'title' => 'Antes e depois: tratamento do mês', 'reason' => 'Antes/depois é prova social direta. Nos stories, gera interação imediata e pedidos de agendamento.', 'objective' => 'Conversão'],
            ],
        ];

        return $suggestions[$niche] ?? [
            ['type' => 'carrossel', 'title' => 'Lista: 5 dicas para seu público', 'reason' => 'Listas em carrossel são o formato com maior taxa de salvamento em qualquer nicho. Adapte para o seu público.', 'objective' => 'Educação'],
            ['type' => 'reels', 'title' => 'Bastidores: como funciona por dentro', 'reason' => 'Conteúdo de bastidores humaniza a marca e gera conexão emocional. Reels curtos (15-30s) performam melhor.', 'objective' => 'Branding'],
            ['type' => 'video', 'title' => 'Depoimento de cliente satisfeito', 'reason' => 'Prova social em vídeo é o conteúdo com maior taxa de conversão. Use legendas para acessibilidade.', 'objective' => 'Conversão'],
        ];
    }
}

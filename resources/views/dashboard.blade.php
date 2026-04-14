<x-app-layout>
    <x-slot name="title">Dashboard</x-slot>

    <div class="mb-10">
        <h1 class="section-title">Dashboard</h1>
        <p class="text-gray-500 mt-2 text-sm sm:text-base">Visão geral da sua agência.</p>
    </div>

    {{-- KPIs Globais --}}
    <div class="grid gap-4 grid-cols-2 lg:grid-cols-4 mb-10">
        <div class="card p-5">
            <div class="flex items-center justify-between mb-3">
                <div class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Clientes</div>
                <div class="w-8 h-8 rounded-none bg-primary/10 flex items-center justify-center">
                    <span class="material-symbols-outlined text-primary-foreground text-lg">group</span>
                </div>
            </div>
            <div class="text-2xl font-bold text-gray-900">{{ $stats['clients'] }}</div>
            <div class="text-xs text-gray-500 font-medium mt-1">ativos na plataforma</div>
        </div>

        <div class="card p-5">
            <div class="flex items-center justify-between mb-3">
                <div class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Postagens</div>
                <div class="w-8 h-8 rounded-none bg-blue-50 flex items-center justify-center">
                    <span class="material-symbols-outlined text-blue-500 text-lg">edit_note</span>
                </div>
            </div>
            <div class="text-2xl font-bold text-gray-900">{{ $stats['posts'] }}</div>
            <div class="text-xs text-gray-500 font-medium mt-1">total criadas</div>
        </div>

        <div class="card p-5">
            <div class="flex items-center justify-between mb-3">
                <div class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Campanhas</div>
                <div class="w-8 h-8 rounded-none bg-violet-50 flex items-center justify-center">
                    <span class="material-symbols-outlined text-violet-500 text-lg">campaign</span>
                </div>
            </div>
            <div class="text-2xl font-bold text-gray-900">{{ $stats['campaigns'] }}</div>
            <div class="text-xs text-gray-500 font-medium mt-1">ativas no momento</div>
        </div>

        <div class="card p-5">
            <div class="flex items-center justify-between mb-3">
                <div class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Engajamento</div>
                <div class="w-8 h-8 rounded-none bg-amber-50 flex items-center justify-center">
                    <span class="material-symbols-outlined text-amber-500 text-lg">trending_up</span>
                </div>
            </div>
            <div class="text-2xl font-bold text-gray-900">{{ $stats['avg_engagement'] }}</div>
            <div class="text-xs text-green-600 font-medium mt-1">média geral</div>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        {{-- Clientes --}}
        <div class="lg:col-span-2">
            <div class="card">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                    <h2 class="font-semibold text-gray-900">Seus clientes</h2>
                    <a href="{{ route('clients.index') }}" class="text-sm text-gray-500 hover:text-primary-foreground font-medium transition-colors">Ver todos →</a>
                </div>
                <div class="divide-y divide-gray-50">
                    @forelse ($clients as $client)
                        <a href="{{ route('clients.show', $client) }}" class="flex items-center gap-4 px-6 py-4 hover:bg-gray-50/50 transition-colors group">
                            <div class="inline-flex h-10 w-10 items-center justify-center rounded-none bg-primary/20 text-primary-foreground font-bold text-sm flex-shrink-0">
                                {{ strtoupper(substr($client->name, 0, 2)) }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="font-medium text-sm text-gray-900 truncate group-hover:text-primary-foreground transition-colors">{{ $client->name }}</div>
                                <div class="text-xs text-gray-500 mt-0.5">{{ $client->niche ?? 'Sem nicho' }} · {{ $client->posts_count }} posts</div>
                            </div>
                            @if ($client->niche)
                                <span class="badge bg-primary/20 text-primary-foreground">{{ $client->niche }}</span>
                            @endif
                        </a>
                    @empty
                        <div class="px-6 py-10 text-center text-sm text-gray-400">
                            Nenhum cliente cadastrado.
                            <a href="{{ route('clients.create') }}" class="text-primary-foreground font-semibold hover:underline ml-1">Criar primeiro cliente</a>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Atividade Recente --}}
        <div>
            <div class="card">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                    <h2 class="font-semibold text-gray-900">Atividade recente</h2>
                </div>
                <div class="divide-y divide-gray-50">
                    @forelse ($recentPosts as $post)
                        <div class="px-6 py-4">
                            <div class="font-medium text-sm text-gray-900 truncate">{{ $post->title }}</div>
                            <div class="flex items-center gap-2 mt-1.5">
                                <span class="badge {{ match($post->status) {
                                    'publicada' => 'bg-green-100 text-green-700',
                                    'agendada' => 'bg-blue-100 text-blue-700',
                                    'em_producao' => 'bg-amber-100 text-amber-700',
                                    default => 'bg-gray-100 text-gray-600'
                                } }}">{{ $post->status_label }}</span>
                                <span class="text-xs text-gray-400">{{ $post->client->name }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="px-6 py-10 text-center text-sm text-gray-400">
                            Nenhuma atividade recente.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

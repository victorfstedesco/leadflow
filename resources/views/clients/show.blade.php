<x-app-layout>
    <x-slot name="title">{{ $client->name }}</x-slot>

    <x-slot name="clientBar">
        <x-client-subnav :client="$client" />
    </x-slot>

    {{-- KPIs reais --}}
    <div class="grid gap-4 grid-cols-2 lg:grid-cols-3 mb-8">
        <div class="card p-5">
            <div class="flex items-center justify-between mb-3">
                <div class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Postagens</div>
                <div class="w-8 h-8 rounded-none bg-primary/10 flex items-center justify-center">
                    <span class="material-symbols-outlined text-primary-foreground text-lg">edit_note</span>
                </div>
            </div>
            <div class="text-2xl font-bold text-gray-900">{{ $totalPosts }}</div>
            <div class="text-xs text-gray-500 font-medium mt-1">total criadas</div>
        </div>

        <div class="card p-5">
            <div class="flex items-center justify-between mb-3">
                <div class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Campanhas ativas</div>
                <div class="w-8 h-8 rounded-none bg-violet-50 flex items-center justify-center">
                    <span class="material-symbols-outlined text-violet-500 text-lg">campaign</span>
                </div>
            </div>
            <div class="text-2xl font-bold text-gray-900">{{ $campaignsActive }}</div>
            <div class="text-xs text-gray-500 font-medium mt-1">no Meta</div>
        </div>

        <div class="card p-5">
            <div class="flex items-center justify-between mb-3">
                <div class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Vinculadas</div>
                <div class="w-8 h-8 rounded-none bg-green-50 flex items-center justify-center">
                    <span class="material-symbols-outlined text-green-500 text-lg">link</span>
                </div>
            </div>
            <div class="text-2xl font-bold text-gray-900">{{ $linkedPosts }}</div>
            <div class="text-xs text-gray-500 font-medium mt-1">postagens com campanha</div>
        </div>
    </div>

    {{-- Conteúdo principal --}}
    <div class="grid gap-6 lg:grid-cols-3">

        {{-- Coluna principal --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Campanhas recentes --}}
            <div class="card">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                    <h2 class="font-semibold text-gray-900">Campanhas recentes</h2>
                    <a href="{{ route('clients.campaigns', $client) }}"
                        class="text-sm text-gray-500 hover:text-primary-foreground font-medium transition-colors">Ver
                        todas →</a>
                </div>
                <div class="divide-y divide-gray-50">
                    @forelse ($recentCampaigns as $campaign)
                        <a href="{{ route('campaigns.show', [$client, $campaign]) }}"
                            class="flex items-center gap-4 px-6 py-4 hover:bg-gray-50/50 transition-colors group">
                            <div
                                class="w-2 h-2 rounded-full flex-shrink-0 mt-1
                                    {{ $campaign->meta_status === 'ACTIVE' ? 'bg-green-400' : ($campaign->meta_status === 'PAUSED' ? 'bg-amber-400' : 'bg-gray-300') }}">
                            </div>
                            <div class="flex-1 min-w-0">
                                <div
                                    class="font-medium text-sm text-gray-900 truncate group-hover:text-primary-foreground transition-colors">
                                    {{ $campaign->name }}
                                </div>
                                <div class="text-xs text-gray-400 mt-0.5">
                                    {{ $campaign->meta_status_label }}
                                    @if ($campaign->last_synced_at)
                                        · Sincronizado {{ $campaign->last_synced_at->diffForHumans() }}
                                    @endif
                                </div>
                            </div>
                            <div class="text-right flex-shrink-0">
                                @if (!empty($campaign->insights['spend']))
                                    <div class="text-sm font-semibold text-gray-900">R$
                                        {{ number_format($campaign->insights['spend'], 2, ',', '.') }}</div>
                                    <div class="text-xs text-gray-400">investimento</div>
                                @else
                                    <span
                                        class="badge {{ $campaign->meta_status === 'ACTIVE' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                                        {{ $campaign->meta_status_label }}
                                    </span>
                                @endif
                            </div>
                        </a>
                    @empty
                        <div class="px-6 py-10 text-center text-sm text-gray-400">
                            Nenhuma campanha sincronizada.
                            @if ($client->isMetaConnected())
                                <a href="{{ route('campaigns.sync', $client) }}"
                                    class="text-primary-foreground font-semibold hover:underline ml-1"
                                    onclick="event.preventDefault(); document.getElementById('sync-form').submit()">Sincronizar
                                    agora</a>
                                <form id="sync-form" method="POST" action="{{ route('campaigns.sync', $client) }}"
                                    class="hidden">@csrf</form>
                            @else
                                <a href="{{ route('clients.settings', $client) }}"
                                    class="text-primary-foreground font-semibold hover:underline ml-1">Conectar Meta Ads</a>
                            @endif
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Postagens recentes --}}
            <div class="card">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                    <h2 class="font-semibold text-gray-900">Últimas postagens</h2>
                    <a href="{{ route('clients.posts', $client) }}"
                        class="text-sm text-gray-500 hover:text-primary-foreground font-medium transition-colors">Ver
                        todas →</a>
                </div>
                <div class="divide-y divide-gray-50">
                    @forelse ($recentPosts as $post)
                        <a href="{{ route('posts.edit', [$client, $post]) }}"
                            class="flex items-center gap-4 px-6 py-4 hover:bg-gray-50/50 transition-colors group">
                            <div
                                class="w-10 h-10 rounded-none bg-gray-50 border border-gray-100 flex items-center justify-center flex-shrink-0">
                                <span
                                    class="material-symbols-outlined text-[18px] text-gray-500">{{ $post->content_type_icon }}</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div
                                    class="font-medium text-sm text-gray-900 truncate group-hover:text-primary-foreground transition-colors">
                                    {{ $post->title }}
                                </div>
                                <div class="text-xs text-gray-500 mt-0.5">{{ $post->content_type_label }} ·
                                    {{ $post->objective_label }}</div>
                            </div>
                            @if ($post->campaign)
                                <span
                                    class="badge bg-primary/20 text-primary-foreground">{{ Str::limit($post->campaign->name, 20) }}</span>
                            @else
                                <span class="badge bg-gray-100 text-gray-500">Sem campanha</span>
                            @endif
                        </a>
                    @empty
                        <div class="px-6 py-10 text-center text-sm text-gray-400">
                            Nenhuma postagem criada.
                            <a href="{{ route('posts.create', $client) }}"
                                class="text-primary-foreground font-semibold hover:underline ml-1">Criar postagem</a>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">
            {{-- Redes sociais ativas --}}
            <div class="card p-6">
                <h2 class="font-semibold text-gray-900 mb-4">Redes sociais</h2>
                @if (!empty($client->channels))
                    <div class="space-y-2">
                        @foreach ($client->channels as $channel)
                            <div class="flex items-center gap-3 py-2">
                                <div class="w-2 h-2 rounded-full bg-primary"></div>
                                <span class="text-sm text-gray-700">{{ $channel }}</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-400">Nenhuma rede social configurada.</p>
                @endif
            </div>

            {{-- Meta status --}}
            <div class="card p-6">
                <h2 class="font-semibold text-gray-900 mb-4">Meta Ads</h2>
                @if ($client->isMetaConnected())
                    <div class="flex items-center gap-2 text-sm text-green-700">
                        <span class="material-symbols-outlined text-[18px]">check_circle</span>
                        Conta conectada
                    </div>
                    @if ($client->meta_ad_account_id)
                        <p class="text-xs text-gray-400 mt-1">{{ $client->meta_ad_account_id }}</p>
                    @endif
                @else
                    <div class="flex items-center gap-2 text-sm text-gray-400">
                        <span class="material-symbols-outlined text-[18px]">link_off</span>
                        Não conectado
                    </div>
                    <a href="{{ route('clients.settings', $client) }}"
                        class="mt-3 inline-flex text-xs font-semibold text-primary-foreground hover:underline">
                        Conectar conta Meta →
                    </a>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
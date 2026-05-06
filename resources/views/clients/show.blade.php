<x-app-layout>
    <x-slot name="title">{{ $client->name }}</x-slot>

    <x-slot name="clientBar">
        <x-client-subnav :client="$client" />
    </x-slot>

    {{-- Header Profile (Dashboard Report Style) --}}
    <div class="bg-white rounded p-8 md:p-10 shadow-sm border border-gray-100 mb-10 relative overflow-hidden">
        <div class="absolute -right-20 -top-20 w-64 h-64 bg-primary/5 rounded blur-3xl pointer-events-none"></div>
        <div class="absolute right-1/4 -bottom-20 w-40 h-40 bg-blue-50 rounded blur-3xl pointer-events-none"></div>
        
        <div class="flex flex-col md:flex-row gap-8 items-start md:items-center relative z-10">
            <div class="inline-flex h-24 w-24 items-center justify-center rounded bg-primary text-primary-foreground font-black text-3xl shadow-lg ring-8 ring-primary/10">
                {{ strtoupper(substr($client->name, 0, 2)) }}
            </div>
            <div class="flex-1">
                <div class="flex items-center gap-3 mb-2">
                    <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">{{ $client->name }}</h1>
                    @if ($client->niche)
                        <span class="badge bg-white text-gray-700 border border-gray-200 shadow-sm">{{ $client->niche }}</span>
                    @endif
                </div>
                <div class="flex flex-wrap items-center gap-4 text-sm font-medium text-gray-500">
                    <div class="flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[18px]">calendar_today</span>
                        <span>Cliente desde {{ $client->created_at->translatedFormat('M Y') }}</span>
                    </div>
                    @if (!empty($client->channels))
                        <div class="flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded bg-gray-300"></span>
                            <span>{{ implode(', ', $client->channels) }}</span>
                        </div>
                    @endif
                </div>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('clients.settings', $client) }}" class="btn-secondary">
                    <span class="material-symbols-outlined text-[20px]">settings</span>
                    Configurações
                </a>
            </div>
        </div>
    </div>

    <div class="mb-6">
        <h2 class="text-xl font-bold text-gray-900 tracking-tight">Visão Geral</h2>
    </div>

    {{-- KPIs --}}
    <div class="grid gap-6 grid-cols-1 md:grid-cols-3 mb-10">
        <div class="card p-6 flex flex-col justify-between border-gray-200/60 shadow-sm hover:border-primary/30 group">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded bg-primary/10 flex items-center justify-center group-hover:scale-110 transition-transform">
                    <span class="material-symbols-outlined text-primary-foreground text-[20px]">edit_note</span>
                </div>
                <div class="text-xs font-bold text-gray-500 uppercase tracking-wider">Total de Postagens</div>
            </div>
            <div>
                <div class="text-4xl font-extrabold text-gray-900">{{ $totalPosts }}</div>
                <div class="text-sm text-gray-400 font-medium mt-1">publicadas e agendadas</div>
            </div>
        </div>

        <div class="card p-6 flex flex-col justify-between border-gray-200/60 shadow-sm hover:border-violet-500/30 group">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded bg-violet-100 flex items-center justify-center group-hover:scale-110 transition-transform">
                    <span class="material-symbols-outlined text-violet-600 text-[20px]">campaign</span>
                </div>
                <div class="text-xs font-bold text-gray-500 uppercase tracking-wider">Campanhas Ativas</div>
            </div>
            <div>
                <div class="text-4xl font-extrabold text-gray-900">{{ $campaignsActive }}</div>
                <div class="text-sm text-gray-400 font-medium mt-1">em veiculação no Meta</div>
            </div>
        </div>

        <div class="card p-6 flex flex-col justify-between border-gray-200/60 shadow-sm hover:border-green-500/30 group">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded bg-green-100 flex items-center justify-center group-hover:scale-110 transition-transform">
                    <span class="material-symbols-outlined text-green-600 text-[20px]">link</span>
                </div>
                <div class="text-xs font-bold text-gray-500 uppercase tracking-wider">Posts Vinculados</div>
            </div>
            <div>
                <div class="text-4xl font-extrabold text-gray-900">{{ $linkedPosts }}</div>
                <div class="text-sm text-gray-400 font-medium mt-1">fazem parte de campanhas</div>
            </div>
        </div>
    </div>

    {{-- Conteúdo principal --}}
    <div class="grid gap-8 lg:grid-cols-3">

        {{-- Coluna principal --}}
        <div class="lg:col-span-2 space-y-8">

            {{-- Campanhas recentes --}}
            <div class="card p-0 overflow-hidden border-gray-200/60 shadow-sm">
                <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100 bg-white/50 backdrop-blur-sm">
                    <h2 class="font-bold text-gray-900 text-lg">Campanhas em andamento</h2>
                    <a href="{{ route('clients.campaigns', $client) }}"
                       class="text-sm text-primary-foreground font-semibold hover:text-primary transition-colors flex items-center gap-1 group">
                       Ver todas <span class="material-symbols-outlined text-[16px] group-hover:translate-x-0.5 transition-transform">arrow_forward</span>
                    </a>
                </div>
                <div class="divide-y divide-gray-50 bg-white">
                    @forelse ($recentCampaigns as $campaign)
                        <a href="{{ route('campaigns.show', [$client, $campaign]) }}"
                           class="flex items-center gap-4 px-6 py-5 hover:bg-gray-50/80 transition-colors group">
                            <div class="w-2.5 h-2.5 rounded flex-shrink-0 shadow-sm
                                {{ $campaign->meta_status === 'ACTIVE' ? 'bg-green-400 shadow-green-400/50' : ($campaign->meta_status === 'PAUSED' ? 'bg-amber-400 shadow-amber-400/50' : 'bg-gray-300') }}">
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="font-semibold text-base text-gray-900 truncate group-hover:text-primary-foreground transition-colors">
                                    {{ $campaign->name }}
                                </div>
                                <div class="text-xs font-medium text-gray-500 mt-1 flex items-center gap-1.5">
                                    <span class="{{ $campaign->meta_status === 'ACTIVE' ? 'text-green-600' : 'text-gray-500' }}">{{ $campaign->meta_status_label }}</span>
                                    @if ($campaign->last_synced_at)
                                        <span class="w-1 h-1 rounded bg-gray-300"></span>
                                        Sincronizado {{ $campaign->last_synced_at->diffForHumans() }}
                                    @endif
                                </div>
                            </div>
                            <div class="text-right flex-shrink-0">
                                @if (!empty($campaign->insights['spend']))
                                    <div class="text-base font-bold text-gray-900">R$ {{ number_format($campaign->insights['spend'], 2, ',', '.') }}</div>
                                    <div class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Investimento</div>
                                @else
                                    <span class="badge border shadow-sm {{ $campaign->meta_status === 'ACTIVE' ? 'bg-green-50 text-green-700 border-green-200' : 'bg-gray-50 text-gray-500 border-gray-200' }}">
                                        {{ $campaign->meta_status_label }}
                                    </span>
                                @endif
                            </div>
                        </a>
                    @empty
                        <div class="px-6 py-12 text-center flex flex-col items-center">
                            <div class="w-16 h-16 rounded bg-gray-50 flex items-center justify-center text-gray-300 mb-4">
                                <span class="material-symbols-outlined text-3xl">campaign</span>
                            </div>
                            <div class="text-gray-500 font-medium mb-3">Nenhuma campanha sincronizada.</div>
                            @if ($client->isMetaConnected())
                                <a href="{{ route('campaigns.sync', $client) }}"
                                   class="btn-primary"
                                   onclick="event.preventDefault(); document.getElementById('sync-form').submit()">Sincronizar agora</a>
                                <form id="sync-form" method="POST" action="{{ route('campaigns.sync', $client) }}" class="hidden">@csrf</form>
                            @else
                                <a href="{{ route('clients.settings', $client) }}"
                                   class="btn-secondary text-sm">Conectar Meta Ads</a>
                            @endif
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Postagens recentes --}}
            <div class="card p-0 overflow-hidden border-gray-200/60 shadow-sm">
                <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100 bg-white/50 backdrop-blur-sm">
                    <h2 class="font-bold text-gray-900 text-lg">Últimas postagens</h2>
                    <a href="{{ route('clients.posts', $client) }}"
                        class="text-sm text-primary-foreground font-semibold hover:text-primary transition-colors flex items-center gap-1 group">
                        Ver todas <span class="material-symbols-outlined text-[16px] group-hover:translate-x-0.5 transition-transform">arrow_forward</span>
                    </a>
                </div>
                <div class="divide-y divide-gray-50 bg-white">
                    @forelse ($recentPosts as $post)
                        <a href="{{ route('posts.edit', [$client, $post]) }}"
                            class="flex items-center gap-4 px-6 py-5 hover:bg-gray-50/80 transition-colors group">
                            <div class="w-12 h-12 rounded bg-gray-50 border border-gray-100 flex items-center justify-center flex-shrink-0 group-hover:bg-primary/10 group-hover:border-primary/20 transition-colors">
                                <span class="material-symbols-outlined text-[24px] text-gray-400 group-hover:text-primary-foreground transition-colors">{{ $post->content_type_icon }}</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="font-semibold text-base text-gray-900 truncate group-hover:text-primary-foreground transition-colors">
                                    {{ $post->title }}
                                </div>
                                <div class="text-xs font-medium text-gray-500 mt-1">{{ $post->content_type_label }} · {{ $post->objective_label }}</div>
                            </div>
                            @if ($post->campaign)
                                <span class="badge bg-primary/10 text-primary-foreground border border-primary/20 hidden sm:inline-flex">{{ Str::limit($post->campaign->name, 20) }}</span>
                            @else
                                <span class="badge bg-surface text-gray-500 border border-gray-200 hidden sm:inline-flex">Sem campanha</span>
                            @endif
                        </a>
                    @empty
                        <div class="px-6 py-12 text-center flex flex-col items-center">
                            <div class="w-16 h-16 rounded bg-gray-50 flex items-center justify-center text-gray-300 mb-4">
                                <span class="material-symbols-outlined text-3xl">edit_note</span>
                            </div>
                            <div class="text-gray-500 font-medium mb-3">Nenhuma postagem criada.</div>
                            <a href="{{ route('posts.create', $client) }}" class="btn-primary text-sm">Criar postagem</a>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="space-y-8">
            {{-- Timeline Básica --}}
            <div class="card p-0 border-gray-200/60 shadow-sm">
                <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100 bg-white/50 backdrop-blur-sm">
                    <h2 class="font-bold text-gray-900 text-lg">Timeline</h2>
                </div>
                <div class="p-6 relative bg-white">
                    <div class="absolute left-[39px] top-6 bottom-6 w-px bg-gray-200"></div>
                    <div class="space-y-6 relative">
                        {{-- Timeline Events --}}
                        @php
                            $timelineEvents = collect($recentCampaigns)->map(function($c) {
                                return ['type' => 'campanha', 'date' => $c->created_at, 'title' => 'Nova campanha iniciada: ' . Str::limit($c->name, 30), 'icon' => 'campaign', 'color' => 'bg-violet-100 text-violet-600 ring-4 ring-white'];
                            })->concat(collect($recentPosts)->map(function($p) {
                                return ['type' => 'post', 'date' => $p->created_at, 'title' => 'Post criado: ' . Str::limit($p->title, 30), 'icon' => 'edit_note', 'color' => 'bg-primary/10 text-primary-foreground ring-4 ring-white'];
                            }))->sortByDesc('date')->take(5);
                        @endphp

                        @forelse($timelineEvents as $event)
                            <div class="flex gap-4">
                                <div class="w-8 h-8 rounded {{ $event['color'] }} flex items-center justify-center flex-shrink-0 relative z-10">
                                    <span class="material-symbols-outlined text-[16px]">{{ $event['icon'] }}</span>
                                </div>
                                <div class="pt-1.5">
                                    <div class="text-xs font-bold text-gray-400 mb-1">{{ $event['date']->format('d/m/Y') }}</div>
                                    <div class="text-sm font-medium text-gray-800 leading-snug">{{ $event['title'] }}</div>
                                </div>
                            </div>
                        @empty
                            <div class="text-sm text-gray-400 italic">Nenhuma atividade recente.</div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Redes sociais ativas --}}
            <div class="card p-6 border-gray-200/60">
                <h2 class="font-bold text-gray-900 mb-5 text-lg">Redes sociais</h2>
                @if (!empty($client->channels))
                    <div class="space-y-3">
                        @foreach ($client->channels as $channel)
                            <div class="flex items-center gap-3 py-2 px-3 rounded bg-gray-50/50 border border-gray-100">
                                <div class="w-2.5 h-2.5 rounded bg-primary shadow-sm shadow-primary/50"></div>
                                <span class="text-sm font-semibold text-gray-700">{{ $channel }}</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-400 italic">Nenhuma rede social configurada.</p>
                @endif
            </div>

            {{-- Meta status --}}
            <div class="card p-6 border-gray-200/60">
                <h2 class="font-bold text-gray-900 mb-5 text-lg">Integração Meta</h2>
                @if ($client->isMetaConnected())
                    <div class="flex items-center gap-3 p-4 rounded bg-green-50/50 border border-green-100">
                        <div class="w-10 h-10 rounded bg-green-100 flex items-center justify-center text-green-600">
                            <span class="material-symbols-outlined text-[20px]">check_circle</span>
                        </div>
                        <div>
                            <div class="text-sm font-bold text-green-800">Conta Conectada</div>
                            @if ($client->meta_ad_account_id)
                                <div class="text-xs font-medium text-green-600/80 mt-0.5">ID: {{ $client->meta_ad_account_id }}</div>
                            @endif
                        </div>
                    </div>
                @else
                    <div class="flex items-center gap-3 p-4 rounded bg-gray-50 border border-gray-100 mb-4">
                        <div class="w-10 h-10 rounded bg-gray-200 flex items-center justify-center text-gray-500">
                            <span class="material-symbols-outlined text-[20px]">link_off</span>
                        </div>
                        <div>
                            <div class="text-sm font-bold text-gray-700">Não Conectado</div>
                            <div class="text-xs font-medium text-gray-500 mt-0.5">Vincule a conta de anúncios</div>
                        </div>
                    </div>
                    <a href="{{ route('clients.settings', $client) }}"
                        class="w-full btn-secondary text-sm">
                        Conectar conta →
                    </a>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>

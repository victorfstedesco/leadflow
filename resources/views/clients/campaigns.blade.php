<x-app-layout>
    <x-slot name="title">Campanhas · {{ $client->name }}</x-slot>

    <x-slot name="clientBar">
        <x-client-subnav :client="$client" />
    </x-slot>

    <div class="mb-6 flex flex-wrap items-start justify-between gap-4">
        <div>
            <h2 class="text-lg font-semibold text-gray-900">Campanhas</h2>
            <p class="text-sm text-gray-500 mt-0.5">Campanhas sincronizadas do Meta. Vincule postagens manualmente para gerar insights.</p>
            @if ($client->meta_last_synced_at)
                <p class="text-xs text-gray-400 mt-1">Última sincronização: {{ $client->meta_last_synced_at->diffForHumans() }}</p>
            @endif
        </div>
        <div class="flex items-center gap-2">
            @if ($client->isMetaConnected())
                <form method="POST" action="{{ route('campaigns.sync', $client) }}">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 bg-primary text-primary-foreground text-sm font-bold rounded-sm hover:bg-primary/90 transition-colors">
                        <span class="material-symbols-outlined text-[16px]">sync</span>
                        Sincronizar com Meta
                    </button>
                </form>
            @else
                <a href="{{ route('clients.settings', $client) }}"
                   class="inline-flex items-center gap-1.5 px-4 py-2 bg-primary text-primary-foreground text-sm font-bold rounded-sm hover:bg-primary/90 transition-colors">
                    <span class="material-symbols-outlined text-[16px]">link</span>
                    Conectar Meta
                </a>
            @endif
        </div>
    </div>

    @if (! $client->isMetaConnected())
        <div class="card p-6 mb-6 border-amber-200 bg-amber-50/50">
            <div class="flex items-start gap-3">
                <span class="material-symbols-outlined text-amber-500">info</span>
                <div>
                    <h3 class="font-semibold text-gray-900 text-sm">Conta Meta não conectada</h3>
                    <p class="text-sm text-gray-600 mt-1">Conecte a conta Meta deste cliente nas configurações para sincronizar campanhas reais e seus insights.</p>
                </div>
            </div>
        </div>
    @endif

    <div x-data="{ expandedCampaign: null, showLinkModal: false, linkTargetId: null, linkTargetName: '' }" class="space-y-6">
        @forelse ($campaigns as $i => $campaign)
            <div class="card overflow-hidden">
                <div class="p-6 cursor-pointer hover:bg-gray-50/50 transition-colors"
                     @click="expandedCampaign = expandedCampaign === {{ $i }} ? null : {{ $i }}">
                    <div class="flex flex-wrap items-start justify-between gap-4">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-2">
                                @php
                                    $statusBg = match($campaign->meta_status) {
                                        'ACTIVE' => 'bg-green-50 border border-green-100',
                                        'PAUSED' => 'bg-amber-50 border border-amber-100',
                                        default => 'bg-gray-50 border border-gray-100',
                                    };
                                    $statusFg = match($campaign->meta_status) {
                                        'ACTIVE' => 'text-green-600',
                                        'PAUSED' => 'text-amber-600',
                                        default => 'text-gray-400',
                                    };
                                    $statusBadge = match($campaign->meta_status) {
                                        'ACTIVE' => 'bg-green-100 text-green-700',
                                        'PAUSED' => 'bg-amber-100 text-amber-700',
                                        default => 'bg-gray-100 text-gray-600',
                                    };
                                @endphp
                                <div class="w-10 h-10 rounded-none flex items-center justify-center {{ $statusBg }}">
                                    <span class="material-symbols-outlined text-lg {{ $statusFg }}">campaign</span>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-900 text-lg">{{ $campaign->name }}</h3>
                                    <div class="flex items-center gap-3 text-xs text-gray-500 mt-0.5">
                                        @if ($campaign->start_date)
                                            <span>{{ $campaign->start_date->format('d/m/Y') }} – {{ $campaign->stop_date?->format('d/m/Y') ?? '—' }}</span>
                                        @endif
                                        @if ($campaign->daily_budget)
                                            <span>R$ {{ number_format($campaign->daily_budget, 2, ',', '.') }}/dia</span>
                                        @elseif ($campaign->lifetime_budget)
                                            <span>R$ {{ number_format($campaign->lifetime_budget, 2, ',', '.') }} total</span>
                                        @endif
                                        @if ($campaign->objective)
                                            <span class="uppercase tracking-wide text-[10px]">{{ $campaign->objective }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="badge {{ $statusBadge }}">{{ $campaign->meta_status_label }}</span>
                            <span class="material-symbols-outlined text-gray-400 transition-transform duration-300"
                                  :class="expandedCampaign === {{ $i }} ? 'rotate-180' : ''">expand_more</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-5 gap-3 mt-4">
                        @php
                            $insights = $campaign->insights ?? [];
                            $metrics = [
                                ['label' => 'Alcance', 'value' => isset($insights['reach']) ? number_format($insights['reach'], 0, ',', '.') : '—'],
                                ['label' => 'Impressões', 'value' => isset($insights['impressions']) ? number_format($insights['impressions'], 0, ',', '.') : '—'],
                                ['label' => 'Cliques', 'value' => isset($insights['clicks']) ? number_format($insights['clicks'], 0, ',', '.') : '—'],
                                ['label' => 'CTR', 'value' => isset($insights['ctr']) ? number_format((float) $insights['ctr'], 2, ',', '.') . '%' : '—'],
                                ['label' => 'CPC', 'value' => isset($insights['cpc']) ? 'R$ ' . number_format((float) $insights['cpc'], 2, ',', '.') : '—'],
                            ];
                        @endphp
                        @foreach ($metrics as $metric)
                            <div class="text-center">
                                <div class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">{{ $metric['label'] }}</div>
                                <div class="text-sm font-bold text-gray-900 mt-0.5">{{ $metric['value'] }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div x-show="expandedCampaign === {{ $i }}"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 -translate-y-2"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     class="border-t border-gray-100">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-[18px] text-gray-400">link</span>
                                <h4 class="text-sm font-semibold text-gray-700">Postagens vinculadas</h4>
                                <span class="badge bg-gray-100 text-gray-600">{{ $campaign->posts->count() }}</span>
                            </div>
                            <button @click.stop="showLinkModal = true; linkTargetId = {{ $campaign->id }}; linkTargetName = '{{ addslashes($campaign->name) }}'"
                                    class="inline-flex items-center gap-1.5 text-xs font-bold text-primary-foreground hover:text-primary transition-colors">
                                <span class="material-symbols-outlined text-[16px]">add_link</span>
                                Vincular postagem
                            </button>
                        </div>

                        @if ($campaign->posts->isEmpty())
                            <div class="rounded-sm bg-gray-50 border border-dashed border-gray-200 p-8 text-center">
                                <span class="material-symbols-outlined text-3xl text-gray-300 mb-2">post_add</span>
                                <p class="text-sm text-gray-400 mt-2">Nenhuma postagem vinculada a esta campanha.</p>
                            </div>
                        @else
                            <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                                @foreach ($campaign->posts as $post)
                                    <div class="group relative bg-gray-50 rounded-sm border border-gray-100 p-4 hover:border-primary/30 hover:bg-primary/5 transition-all">
                                        <div class="flex items-start gap-3">
                                            <div class="w-9 h-9 rounded-none bg-white border border-gray-200 flex items-center justify-center flex-shrink-0 shadow-sm">
                                                <span class="material-symbols-outlined text-[16px] text-gray-600">{{ $post->content_type_icon }}</span>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <div class="font-medium text-sm text-gray-900 truncate">{{ $post->title }}</div>
                                                <div class="flex items-center gap-2 mt-1">
                                                    <span class="text-[10px] font-semibold text-gray-400 uppercase">{{ $post->content_type_label }}</span>
                                                    @if ($post->objective)
                                                        <span class="text-[10px] text-gray-300">·</span>
                                                        <span class="text-[10px] font-semibold text-gray-400 uppercase">{{ $post->objective_label }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <form method="POST" action="{{ route('posts.link-campaign', [$client, $post]) }}"
                                              class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="campaign_id" value="">
                                            <button type="submit" class="w-6 h-6 rounded-full bg-red-50 border border-red-100 flex items-center justify-center hover:bg-red-100 transition-colors" title="Desvincular">
                                                <span class="material-symbols-outlined text-[14px] text-red-500">close</span>
                                            </button>
                                        </form>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="card p-12 text-center">
                <span class="material-symbols-outlined text-4xl text-gray-300">campaign</span>
                <h3 class="text-sm font-semibold text-gray-900 mt-3">Nenhuma campanha sincronizada</h3>
                <p class="text-sm text-gray-500 mt-1">
                    @if ($client->isMetaConnected())
                        Clique em "Sincronizar com Meta" para puxar suas campanhas.
                    @else
                        Conecte a conta Meta deste cliente para começar.
                    @endif
                </p>
            </div>
        @endforelse

        {{-- Modal: Vincular postagem --}}
        <div x-show="showLinkModal" x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center p-4"
             @keydown.escape.window="showLinkModal = false">
            <div class="absolute inset-0 bg-black/30 backdrop-blur-sm" @click="showLinkModal = false"></div>
            <div class="relative bg-white rounded-sm shadow-2xl w-full max-w-lg border border-gray-100 overflow-hidden"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                    <div>
                        <h3 class="font-semibold text-gray-900">Vincular postagem</h3>
                        <p class="text-xs text-gray-500 mt-0.5">Vincular à campanha <strong x-text="linkTargetName"></strong></p>
                    </div>
                    <button @click="showLinkModal = false" class="w-8 h-8 rounded-full hover:bg-gray-100 flex items-center justify-center transition-colors">
                        <span class="material-symbols-outlined text-gray-400">close</span>
                    </button>
                </div>
                <div class="p-6 max-h-[400px] overflow-y-auto">
                    @if ($unlinkedPosts->isEmpty())
                        <div class="text-center py-8">
                            <span class="material-symbols-outlined text-3xl text-gray-300">inbox</span>
                            <p class="text-sm text-gray-400 mt-2">Todas as postagens já estão vinculadas.</p>
                            <a href="{{ route('posts.create', $client) }}" class="mt-3 inline-flex items-center gap-1.5 text-xs font-bold text-primary-foreground hover:underline">
                                <span class="material-symbols-outlined text-[14px]">add</span>
                                Criar nova postagem
                            </a>
                        </div>
                    @else
                        <div class="space-y-2">
                            @foreach ($unlinkedPosts as $post)
                                <form method="POST" action="{{ route('posts.link-campaign', [$client, $post]) }}">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="campaign_id" x-bind:value="linkTargetId">
                                    <button type="submit"
                                            class="w-full flex items-center gap-3 p-3 rounded-sm border border-gray-100 hover:border-primary hover:bg-primary/5 transition-all text-left group">
                                        <div class="w-9 h-9 rounded-none bg-gray-50 border border-gray-200 flex items-center justify-center flex-shrink-0 group-hover:border-primary group-hover:bg-primary/10 transition-colors">
                                            <span class="material-symbols-outlined text-[16px] text-gray-600 group-hover:text-primary-foreground transition-colors">{{ $post->content_type_icon }}</span>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="font-medium text-sm text-gray-900 truncate group-hover:text-primary-foreground transition-colors">{{ $post->title }}</div>
                                            <div class="flex items-center gap-2 mt-0.5">
                                                <span class="text-[10px] font-semibold text-gray-400 uppercase">{{ $post->content_type_label }}</span>
                                                @if ($post->objective)
                                                    <span class="text-[10px] text-gray-300">·</span>
                                                    <span class="text-[10px] font-semibold text-gray-400 uppercase">{{ $post->objective_label }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        <span class="material-symbols-outlined text-gray-300 group-hover:text-primary-foreground transition-colors">add_link</span>
                                    </button>
                                </form>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

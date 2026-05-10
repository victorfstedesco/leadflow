<x-app-layout>
    <x-slot name="title">Campanhas · {{ $client->name }}</x-slot>

    <x-slot name="clientBar">
        <x-client-subnav :client="$client" />
    </x-slot>

    <div x-data="campaignsPage()" class="space-y-6">

        {{-- Header --}}
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <h2 class="text-lg font-semibold text-gray-900">Campanhas</h2>
                <p class="text-sm text-gray-500 mt-0.5">Dados sincronizados do Meta Ads.</p>
                @if ($client->meta_last_synced_at)
                    <p class="text-xs text-gray-400 mt-1">Última sincronização: {{ $client->meta_last_synced_at->diffForHumans() }}</p>
                @endif
            </div>
            <div class="flex items-center gap-2">
                @if ($client->isMetaConnected())
                    <form method="POST" action="{{ route('campaigns.sync', $client) }}">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 bg-primary text-primary-foreground text-sm font-bold  hover:bg-primary/90 transition-colors">
                            <span class="material-symbols-outlined text-[16px]">sync</span>
                            Sincronizar
                        </button>
                    </form>
                @else
                    <a href="{{ route('clients.settings', $client) }}"
                       class="inline-flex items-center gap-1.5 px-4 py-2 bg-primary text-primary-foreground text-sm font-bold  hover:bg-primary/90 transition-colors">
                        <span class="material-symbols-outlined text-[16px]">link</span>
                        Conectar Meta
                    </a>
                @endif
            </div>
        </div>

        @if (! $client->isMetaConnected())
            <div class="card p-6 border-amber-200 bg-amber-50/50">
                <div class="flex items-start gap-3">
                    <span class="material-symbols-outlined text-amber-500">info</span>
                    <div>
                        <h3 class="font-semibold text-gray-900 text-sm">Conta Meta não conectada</h3>
                        <p class="text-sm text-gray-600 mt-1">Conecte a conta Meta nas configurações para sincronizar campanhas.</p>
                    </div>
                </div>
            </div>
        @endif

        {{-- Filtro de período --}}
        @if ($campaigns->isNotEmpty() && $client->isMetaConnected())
            <div class="card p-4 space-y-3">

                {{-- Presets --}}
                <div class="flex flex-wrap gap-1.5">
                    <template x-for="preset in presets" :key="preset.key">
                        <button @click="applyPreset(preset.key)"
                                :class="activePreset === preset.key
                                    ? 'bg-primary text-primary-foreground shadow-sm'
                                    : 'bg-gray-100 text-gray-600 hover:bg-gray-200'"
                                class="px-3 py-1.5 text-xs font-semibold transition-colors"
                                x-text="preset.label">
                        </button>
                    </template>
                </div>

                {{-- Inputs personalizados --}}
                <div x-show="activePreset === 'custom'" x-transition class="flex flex-wrap items-end gap-3">
                    <div>
                        <label class="label text-xs">De</label>
                        <input type="date" x-model="since" class="input py-1.5 text-sm">
                    </div>
                    <div>
                        <label class="label text-xs">Até</label>
                        <input type="date" x-model="until" class="input py-1.5 text-sm">
                    </div>
                    <button @click="fetchPeriodInsights()"
                            :disabled="loading || !since || !until"
                            class="inline-flex items-center gap-1.5 px-4 py-2 bg-primary text-primary-foreground text-sm font-bold hover:bg-primary/90 transition-colors disabled:opacity-50">
                        <span class="material-symbols-outlined text-[16px]" :class="loading ? 'animate-spin' : ''">
                            <template x-if="loading">refresh</template>
                            <template x-if="!loading">search</template>
                        </span>
                        <span x-text="loading ? 'Buscando...' : 'Buscar'"></span>
                    </button>
                </div>

                {{-- Status --}}
                <div class="flex items-center gap-3 min-h-[18px]">
                    <span x-show="loading" class="text-xs text-gray-400 flex items-center gap-1">
                        <span class="material-symbols-outlined text-[14px] animate-spin">refresh</span>
                        Buscando dados...
                    </span>
                    <span x-show="periodActive && !loading" class="text-xs text-primary font-semibold flex items-center gap-1">
                        <span class="material-symbols-outlined text-[14px]">filter_alt</span>
                        <span x-text="periodLabel"></span>
                    </span>
                    <button x-show="periodActive && !loading" @click="clearPeriod()"
                            class="text-xs text-gray-400 hover:text-gray-600 underline">
                        Limpar filtro
                    </button>
                    <p x-show="error" x-text="error" class="text-xs text-red-500"></p>
                </div>

            </div>
        @endif

        {{-- Lista de campanhas --}}
        <div x-data="{ expandedCampaign: null, showLinkModal: false, linkTargetId: null, linkTargetName: '' }" class="space-y-4">
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
                                            default  => 'bg-gray-50 border border-gray-100',
                                        };
                                        $statusFg = match($campaign->meta_status) {
                                            'ACTIVE' => 'text-green-600',
                                            'PAUSED' => 'text-amber-600',
                                            default  => 'text-gray-400',
                                        };
                                        $statusBadge = match($campaign->meta_status) {
                                            'ACTIVE' => 'bg-green-100 text-green-700',
                                            'PAUSED' => 'bg-amber-100 text-amber-700',
                                            default  => 'bg-gray-100 text-gray-600',
                                        };
                                    @endphp
                                    <div class="w-10 h-10 flex items-center justify-center {{ $statusBg }}">
                                        <span class="material-symbols-outlined text-lg {{ $statusFg }}">campaign</span>
                                    </div>
                                    <div>
                                        <h3 class="font-semibold text-gray-900">{{ $campaign->name }}</h3>
                                        <div class="flex items-center gap-3 text-xs text-gray-500 mt-0.5">
                                            @if ($campaign->start_date)
                                                <span>{{ $campaign->start_date->format('d/m/Y') }} – {{ $campaign->stop_date?->format('d/m/Y') ?? '—' }}</span>
                                            @endif
                                            @if ($campaign->daily_budget)
                                                <span>R$ {{ number_format($campaign->daily_budget, 2, ',', '.') }}/dia</span>
                                            @elseif ($campaign->lifetime_budget)
                                                <span>R$ {{ number_format($campaign->lifetime_budget, 2, ',', '.') }} total</span>
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

                        {{-- Métricas: period ou all-time --}}
                        @php
                            $stored = $campaign->insights ?? [];
                            $metrics = [
                                ['label' => 'Alcance',    'key' => 'reach',       'format' => 'int'],
                                ['label' => 'Impressões', 'key' => 'impressions', 'format' => 'int'],
                                ['label' => 'Cliques',    'key' => 'clicks',      'format' => 'int'],
                                ['label' => 'CTR',        'key' => 'ctr',         'format' => 'pct'],
                                ['label' => 'CPC',        'key' => 'cpc',         'format' => 'brl'],
                            ];
                        @endphp
                        <div class="grid grid-cols-5 gap-3 mt-4">
                            @foreach ($metrics as $m)
                                <div class="text-center">
                                    <div class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">{{ $m['label'] }}</div>
                                    <div class="text-sm font-bold text-gray-900 mt-0.5"
                                         x-text="formatMetric(periodInsights[{{ $campaign->id }}], '{{ $m['key'] }}', '{{ $m['format'] }}', {{ json_encode($stored) }})">
                                        {{-- fallback rendered server-side --}}
                                        @if ($m['format'] === 'int')
                                            {{ isset($stored[$m['key']]) ? number_format($stored[$m['key']], 0, ',', '.') : '—' }}
                                        @elseif ($m['format'] === 'pct')
                                            {{ isset($stored[$m['key']]) ? number_format((float)$stored[$m['key']], 2, ',', '.') . '%' : '—' }}
                                        @else
                                            {{ isset($stored[$m['key']]) ? 'R$ ' . number_format((float)$stored[$m['key']], 2, ',', '.') : '—' }}
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Postagens expandidas --}}
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
                                        class="inline-flex items-center gap-1.5 text-xs font-bold text-primary-foreground hover:underline">
                                    <span class="material-symbols-outlined text-[16px]">add_link</span>
                                    Vincular postagem
                                </button>
                            </div>

                            @if ($campaign->posts->isEmpty())
                                <div class=" bg-gray-50 border border-dashed border-gray-200 p-8 text-center text-sm text-gray-400">
                                    Nenhuma postagem vinculada a esta campanha.
                                </div>
                            @else
                                <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                                    @foreach ($campaign->posts as $post)
                                        <div class="group relative bg-gray-50  border border-gray-100 p-4 hover:border-primary/30 transition-all">
                                            <div class="flex items-start gap-3">
                                                <div class="w-9 h-9 bg-white border border-gray-200 flex items-center justify-center flex-shrink-0">
                                                    <span class="material-symbols-outlined text-[16px] text-gray-600">{{ $post->content_type_icon }}</span>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <div class="font-medium text-sm text-gray-900 truncate">{{ $post->title }}</div>
                                                    <div class="text-[10px] font-semibold text-gray-400 uppercase mt-0.5">{{ $post->content_type_label }}</div>
                                                </div>
                                            </div>
                                            <form method="POST" action="{{ route('posts.link-campaign', [$client, $post]) }}"
                                                  class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                                @csrf @method('PATCH')
                                                <input type="hidden" name="campaign_id" value="">
                                                <button type="submit" class="w-6 h-6  bg-red-50 border border-red-100 flex items-center justify-center hover:bg-red-100" title="Desvincular">
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
                            Clique em "Sincronizar" para puxar suas campanhas do Meta.
                        @else
                            Conecte a conta Meta para começar.
                        @endif
                    </p>
                </div>
            @endforelse

            {{-- Modal: Vincular postagem --}}
            <div x-show="showLinkModal" x-cloak
                 class="fixed inset-0 z-50 flex items-center justify-center p-4"
                 @keydown.escape.window="showLinkModal = false">
                <div class="absolute inset-0 bg-black/30 backdrop-blur-sm" @click="showLinkModal = false"></div>
                <div class="relative bg-white  shadow-2xl w-full max-w-lg border border-gray-100 overflow-hidden"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100">
                    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                        <div>
                            <h3 class="font-semibold text-gray-900">Vincular postagem</h3>
                            <p class="text-xs text-gray-500 mt-0.5">Campanha: <strong x-text="linkTargetName"></strong></p>
                        </div>
                        <button @click="showLinkModal = false" class="w-8 h-8  hover:bg-gray-100 flex items-center justify-center">
                            <span class="material-symbols-outlined text-gray-400">close</span>
                        </button>
                    </div>
                    <div class="p-6 max-h-[400px] overflow-y-auto">
                        @if ($unlinkedPosts->isEmpty())
                            <div class="text-center py-8 text-sm text-gray-400">
                                Todas as postagens já estão vinculadas.
                            </div>
                        @else
                            <div class="space-y-2">
                                @foreach ($unlinkedPosts as $post)
                                    <form method="POST" action="{{ route('posts.link-campaign', [$client, $post]) }}">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="campaign_id" x-bind:value="linkTargetId">
                                        <button type="submit"
                                                class="w-full flex items-center gap-3 p-3  border border-gray-100 hover:border-primary hover:bg-primary/5 transition-all text-left group">
                                            <div class="w-9 h-9 bg-gray-50 border border-gray-200 flex items-center justify-center flex-shrink-0">
                                                <span class="material-symbols-outlined text-[16px] text-gray-600">{{ $post->content_type_icon }}</span>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <div class="font-medium text-sm text-gray-900 truncate">{{ $post->title }}</div>
                                                <div class="text-[10px] font-semibold text-gray-400 uppercase">{{ $post->content_type_label }}</div>
                                            </div>
                                            <span class="material-symbols-outlined text-gray-300 group-hover:text-primary-foreground">add_link</span>
                                        </button>
                                    </form>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const CAMPAIGN_ROUTES = @json($campaigns->mapWithKeys(fn($c) => [
            $c->id => route('campaigns.insights', [$client, $c])
        ]));

        function campaignsPage() {
            return {
                since: '',
                until: '',
                loading: false,
                periodActive: false,
                activePreset: null,
                periodLabel: '',
                error: '',
                periodInsights: {},

                init() {
                    this.applyPreset('today');
                },

                presets: [
                    { key: 'today',      label: 'Hoje' },
                    { key: 'yesterday',  label: 'Ontem' },
                    { key: 'last7',      label: 'Últ. 7 dias' },
                    { key: 'last30',     label: 'Últ. 30 dias' },
                    { key: 'thisMonth',  label: 'Este mês' },
                    { key: 'lastMonth',  label: 'Mês passado' },
                    { key: 'q1',         label: 'Q1' },
                    { key: 'q2',         label: 'Q2' },
                    { key: 'q3',         label: 'Q3' },
                    { key: 'q4',         label: 'Q4' },
                    { key: 'ytd',        label: 'YTD' },
                    { key: 'custom',     label: 'Personalizado' },
                ],

                getPresetRange(key) {
                    const now = new Date();
                    const year = now.getFullYear();
                    const month = now.getMonth();
                    const fmt = d => d.toISOString().slice(0, 10);
                    const ymd = (y, m, d) => fmt(new Date(y, m, d));
                    const today = fmt(now);

                    switch (key) {
                        case 'today':
                            return { since: today, until: today, label: 'Hoje' };
                        case 'yesterday': {
                            const d = new Date(now); d.setDate(d.getDate() - 1);
                            const yd = fmt(d);
                            return { since: yd, until: yd, label: 'Ontem' };
                        }
                        case 'last7': {
                            const d = new Date(now); d.setDate(d.getDate() - 6);
                            return { since: fmt(d), until: today, label: 'Últimos 7 dias' };
                        }
                        case 'last30': {
                            const d = new Date(now); d.setDate(d.getDate() - 29);
                            return { since: fmt(d), until: today, label: 'Últimos 30 dias' };
                        }
                        case 'thisMonth':
                            return { since: ymd(year, month, 1), until: today, label: 'Este mês' };
                        case 'lastMonth': {
                            const lm = month === 0 ? 11 : month - 1;
                            const ly = month === 0 ? year - 1 : year;
                            const lastDay = new Date(year, month, 0).getDate();
                            return { since: ymd(ly, lm, 1), until: ymd(ly, lm, lastDay), label: 'Mês passado' };
                        }
                        case 'q1': return { since: ymd(year, 0, 1),  until: ymd(year, 2, 31), label: `Q1 ${year}` };
                        case 'q2': return { since: ymd(year, 3, 1),  until: ymd(year, 5, 30), label: `Q2 ${year}` };
                        case 'q3': return { since: ymd(year, 6, 1),  until: ymd(year, 8, 30), label: `Q3 ${year}` };
                        case 'q4': return { since: ymd(year, 9, 1),  until: ymd(year, 11, 31), label: `Q4 ${year}` };
                        case 'ytd': return { since: ymd(year, 0, 1), until: today, label: `YTD ${year}` };
                        default: return null;
                    }
                },

                applyPreset(key) {
                    this.activePreset = key;
                    this.error = '';
                    if (key === 'custom') {
                        this.since = '';
                        this.until = '';
                        return;
                    }
                    const range = this.getPresetRange(key);
                    if (!range) return;
                    this.since = range.since;
                    this.until = range.until;
                    this.periodLabel = range.label;
                    this.fetchPeriodInsights();
                },

                async fetchPeriodInsights() {
                    if (!this.since || !this.until) return;
                    if (this.activePreset === 'custom') {
                        const fmtDate = s => new Date(s + 'T00:00:00').toLocaleDateString('pt-BR');
                        this.periodLabel = `${fmtDate(this.since)} → ${fmtDate(this.until)}`;
                    }
                    this.loading = true;
                    this.error = '';
                    this.periodInsights = {};

                    try {
                        const ids = Object.keys(CAMPAIGN_ROUTES);
                        const results = await Promise.all(ids.map(async id => {
                            const url = CAMPAIGN_ROUTES[id] + `?since=${this.since}&until=${this.until}`;
                            const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                            const json = await res.json();
                            return { id: parseInt(id), data: json.data ?? {} };
                        }));

                        results.forEach(r => { this.periodInsights[r.id] = r.data; });
                        this.periodActive = true;
                    } catch (e) {
                        this.error = 'Erro ao buscar dados do período. Tente novamente.';
                    } finally {
                        this.loading = false;
                    }
                },

                clearPeriod() {
                    this.since = '';
                    this.until = '';
                    this.periodInsights = {};
                    this.periodActive = false;
                    this.activePreset = null;
                    this.periodLabel = '';
                    this.error = '';
                },

                formatMetric(periodData, key, format, stored) {
                    const data = this.periodActive && periodData ? periodData : stored;
                    const val = data?.[key];
                    if (val === undefined || val === null || val === '') return '—';
                    if (format === 'int') return Number(val).toLocaleString('pt-BR');
                    if (format === 'pct') return Number(val).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + '%';
                    if (format === 'brl') return 'R$ ' + Number(val).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                    return val;
                },
            }
        }
    </script>
</x-app-layout>

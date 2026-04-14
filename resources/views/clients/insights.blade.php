<x-app-layout>
    <x-slot name="title">Insights · {{ $client->name }}</x-slot>

    <x-slot name="clientBar">
        <x-client-subnav :client="$client" />
    </x-slot>

    <div class="mb-6">
        <h2 class="text-lg font-semibold text-gray-900">Insights</h2>
        <p class="text-sm text-gray-500 mt-0.5">Análises e recomendações baseadas no desempenho e no nicho <strong>{{ $client->niche ?? 'geral' }}</strong>.</p>
    </div>

    {{-- KPIs --}}
    <div class="grid gap-4 grid-cols-2 lg:grid-cols-4 mb-8">
        <div class="card p-5">
            <div class="flex items-center justify-between mb-3">
                <div class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Melhor conteúdo</div>
                <div class="w-8 h-8 rounded-none bg-primary/10 flex items-center justify-center">
                    <span class="material-symbols-outlined text-primary-foreground text-lg">star</span>
                </div>
            </div>
            <div class="text-xl font-bold text-gray-900">{{ $kpis['best_content'] }}</div>
            <div class="text-xs text-gray-500 font-medium mt-1">maior engajamento</div>
        </div>

        <div class="card p-5">
            <div class="flex items-center justify-between mb-3">
                <div class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Melhor horário</div>
                <div class="w-8 h-8 rounded-none bg-blue-50 flex items-center justify-center">
                    <span class="material-symbols-outlined text-blue-500 text-lg">schedule</span>
                </div>
            </div>
            <div class="text-xl font-bold text-gray-900">{{ $kpis['best_time'] }}</div>
            <div class="text-xs text-gray-500 font-medium mt-1">para publicar</div>
        </div>

        <div class="card p-5">
            <div class="flex items-center justify-between mb-3">
                <div class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Engajamento</div>
                <div class="w-8 h-8 rounded-none bg-amber-50 flex items-center justify-center">
                    <span class="material-symbols-outlined text-amber-500 text-lg">trending_up</span>
                </div>
            </div>
            <div class="text-xl font-bold text-gray-900">{{ $kpis['avg_engagement'] }}</div>
            <div class="text-xs text-green-600 font-medium mt-1">média do período</div>
        </div>

        <div class="card p-5">
            <div class="flex items-center justify-between mb-3">
                <div class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Seguidores</div>
                <div class="w-8 h-8 rounded-none bg-violet-50 flex items-center justify-center">
                    <span class="material-symbols-outlined text-violet-500 text-lg">person_add</span>
                </div>
            </div>
            <div class="text-xl font-bold text-gray-900">{{ $kpis['follower_growth'] }}</div>
            <div class="text-xs text-green-600 font-medium mt-1">este mês</div>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-2 mb-8">
        {{-- Performance por tipo de conteúdo --}}
        <div class="card p-6">
            <h3 class="font-semibold text-gray-900 mb-4">Performance por tipo de conteúdo</h3>
            <div class="space-y-4">
                @php $maxReach = max(array_column($contentPerformance, 'reach')); @endphp
                @foreach ($contentPerformance as $item)
                    <div>
                        <div class="flex items-center justify-between text-sm mb-1.5">
                            <span class="font-medium text-gray-700">{{ $item['type'] }}</span>
                            <div class="flex items-center gap-4">
                                <span class="text-gray-500">{{ number_format($item['reach']) }} alcance</span>
                                <span class="font-bold text-primary-foreground">{{ $item['engagement'] }}%</span>
                            </div>
                        </div>
                        <div class="h-2.5 rounded-full bg-gray-100 overflow-hidden">
                            <div class="h-full bg-primary rounded-full transition-all" style="width: {{ ($item['reach'] / $maxReach) * 100 }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Desempenho semanal simulado --}}
        <div class="card p-6">
            <h3 class="font-semibold text-gray-900 mb-4">Engajamento semanal</h3>
            <div class="flex items-end gap-1.5 h-48">
                @foreach ([4.2, 3.8, 5.1, 4.7, 6.2, 5.5, 4.9] as $i => $val)
                    @php $pct = ($val / 6.5) * 100; @endphp
                    <div class="flex-1 flex flex-col items-center gap-1">
                        <span class="text-[10px] font-bold text-gray-500">{{ $val }}%</span>
                        <div class="w-full rounded-none bg-primary/{{ $val > 5 ? '60' : '30' }} transition-all hover:bg-primary"
                             style="height: {{ $pct }}%"></div>
                        <span class="text-[10px] text-gray-400">{{ ['Seg','Ter','Qua','Qui','Sex','Sáb','Dom'][$i] }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Insights Contextuais --}}
    <div class="mb-6">
        <h2 class="text-lg font-semibold text-gray-900">Recomendações estratégicas</h2>
        <p class="text-sm text-gray-500 mt-0.5">Insights personalizados para o nicho <strong>{{ $client->niche ?? 'geral' }}</strong>.</p>
    </div>

    <div class="grid gap-4 md:grid-cols-3">
        @foreach ($contextualInsights as $insight)
            <div class="card p-6 hover:shadow-md transition-shadow group">
                <div class="w-10 h-10 rounded-none bg-primary/10 flex items-center justify-center mb-4 group-hover:bg-primary transition-colors">
                    <span class="material-symbols-outlined text-primary-foreground group-hover:text-white text-lg transition-colors">{{ $insight['icon'] }}</span>
                </div>
                <h3 class="font-semibold text-gray-900 mb-2">{{ $insight['title'] }}</h3>
                <p class="text-sm text-gray-500 leading-relaxed">{{ $insight['text'] }}</p>
            </div>
        @endforeach
    </div>
</x-app-layout>

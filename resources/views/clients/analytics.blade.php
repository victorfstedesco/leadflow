<x-app-layout>
    <x-slot name="title">Analytics · {{ $client->name }}</x-slot>

    {{-- Header do cliente --}}
    <div class="flex flex-wrap items-center justify-between gap-4 mb-2">
        <div class="flex items-center gap-4">
            <a href="{{ route('clients.index') }}" class="text-gray-400 hover:text-gray-900 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <div class="flex items-center gap-3">
                <div class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-primary/30 text-primary-foreground font-bold text-sm">
                    {{ strtoupper(substr($client->name, 0, 2)) }}
                </div>
                <h1 class="text-xl font-bold tracking-tight text-gray-900">{{ $client->name }}</h1>
            </div>
        </div>
    </div>

    {{-- Sub-navegação --}}
    <x-client-subnav :client="$client" />

    {{-- Header da seção --}}
    <div class="mb-6">
        <h2 class="text-lg font-semibold text-gray-900">Analytics</h2>
        <p class="text-sm text-gray-500 mt-0.5">Desempenho do funil e métricas de aquisição deste cliente.</p>
    </div>

    {{-- KPIs --}}
    <div class="grid gap-4 grid-cols-2 lg:grid-cols-4 mb-8">
        <div class="card p-5">
            <div class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Total de leads</div>
            <div class="text-2xl font-bold text-gray-900 mt-2">{{ $totalLeads }}</div>
        </div>
        <div class="card p-5">
            <div class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Convertidos</div>
            <div class="text-2xl font-bold text-gray-900 mt-2">{{ $converted }}</div>
        </div>
        <div class="card p-5">
            <div class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Taxa de conversão</div>
            <div class="text-2xl font-bold text-primary-foreground mt-2">{{ $conversionRate }}%</div>
        </div>
        <div class="card p-5">
            <div class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Leads parados (7d+)</div>
            <div class="text-2xl font-bold text-gray-900 mt-2">{{ $stale }}</div>
        </div>
    </div>

    {{-- Gráficos --}}
    <div class="grid gap-6 md:grid-cols-2">
        <div class="card p-6">
            <h3 class="font-semibold text-gray-900 mb-4">Leads por canal</h3>
            @if ($bySource->isEmpty())
                <p class="text-sm text-gray-400">Sem dados ainda.</p>
            @else
                <div class="space-y-3">
                    @foreach ($bySource as $source => $count)
                        @php $pct = $totalLeads > 0 ? round(($count / $totalLeads) * 100) : 0; @endphp
                        <div>
                            <div class="flex items-center justify-between text-sm mb-1">
                                <span class="text-gray-700">{{ $source }}</span>
                                <span class="text-gray-500"><strong class="text-gray-900">{{ $count }}</strong> · {{ $pct }}%</span>
                            </div>
                            <div class="h-2 rounded-full bg-gray-100 overflow-hidden">
                                <div class="h-full bg-primary rounded-full transition-all" style="width: {{ $pct }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="card p-6">
            <h3 class="font-semibold text-gray-900 mb-4">Leads por etapa</h3>
            <div class="space-y-3">
                @foreach ($byStage as $stageName => $count)
                    @php $pct = $totalLeads > 0 ? round(($count / $totalLeads) * 100) : 0; @endphp
                    <div>
                        <div class="flex items-center justify-between text-sm mb-1">
                            <span class="text-gray-700">{{ $stageName }}</span>
                            <span class="text-gray-500"><strong class="text-gray-900">{{ $count }}</strong> · {{ $pct }}%</span>
                        </div>
                        <div class="h-2 rounded-full bg-gray-100 overflow-hidden">
                            <div class="h-full bg-primary-foreground rounded-full transition-all" style="width: {{ $pct }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-app-layout>

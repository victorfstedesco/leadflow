<x-app-layout>
    <x-slot name="title">{{ $client->name }}</x-slot>

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
                <div>
                    <h1 class="text-xl font-bold tracking-tight text-gray-900">{{ $client->name }}</h1>
                    @if (!empty($client->channels))
                        <div class="flex flex-wrap gap-1 mt-0.5">
                            @foreach ($client->channels as $channel)
                                <span class="text-xs text-gray-500">{{ $channel }}{{ !$loop->last ? ' ·' : '' }}</span>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Sub-navegação --}}
    <x-client-subnav :client="$client" />

    {{-- KPIs --}}
    <div class="grid gap-4 grid-cols-2 lg:grid-cols-4 mb-8">
        <div class="card p-5">
            <div class="flex items-center justify-between mb-3">
                <div class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Visitas</div>
                <div class="w-8 h-8 rounded-none bg-blue-50 flex items-center justify-center">
                    <span class="material-symbols-outlined text-blue-500 text-lg">visibility</span>
                </div>
            </div>
            <div class="text-2xl font-bold text-gray-900">12.4K</div>
            <div class="text-xs text-green-600 font-medium mt-1">+18% vs mês anterior</div>
        </div>

        <div class="card p-5">
            <div class="flex items-center justify-between mb-3">
                <div class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Posts ativos</div>
                <div class="w-8 h-8 rounded-none bg-primary/10 flex items-center justify-center">
                    <span class="material-symbols-outlined text-primary-foreground text-lg">campaign</span>
                </div>
            </div>
            <div class="text-2xl font-bold text-gray-900">{{ count($activePosts) }}</div>
            <div class="text-xs text-gray-500 font-medium mt-1">em andamento</div>
        </div>

        <div class="card p-5">
            <div class="flex items-center justify-between mb-3">
                <div class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Leads no mês</div>
                <div class="w-8 h-8 rounded-none bg-violet-50 flex items-center justify-center">
                    <span class="material-symbols-outlined text-violet-500 text-lg">person_add</span>
                </div>
            </div>
            <div class="text-2xl font-bold text-gray-900">{{ $totalLeads }}</div>
            <div class="text-xs text-gray-500 font-medium mt-1">total acumulado</div>
        </div>

        <div class="card p-5">
            <div class="flex items-center justify-between mb-3">
                <div class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Conversão</div>
                <div class="w-8 h-8 rounded-none bg-amber-50 flex items-center justify-center">
                    <span class="material-symbols-outlined text-amber-500 text-lg">trending_up</span>
                </div>
            </div>
            <div class="text-2xl font-bold text-gray-900">{{ $conversionRate }}%</div>
            <div class="text-xs text-gray-500 font-medium mt-1">última etapa do funil</div>
        </div>
    </div>

    {{-- Conteúdo principal: Postagens ativas + Visitas --}}
    <div class="grid gap-6 lg:grid-cols-3">
        {{-- Postagens em andamento --}}
        <div class="lg:col-span-2">
            <div class="card">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                    <h2 class="font-semibold text-gray-900">Postagens em andamento</h2>
                    <a href="{{ route('clients.posts', $client) }}" class="text-sm text-gray-500 hover:text-primary-foreground font-medium transition-colors">Ver todas →</a>
                </div>
                <div class="divide-y divide-gray-50">
                    @forelse ($activePosts as $post)
                        <div class="flex items-center gap-4 px-6 py-4 hover:bg-gray-50/50 transition-colors">
                            <div class="w-10 h-10 rounded-none {{ $post['platform_bg'] }} flex items-center justify-center flex-shrink-0">
                                <span class="material-symbols-outlined text-[18px] text-gray-700">{{ $post['platform_icon'] }}</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="font-medium text-sm text-gray-900 truncate">{{ $post['title'] }}</div>
                                <div class="text-xs text-gray-500 mt-0.5">{{ $post['platform'] }} · {{ $post['date'] }}</div>
                            </div>
                            <span class="badge {{ $post['status'] === 'Publicada' ? 'bg-green-100 text-green-700' : ($post['status'] === 'Agendada' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-600') }}">
                                {{ $post['status'] }}
                            </span>
                        </div>
                    @empty
                        <div class="px-6 py-10 text-center text-sm text-gray-400">
                            Nenhuma postagem em andamento.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Sidebar: Visitas e Canais --}}
        <div class="space-y-6">
            {{-- Gráfico placeholder de visitas --}}
            <div class="card p-6">
                <h2 class="font-semibold text-gray-900 mb-4">Visitas — últimos 7 dias</h2>
                <div class="flex items-end gap-1.5 h-32">
                    @foreach ([65, 40, 78, 52, 90, 85, 72] as $i => $val)
                        <div class="flex-1 flex flex-col items-center gap-1">
                            <div class="w-full rounded-none bg-primary/{{ $val > 70 ? '60' : '30' }} transition-all hover:bg-primary"
                                 style="height: {{ $val }}%"></div>
                            <span class="text-[10px] text-gray-400">{{ ['Seg','Ter','Qua','Qui','Sex','Sáb','Dom'][$i] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Canais ativos --}}
            <div class="card p-6">
                <h2 class="font-semibold text-gray-900 mb-4">Canais ativos</h2>
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
                    <p class="text-sm text-gray-400">Nenhum canal configurado.</p>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>

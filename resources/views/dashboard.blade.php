<x-app-layout>
    <x-slot name="title">Dashboard</x-slot>

    <div class="mb-10">
        <h1 class="section-title">Dashboard</h1>
        <p class="text-gray-500 mt-2 text-sm sm:text-base">Visão geral da sua agência.</p>
    </div>

    {{-- KPIs Globais --}}
    <div class="grid gap-4 grid-cols-2 lg:grid-cols-3 mb-10">
        <div class="card p-5">
            <div class="flex items-center justify-between mb-3">
                <div class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Clientes</div>
                <div class="w-8 h-8 bg-primary/10 flex items-center justify-center rounded">
                    <span class="material-symbols-outlined text-primary-foreground text-lg">group</span>
                </div>
            </div>
            <div class="text-2xl font-bold text-gray-900">{{ $stats['clients'] }}</div>
            <div class="text-xs text-gray-500 font-medium mt-1">na plataforma</div>
        </div>

        <div class="card p-5">
            <div class="flex items-center justify-between mb-3">
                <div class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Postagens</div>
                <div class="w-8 h-8 bg-blue-50 flex items-center justify-center rounded">
                    <span class="material-symbols-outlined text-blue-500 text-lg">edit_note</span>
                </div>
            </div>
            <div class="text-2xl font-bold text-gray-900">{{ $stats['posts'] }}</div>
            <div class="text-xs text-gray-500 font-medium mt-1">total criadas</div>
        </div>

        <div class="card p-5">
            <div class="flex items-center justify-between mb-3">
                <div class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Campanhas ativas</div>
                <div class="w-8 h-8 bg-violet-50 flex items-center justify-center rounded">
                    <span class="material-symbols-outlined text-violet-500 text-lg">campaign</span>
                </div>
            </div>
            <div class="text-2xl font-bold text-gray-900">{{ $stats['campaigns_active'] }}</div>
            <div class="text-xs text-gray-500 font-medium mt-1">de {{ $stats['campaigns'] }} total</div>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">

        {{-- Coluna principal --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Clientes --}}
            <div class="card">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                    <h2 class="font-semibold text-gray-900">Seus clientes</h2>
                    <a href="{{ route('clients.index') }}"
                        class="text-sm text-gray-500 hover:text-primary-foreground font-medium transition-colors">Ver
                        todos →</a>
                </div>
                <div class="divide-y divide-gray-50">
                    @forelse ($clients as $client)
                        <a href="{{ route('clients.show', $client) }}"
                            class="flex items-center gap-4 px-6 py-4 hover:bg-gray-50/50 transition-colors group">
                            <div
                                class="inline-flex h-10 w-10 items-center justify-center rounded bg-primary/20 text-primary-foreground font-bold text-sm flex-shrink-0">
                                {{ strtoupper(substr($client->name, 0, 2)) }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <div
                                    class="font-medium text-sm text-gray-900 truncate group-hover:text-primary-foreground transition-colors">
                                    {{ $client->name }}</div>
                                <div class="text-xs text-gray-500 mt-0.5">{{ $client->niche ?? 'Sem nicho' }} ·
                                    {{ $client->posts_count }} posts</div>
                            </div>
                            @if ($client->niche)
                                <span class="badge bg-primary/20 text-primary-foreground">{{ $client->niche }}</span>
                            @endif
                        </a>
                    @empty
                        <div class="px-6 py-10 text-center text-sm text-gray-400">
                            Nenhum cliente cadastrado.
                            <a href="{{ route('clients.create') }}"
                                class="text-primary-foreground font-semibold hover:underline ml-1">Criar primeiro
                                cliente</a>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Sidebar: Planejamentos recentes --}}
        <div>
            <div class="card">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                    <h2 class="font-semibold text-gray-900">Planejamentos</h2>
                </div>
                <div class="divide-y divide-gray-50">
                    @forelse ($recentPlannings as $planning)
                                        @php
                                            $totalGoals = $planning->goals->count();
                                            $avgProgress = $totalGoals > 0
                                                ? $planning->goals->avg('progress_percent')
                                                : null;
                                        @endphp
                                        <a href="{{ route('plannings.show', [$planning->client, $planning]) }}"
                                            class="block px-6 py-4 hover:bg-gray-50/50 transition-colors group">
                                            <div class="flex items-center justify-between mb-2">
                                                <div
                                                    class="font-medium text-sm text-gray-900 truncate group-hover:text-primary-foreground transition-colors">
                                                    {{ $planning->name }}
                                                </div>
                                                <span class="badge ml-2 flex-shrink-0 {{ match ($planning->status) {
                            'ativo' => 'bg-green-100 text-green-700',
                            'pausado' => 'bg-amber-100 text-amber-700',
                            'concluido' => 'bg-blue-100 text-blue-700',
                            default => 'bg-gray-100 text-gray-500'
                        } }}">{{ $planning->status_label }}</span>
                                            </div>
                                            <div class="text-xs text-gray-400 mb-2">{{ $planning->client->name }}</div>
                                            @if ($avgProgress !== null)
                                                <div class="flex items-center gap-2">
                                                    <div class="flex-1 h-1.5 bg-gray-100 rounded-full overflow-hidden">
                                                        <div class="h-full bg-primary rounded-full transition-all"
                                                            style="width: {{ min(100, round($avgProgress)) }}%"></div>
                                                    </div>
                                                    <span class="text-xs text-gray-500 flex-shrink-0">{{ round($avgProgress) }}%</span>
                                                </div>
                                            @else
                                                <span class="text-xs text-gray-400">Sem metas definidas</span>
                                            @endif
                                        </a>
                    @empty
                        <div class="px-6 py-10 text-center text-sm text-gray-400">
                            Nenhum planejamento criado.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
<x-app-layout>
    <x-slot name="title">Dashboard</x-slot>

    <div class="mb-12 flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <h1 class="section-title">Visão Geral</h1>
            <p class="text-gray-500 mt-2 text-sm sm:text-base">Acompanhe as métricas globais e o status da sua agência.</p>
        </div>
        <a href="{{ route('clients.create') }}" class="btn-primary shrink-0">
            <span class="material-symbols-outlined text-[20px]">add</span>
            Novo Cliente
        </a>
    </div>

    {{-- KPIs Globais --}}
    <div class="grid gap-6 grid-cols-1 md:grid-cols-3 mb-12">
        <div class="card p-6 flex flex-col justify-between relative overflow-hidden group">
            <div class="absolute -right-6 -top-6 w-32 h-32 bg-primary/5 rounded transition-transform group-hover:scale-150 duration-700 ease-out"></div>
            <div class="flex items-center justify-between mb-6 relative z-10">
                <div class="text-[11px] font-bold text-gray-500 uppercase tracking-wider">Clientes</div>
                <div class="w-10 h-10 rounded bg-primary/10 flex items-center justify-center text-primary-foreground shadow-sm">
                    <span class="material-symbols-outlined text-[20px]">group</span>
                </div>
            </div>
            <div class="relative z-10">
                <div class="text-4xl font-extrabold text-gray-900 tracking-tight">{{ $stats['clients'] }}</div>
                <div class="text-sm text-gray-400 font-medium mt-1">ativos na plataforma</div>
            </div>
        </div>

        <div class="card p-6 flex flex-col justify-between relative overflow-hidden group">
            <div class="absolute -right-6 -top-6 w-32 h-32 bg-blue-50 rounded transition-transform group-hover:scale-150 duration-700 ease-out"></div>
            <div class="flex items-center justify-between mb-6 relative z-10">
                <div class="text-[11px] font-bold text-gray-500 uppercase tracking-wider">Postagens</div>
                <div class="w-10 h-10 rounded bg-blue-100 flex items-center justify-center text-blue-600 shadow-sm">
                    <span class="material-symbols-outlined text-[20px]">edit_note</span>
                </div>
            </div>
            <div class="relative z-10">
                <div class="text-4xl font-extrabold text-gray-900 tracking-tight">{{ $stats['posts'] }}</div>
                <div class="text-sm text-gray-400 font-medium mt-1">total criadas</div>
            </div>
        </div>

        <div class="card p-6 flex flex-col justify-between relative overflow-hidden group">
            <div class="absolute -right-6 -top-6 w-32 h-32 bg-violet-50 rounded transition-transform group-hover:scale-150 duration-700 ease-out"></div>
            <div class="flex items-center justify-between mb-6 relative z-10">
                <div class="text-[11px] font-bold text-gray-500 uppercase tracking-wider">Campanhas Ativas</div>
                <div class="w-10 h-10 rounded bg-violet-100 flex items-center justify-center text-violet-600 shadow-sm">
                    <span class="material-symbols-outlined text-[20px]">campaign</span>
                </div>
            </div>
            <div class="relative z-10">
                <div class="text-4xl font-extrabold text-gray-900 tracking-tight">{{ $stats['campaigns_active'] }}</div>
                <div class="text-sm text-gray-400 font-medium mt-1">de {{ $stats['campaigns'] }} no total</div>
            </div>
        </div>
    </div>

    <div class="grid gap-8 lg:grid-cols-3">

        {{-- Coluna principal --}}
        <div class="lg:col-span-2 space-y-8">

            {{-- Clientes --}}
            <div class="card p-0 overflow-hidden border-gray-200/60 shadow-sm">
                <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100 bg-white/50 backdrop-blur-sm">
                    <h2 class="font-bold text-gray-900 text-lg">Seus clientes</h2>
                    <a href="{{ route('clients.index') }}" class="text-sm text-primary-foreground font-semibold hover:text-primary transition-colors flex items-center gap-1 group">
                        Ver todos <span class="material-symbols-outlined text-[16px] group-hover:translate-x-0.5 transition-transform">arrow_forward</span>
                    </a>
                </div>
                <div class="divide-y divide-gray-50">
                    @forelse ($clients as $client)
                        <a href="{{ route('clients.show', $client) }}" class="flex items-center gap-4 px-6 py-5 hover:bg-gray-50/80 transition-colors group">
                            <div class="inline-flex h-12 w-12 items-center justify-center rounded bg-primary/10 text-primary-foreground font-bold text-sm flex-shrink-0 group-hover:bg-primary group-hover:text-primary-foreground transition-all duration-300 shadow-sm group-hover:shadow-md group-hover:-translate-y-0.5">
                                {{ strtoupper(substr($client->name, 0, 2)) }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="font-semibold text-base text-gray-900 truncate group-hover:text-primary-foreground transition-colors">{{ $client->name }}</div>
                                <div class="text-sm text-gray-500 mt-0.5 flex items-center gap-2">
                                    <span>{{ $client->niche ?? 'Sem nicho' }}</span>
                                    <span class="w-1 h-1 rounded bg-gray-300"></span>
                                    <span>{{ $client->posts_count }} posts</span>
                                </div>
                            </div>
                            @if ($client->niche)
                                <span class="badge bg-surface text-gray-600 border border-gray-200 group-hover:border-primary/30 transition-colors shadow-sm hidden sm:inline-flex">{{ $client->niche }}</span>
                            @endif
                            <span class="material-symbols-outlined text-gray-300 group-hover:text-primary-foreground transition-all ml-2 group-hover:translate-x-1">chevron_right</span>
                        </a>
                    @empty
                        <div class="px-6 py-12 text-center flex flex-col items-center">
                            <div class="w-16 h-16 rounded bg-gray-50 flex items-center justify-center text-gray-300 mb-4">
                                <span class="material-symbols-outlined text-3xl">inbox</span>
                            </div>
                            <div class="text-gray-500 font-medium">Nenhum cliente cadastrado.</div>
                            <a href="{{ route('clients.create') }}" class="text-primary-foreground font-semibold hover:underline mt-2">Criar primeiro cliente</a>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Sidebar: Planejamentos recentes --}}
        <div>
            <div class="card p-0 overflow-hidden border-gray-200/60 shadow-sm sticky top-24">
                <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100 bg-white/50 backdrop-blur-sm">
                    <h2 class="font-bold text-gray-900 text-lg">Planejamentos</h2>
                </div>
                <div class="divide-y divide-gray-50 bg-white">
                    @forelse ($recentPlannings as $planning)
                        @php
                            $totalGoals = $planning->goals->count();
                            $avgProgress = $totalGoals > 0
                                ? $planning->goals->avg('progress_percent')
                                : null;
                        @endphp
                        <a href="{{ route('plannings.show', [$planning->client, $planning]) }}"
                           class="block px-6 py-5 hover:bg-gray-50/80 transition-colors group">
                            <div class="flex items-start justify-between mb-1">
                                <div class="font-semibold text-sm text-gray-900 truncate group-hover:text-primary-foreground transition-colors pr-2">
                                    {{ $planning->name }}
                                </div>
                                <span class="badge flex-shrink-0 border shadow-sm {{ match($planning->status) {
                                    'ativo' => 'bg-green-50 text-green-700 border-green-200',
                                    'pausado' => 'bg-amber-50 text-amber-700 border-amber-200',
                                    'concluido' => 'bg-blue-50 text-blue-700 border-blue-200',
                                    default => 'bg-gray-50 text-gray-600 border-gray-200'
                                } }}">{{ $planning->status_label }}</span>
                            </div>
                            <div class="text-xs text-gray-500 mb-3 flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-[14px]">apartment</span>
                                {{ $planning->client->name }}
                            </div>
                            @if ($avgProgress !== null)
                                <div class="flex items-center gap-3">
                                    <div class="flex-1 h-2 bg-gray-100 rounded overflow-hidden">
                                        <div class="h-full bg-primary rounded transition-all duration-1000 ease-out"
                                             style="width: {{ min(100, round($avgProgress)) }}%"></div>
                                    </div>
                                    <span class="text-xs font-bold text-gray-600 flex-shrink-0 w-8 text-right">{{ round($avgProgress) }}%</span>
                                </div>
                            @else
                                <span class="text-xs text-gray-400 italic">Sem metas definidas</span>
                            @endif
                        </a>
                    @empty
                        <div class="px-6 py-12 text-center text-sm text-gray-400">
                            Nenhum planejamento criado.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

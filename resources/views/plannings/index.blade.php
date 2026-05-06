<x-app-layout>
    <x-slot name="title">Planejamento · {{ $client->name }}</x-slot>

    <x-slot name="clientBar">
        <x-client-subnav :client="$client" />
    </x-slot>

    <div class="max-w-6xl mx-auto">
        <div class="mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="section-title">Planejamentos</h1>
                <p class="text-gray-500 mt-2">Crie ciclos de planejamento, defina metas e acompanhe campanhas vinculadas do cliente <strong>{{ $client->name }}</strong>.</p>
            </div>
            <a href="{{ route('plannings.create', $client) }}" class="btn-primary inline-flex flex-shrink-0">
                <span class="material-symbols-outlined text-[20px]">add</span>
                Novo planejamento
            </a>
        </div>

        @if ($plannings->isEmpty())
            <div class="card p-12 text-center flex flex-col items-center justify-center border-gray-100 shadow-sm relative overflow-hidden">
                <div class="absolute inset-0 bg-primary/5 blur-3xl rounded-full w-64 h-64 mx-auto top-1/2 -translate-y-1/2 pointer-events-none"></div>
                <div class="w-20 h-20 rounded-full bg-primary/10 flex items-center justify-center mb-6 text-primary-foreground relative z-10">
                    <span class="material-symbols-outlined text-[40px]">flag</span>
                </div>
                <h3 class="font-bold text-2xl text-gray-900 mb-2 relative z-10">Nenhum planejamento</h3>
                <p class="text-gray-500 text-base mb-8 max-w-md relative z-10">Crie um ciclo de planejamento para acompanhar os resultados, metas e performance das campanhas de forma organizada.</p>
                <a href="{{ route('plannings.create', $client) }}" class="btn-primary relative z-10">
                    Criar primeiro planejamento
                </a>
            </div>
        @else
            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                @foreach ($plannings as $planning)
                    <a href="{{ route('plannings.show', [$client, $planning]) }}" class="card p-6 block hover:shadow-md transition-shadow group border border-gray-100 relative overflow-hidden">
                        {{-- Decorative background blur on hover --}}
                        <div class="absolute -right-10 -top-10 w-24 h-24 bg-primary/10 rounded-full blur-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none"></div>

                        <div class="flex items-start justify-between gap-4 mb-5 relative z-10">
                            <div class="w-12 h-12 rounded bg-primary/10 border border-primary/20 flex items-center justify-center group-hover:bg-primary group-hover:border-primary transition-colors flex-shrink-0">
                                <span class="material-symbols-outlined text-primary-foreground group-hover:text-white transition-colors text-[24px]">flag</span>
                            </div>
                            @php
                                $badge = match($planning->status) {
                                    'ativo' => 'bg-green-100 text-green-700 border-green-200',
                                    'pausado' => 'bg-amber-100 text-amber-700 border-amber-200',
                                    'concluido' => 'bg-blue-100 text-blue-700 border-blue-200',
                                    default => 'bg-gray-100 text-gray-600 border-gray-200',
                                };
                            @endphp
                            <span class="badge border {{ $badge }}">{{ $planning->status_label }}</span>
                        </div>
                        
                        <div class="relative z-10 mb-6">
                            <h3 class="font-bold text-lg text-gray-900 group-hover:text-primary-foreground transition-colors mb-1">{{ $planning->name }}</h3>
                            @if ($planning->period_start || $planning->period_end)
                                <p class="text-sm font-medium text-gray-500">
                                    {{ $planning->period_start?->format('d/m/Y') ?? '—' }} até {{ $planning->period_end?->format('d/m/Y') ?? '—' }}
                                </p>
                            @else
                                <p class="text-sm font-medium text-gray-500 italic">Período não definido</p>
                            @endif
                        </div>

                        <div class="grid grid-cols-2 gap-4 pt-4 border-t border-gray-100 relative z-10">
                            <div>
                                <div class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Metas</div>
                                <div class="text-base font-bold text-gray-900 flex items-center gap-1.5">
                                    <span class="material-symbols-outlined text-[16px] text-primary-foreground">track_changes</span>
                                    {{ $planning->goals_count }}
                                </div>
                            </div>
                            <div>
                                <div class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Campanhas</div>
                                <div class="text-base font-bold text-gray-900 flex items-center gap-1.5">
                                    <span class="material-symbols-outlined text-[16px] text-blue-500">campaign</span>
                                    {{ $planning->campaigns_count }}
                                </div>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</x-app-layout>

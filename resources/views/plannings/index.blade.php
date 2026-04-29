<x-app-layout>
    <x-slot name="title">Planejamento · {{ $client->name }}</x-slot>

    <x-slot name="clientBar">
        <x-client-subnav :client="$client" />
    </x-slot>

    <div class="mb-6 flex items-start justify-between gap-4">
        <div>
            <h2 class="text-lg font-semibold text-gray-900">Planejamentos</h2>
            <p class="text-sm text-gray-500 mt-0.5">Crie ciclos de planejamento, defina metas e acompanhe campanhas vinculadas.</p>
        </div>
        <a href="{{ route('plannings.create', $client) }}" class="btn-primary inline-flex">
            <span class="material-symbols-outlined text-[18px]">add</span>
            Novo planejamento
        </a>
    </div>

    @forelse ($plannings as $planning)
        <a href="{{ route('plannings.show', [$client, $planning]) }}"
           class="card p-6 mb-4 block hover:border-primary/40 transition-colors">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div class="flex-1">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-none bg-primary/10 border border-primary/20 flex items-center justify-center">
                            <span class="material-symbols-outlined text-primary-foreground">flag</span>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900">{{ $planning->name }}</h3>
                            @if ($planning->period_start || $planning->period_end)
                                <p class="text-xs text-gray-500 mt-0.5">
                                    {{ $planning->period_start?->format('d/m/Y') ?? '—' }} – {{ $planning->period_end?->format('d/m/Y') ?? '—' }}
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    @php
                        $badge = match($planning->status) {
                            'ativo' => 'bg-green-100 text-green-700',
                            'pausado' => 'bg-amber-100 text-amber-700',
                            'concluido' => 'bg-blue-100 text-blue-700',
                            default => 'bg-gray-100 text-gray-600',
                        };
                    @endphp
                    <span class="badge {{ $badge }}">{{ $planning->status_label }}</span>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4 mt-4">
                <div>
                    <div class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Metas</div>
                    <div class="text-sm font-bold text-gray-900 mt-0.5">{{ $planning->goals_count }}</div>
                </div>
                <div>
                    <div class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Campanhas</div>
                    <div class="text-sm font-bold text-gray-900 mt-0.5">{{ $planning->campaigns_count }}</div>
                </div>
            </div>
        </a>
    @empty
        <div class="card p-12 text-center">
            <span class="material-symbols-outlined text-4xl text-gray-300">flag</span>
            <h3 class="text-sm font-semibold text-gray-900 mt-3">Nenhum planejamento criado</h3>
            <p class="text-sm text-gray-500 mt-1">Crie um planejamento para definir metas e acompanhar campanhas.</p>
            <a href="{{ route('plannings.create', $client) }}" class="btn-primary inline-flex mt-4">
                <span class="material-symbols-outlined text-[18px]">add</span>
                Novo planejamento
            </a>
        </div>
    @endforelse
</x-app-layout>

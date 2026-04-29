<x-app-layout>
    <x-slot name="title">{{ $planning->name }} · {{ $client->name }}</x-slot>

    <x-slot name="clientBar">
        <x-client-subnav :client="$client" />
    </x-slot>

    <div class="mb-6 flex flex-wrap items-start justify-between gap-4">
        <div>
            <a href="{{ route('plannings.index', $client) }}" class="text-sm text-gray-500 hover:text-gray-900">← Planejamentos</a>
            <h2 class="text-lg font-semibold text-gray-900 mt-2">{{ $planning->name }}</h2>
            <div class="text-sm text-gray-500 mt-0.5 flex items-center gap-3">
                @if ($planning->period_start || $planning->period_end)
                    <span>{{ $planning->period_start?->format('d/m/Y') ?? '—' }} – {{ $planning->period_end?->format('d/m/Y') ?? '—' }}</span>
                @endif
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
        <a href="{{ route('plannings.edit', [$client, $planning]) }}" class="btn-secondary inline-flex">
            <span class="material-symbols-outlined text-[18px]">edit</span>
            Editar
        </a>
    </div>

    @if ($planning->notes)
        <div class="card p-4 mb-6 bg-gray-50/50 text-sm text-gray-700">{{ $planning->notes }}</div>
    @endif

    {{-- Seção: Metas --}}
    <section class="mb-8" x-data="{ showAddGoal: false, editingGoalId: null }">
        <div class="flex items-center justify-between mb-3">
            <h3 class="font-semibold text-gray-900">Metas</h3>
            <button @click="showAddGoal = !showAddGoal" class="inline-flex items-center gap-1.5 text-xs font-bold text-primary-foreground hover:underline">
                <span class="material-symbols-outlined text-[16px]">add</span>
                Adicionar meta
            </button>
        </div>

        <div x-show="showAddGoal" x-cloak class="card p-5 mb-3">
            <form method="POST" action="{{ route('goals.store', [$client, $planning]) }}" class="grid md:grid-cols-4 gap-3">
                @csrf
                <input type="text" name="title" required class="input md:col-span-2" placeholder="Título da meta">
                <input type="text" name="unit" class="input" placeholder="Unidade (ex: leads)">
                <input type="number" name="target_value" step="0.01" min="0" class="input" placeholder="Valor alvo">
                <input type="number" name="current_value" step="0.01" min="0" class="input" placeholder="Atual" value="0">
                <input type="text" name="notes" class="input md:col-span-2" placeholder="Notas (opcional)">
                <button type="submit" class="btn-primary md:col-span-1">Adicionar</button>
            </form>
        </div>

        @forelse ($planning->goals as $goal)
            <div class="card p-5 mb-3" x-data="{ editing: false }">
                <div x-show="!editing">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex-1">
                            <h4 class="font-semibold text-gray-900 text-sm">{{ $goal->title }}</h4>
                            @if ($goal->notes)
                                <p class="text-xs text-gray-500 mt-1">{{ $goal->notes }}</p>
                            @endif
                            <div class="mt-3">
                                <div class="flex items-center justify-between text-xs text-gray-600 mb-1">
                                    <span>{{ rtrim(rtrim(number_format((float) $goal->current_value, 2, ',', '.'), '0'), ',') }} / {{ rtrim(rtrim(number_format((float) $goal->target_value, 2, ',', '.'), '0'), ',') }} {{ $goal->unit }}</span>
                                    <span class="font-bold">{{ number_format($goal->progress_percent, 0) }}%</span>
                                </div>
                                <div class="w-full bg-gray-100 rounded-full h-2">
                                    <div class="bg-primary h-2 rounded-full transition-all" style="width: {{ $goal->progress_percent }}%"></div>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center gap-1">
                            <button @click="editing = true" class="p-1.5 hover:bg-gray-100 rounded">
                                <span class="material-symbols-outlined text-[16px] text-gray-500">edit</span>
                            </button>
                            <form method="POST" action="{{ route('goals.destroy', [$client, $planning, $goal]) }}" onsubmit="return confirm('Excluir meta?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-1.5 hover:bg-red-50 rounded">
                                    <span class="material-symbols-outlined text-[16px] text-red-500">delete</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <form x-show="editing" x-cloak method="POST" action="{{ route('goals.update', [$client, $planning, $goal]) }}" class="grid md:grid-cols-4 gap-3">
                    @csrf
                    @method('PUT')
                    <input type="text" name="title" required class="input md:col-span-2" value="{{ $goal->title }}">
                    <input type="text" name="unit" class="input" value="{{ $goal->unit }}" placeholder="Unidade">
                    <input type="number" name="target_value" step="0.01" min="0" class="input" value="{{ $goal->target_value }}" placeholder="Alvo">
                    <input type="number" name="current_value" step="0.01" min="0" class="input" value="{{ $goal->current_value }}" placeholder="Atual">
                    <input type="text" name="notes" class="input md:col-span-2" value="{{ $goal->notes }}" placeholder="Notas">
                    <div class="md:col-span-1 flex gap-2">
                        <button type="submit" class="btn-primary flex-1">Salvar</button>
                        <button type="button" @click="editing = false" class="btn-secondary">×</button>
                    </div>
                </form>
            </div>
        @empty
            <div class="card p-8 text-center text-sm text-gray-400">
                Nenhuma meta definida. Use "Adicionar meta" para começar.
            </div>
        @endforelse
    </section>

    {{-- Seção: Campanhas vinculadas --}}
    <section x-data="{ showAttach: false }">
        <div class="flex items-center justify-between mb-3">
            <h3 class="font-semibold text-gray-900">Campanhas vinculadas</h3>
            @if ($availableCampaigns->isNotEmpty())
                <button @click="showAttach = !showAttach" class="inline-flex items-center gap-1.5 text-xs font-bold text-primary-foreground hover:underline">
                    <span class="material-symbols-outlined text-[16px]">add_link</span>
                    Vincular campanha
                </button>
            @endif
        </div>

        <div x-show="showAttach" x-cloak class="card p-5 mb-3">
            <form method="POST" action="{{ route('plannings.attach-campaign', [$client, $planning]) }}">
                @csrf
                <p class="text-xs font-semibold text-gray-600 mb-2">Selecione campanhas para vincular</p>
                <div class="space-y-2 max-h-72 overflow-y-auto">
                    @foreach ($availableCampaigns as $c)
                        <label class="flex items-center gap-2 p-2 rounded hover:bg-gray-50 cursor-pointer">
                            <input type="checkbox" name="campaign_ids[]" value="{{ $c->id }}" class="rounded">
                            <span class="text-sm">{{ $c->name }}</span>
                            <span class="badge bg-gray-100 text-gray-600 ml-auto">{{ $c->meta_status_label }}</span>
                        </label>
                    @endforeach
                </div>
                <div class="mt-3 flex justify-end gap-2">
                    <button type="button" @click="showAttach = false" class="btn-secondary">Cancelar</button>
                    <button type="submit" class="btn-primary">Vincular selecionadas</button>
                </div>
            </form>
        </div>

        @forelse ($planning->campaigns as $campaign)
            <div class="card p-5 mb-3">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div class="flex-1">
                        <a href="{{ route('campaigns.show', [$client, $campaign]) }}" class="font-semibold text-gray-900 hover:text-primary-foreground text-sm">{{ $campaign->name }}</a>
                        <div class="text-xs text-gray-500 mt-0.5">
                            Status Meta: <span class="font-medium">{{ $campaign->meta_status_label }}</span>
                            @if ($campaign->start_date)
                                · {{ $campaign->start_date->format('d/m/Y') }} – {{ $campaign->stop_date?->format('d/m/Y') ?? '—' }}
                            @endif
                        </div>
                        @php $insights = $campaign->insights ?? []; @endphp
                        @if (! empty($insights))
                            <div class="grid grid-cols-5 gap-3 mt-3">
                                @foreach ([
                                    ['Alcance', $insights['reach'] ?? null],
                                    ['Impressões', $insights['impressions'] ?? null],
                                    ['Cliques', $insights['clicks'] ?? null],
                                    ['CTR', isset($insights['ctr']) ? number_format((float)$insights['ctr'], 2, ',', '.') . '%' : null],
                                    ['CPC', isset($insights['cpc']) ? 'R$ ' . number_format((float)$insights['cpc'], 2, ',', '.') : null],
                                ] as [$label, $value])
                                    <div class="text-center">
                                        <div class="text-[10px] font-semibold text-gray-400 uppercase">{{ $label }}</div>
                                        <div class="text-sm font-bold text-gray-900 mt-0.5">{{ $value ?? '—' }}</div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                    <div class="flex items-center gap-2">
                        <form method="POST" action="{{ route('plannings.campaign-status', [$client, $planning, $campaign]) }}" class="flex items-center gap-2">
                            @csrf
                            @method('PATCH')
                            <select name="local_status" onchange="this.form.submit()" class="input py-1.5 text-xs">
                                @foreach (['em_execucao' => 'Em execução', 'pausada' => 'Pausada', 'concluida' => 'Concluída'] as $v => $l)
                                    <option value="{{ $v }}" @selected($campaign->pivot->local_status === $v)>{{ $l }}</option>
                                @endforeach
                            </select>
                        </form>
                        <form method="POST" action="{{ route('plannings.detach-campaign', [$client, $planning, $campaign]) }}" onsubmit="return confirm('Desvincular campanha?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-1.5 hover:bg-red-50 rounded">
                                <span class="material-symbols-outlined text-[16px] text-red-500">link_off</span>
                            </button>
                        </form>
                    </div>
                </div>
                @if ($campaign->pivot->notes)
                    <p class="text-xs text-gray-500 mt-2 italic">{{ $campaign->pivot->notes }}</p>
                @endif
            </div>
        @empty
            <div class="card p-8 text-center text-sm text-gray-400">
                Nenhuma campanha vinculada.
                @if ($client->campaigns->isEmpty())
                    <a href="{{ route('clients.campaigns', $client) }}" class="text-primary-foreground font-semibold hover:underline ml-1">Sincronize campanhas com o Meta</a> primeiro.
                @endif
            </div>
        @endforelse
    </section>
</x-app-layout>

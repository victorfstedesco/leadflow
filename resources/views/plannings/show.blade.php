<x-app-layout>
    <x-slot name="title">{{ $planning->name }} · {{ $client->name }}</x-slot>

    <x-slot name="clientBar">
        <x-client-subnav :client="$client" />
    </x-slot>

    {{-- Header --}}
    <div class="mb-6 flex flex-wrap items-start justify-between gap-4">
        <div>
            <a href="{{ route('plannings.index', $client) }}" class="text-sm text-gray-500 hover:text-gray-900">← Planejamentos</a>
            <h2 class="text-lg font-semibold text-gray-900 mt-2">{{ $planning->name }}</h2>
            <div class="text-sm text-gray-500 mt-0.5 flex items-center gap-3">
                @if ($planning->period_start || $planning->period_end)
                    <span>{{ $planning->period_start?->format('d/m/Y') ?? '—' }} – {{ $planning->period_end?->format('d/m/Y') ?? '—' }}</span>
                @endif
                @php $badge = match($planning->status) {
                    'ativo'    => 'bg-green-100 text-green-700',
                    'pausado'  => 'bg-amber-100 text-amber-700',
                    'concluido'=> 'bg-blue-100 text-blue-700',
                    default    => 'bg-gray-100 text-gray-600',
                }; @endphp
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

    <div class="grid gap-6 lg:grid-cols-3">

        {{-- Metas --}}
        <div class="lg:col-span-2">
            <div class="card">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-900">Metas</h3>
                    <span class="text-xs text-gray-400 flex items-center gap-1">
                        <span class="material-symbols-outlined text-[14px]">sync</span>
                        Atualizadas ao sincronizar campanhas
                    </span>
                </div>

                @forelse ($planning->goals as $goal)
                    @php
                        $pct = $goal->progress_percent;
                        $barColor = $pct >= 100 ? 'bg-green-500' : ($pct >= 60 ? 'bg-primary' : 'bg-amber-400');
                    @endphp
                    <div class="flex items-start gap-4 px-6 py-5 border-b border-gray-50 last:border-0">
                        <div class="w-10 h-10  bg-primary/10 flex items-center justify-center flex-shrink-0">
                            <span class="material-symbols-outlined text-primary-foreground text-[18px]">{{ $goal->category_icon }}</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between gap-2 mb-2">
                                <span class="text-sm font-semibold text-gray-900">{{ $goal->category_label }}</span>
                                <span class="text-sm font-bold {{ $pct >= 100 ? 'text-green-600' : 'text-gray-700' }}">
                                    {{ number_format($pct, 0) }}%
                                </span>
                            </div>
                            <div class="w-full bg-gray-100  h-2 mb-1.5">
                                <div class="{{ $barColor }} h-2  transition-all" style="width: {{ min(100, $pct) }}%"></div>
                            </div>
                            <div class="flex items-center justify-between text-xs text-gray-400">
                                <span>
                                    Atual:
                                    @if (in_array($goal->category, ['cpc', 'spend']))
                                        R$ {{ number_format((float)$goal->current_value, 2, ',', '.') }}
                                    @elseif ($goal->category === 'ctr')
                                        {{ number_format((float)$goal->current_value, 2, ',', '.') }}%
                                    @else
                                        {{ number_format((float)$goal->current_value, 0, ',', '.') }}
                                    @endif
                                </span>
                                <span>
                                    Alvo:
                                    @if (in_array($goal->category, ['cpc', 'spend']))
                                        R$ {{ number_format((float)$goal->target_value, 2, ',', '.') }}
                                    @elseif ($goal->category === 'ctr')
                                        {{ number_format((float)$goal->target_value, 2, ',', '.') }}%
                                    @else
                                        {{ number_format((float)$goal->target_value, 0, ',', '.') }} {{ $goal->category_unit }}
                                    @endif
                                </span>
                            </div>
                        </div>
                        <form method="POST" action="{{ route('goals.destroy', [$client, $planning, $goal]) }}"
                              onsubmit="return confirm('Excluir meta?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="p-1.5 hover:bg-red-50  mt-1">
                                <span class="material-symbols-outlined text-[16px] text-red-400">delete</span>
                            </button>
                        </form>
                    </div>
                @empty
                    <div class="px-6 py-10 text-center text-sm text-gray-400">
                        Nenhuma meta definida neste planejamento.
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Campanhas vinculadas (sidebar) --}}
        <div x-data="{ showAttach: false }">
            <div class="card">
                <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-900">Campanhas</h3>
                    @if ($availableCampaigns->isNotEmpty())
                        <button @click="showAttach = !showAttach"
                                class="text-xs font-bold text-primary-foreground hover:underline flex items-center gap-0.5">
                            <span class="material-symbols-outlined text-[15px]">add_link</span>
                            Vincular
                        </button>
                    @endif
                </div>

                {{-- Attach form --}}
                <div x-show="showAttach" x-cloak class="px-5 py-4 border-b border-gray-100 bg-gray-50/50">
                    <form method="POST" action="{{ route('plannings.attach-campaign', [$client, $planning]) }}">
                        @csrf
                        <div class="space-y-1.5 max-h-48 overflow-y-auto mb-3">
                            @foreach ($availableCampaigns as $c)
                                <label class="flex items-center gap-2 p-1.5  hover:bg-white cursor-pointer text-sm">
                                    <input type="checkbox" name="campaign_ids[]" value="{{ $c->id }}" class=" ">
                                    <span class="truncate">{{ $c->name }}</span>
                                </label>
                            @endforeach
                        </div>
                        <div class="flex gap-2">
                            <button type="button" @click="showAttach = false" class="btn-secondary text-xs py-1.5 px-3">Cancelar</button>
                            <button type="submit" class="btn-primary text-xs py-1.5 px-3">Vincular</button>
                        </div>
                    </form>
                </div>

                <div class="divide-y divide-gray-50">
                    @forelse ($planning->campaigns as $campaign)
                        <div class="px-5 py-4">
                            <div class="flex items-start justify-between gap-2">
                                <div class="flex-1 min-w-0">
                                    <a href="{{ route('campaigns.show', [$client, $campaign]) }}"
                                       class="text-sm font-medium text-gray-900 hover:text-primary-foreground truncate block">
                                        {{ $campaign->name }}
                                    </a>
                                    <div class="flex items-center gap-2 mt-1">
                                        <span class="w-1.5 h-1.5  flex-shrink-0
                                            {{ $campaign->meta_status === 'ACTIVE' ? 'bg-green-400' : 'bg-gray-300' }}"></span>
                                        <span class="text-xs text-gray-400">{{ $campaign->meta_status_label }}</span>
                                    </div>
                                </div>
                                <form method="POST" action="{{ route('plannings.detach-campaign', [$client, $planning, $campaign]) }}"
                                      onsubmit="return confirm('Desvincular?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-1 hover:bg-red-50  ">
                                        <span class="material-symbols-outlined text-[15px] text-red-400">link_off</span>
                                    </button>
                                </form>
                            </div>

                            {{-- Local status --}}
                            <form method="POST" action="{{ route('plannings.campaign-status', [$client, $planning, $campaign]) }}" class="mt-2">
                                @csrf @method('PATCH')
                                <select name="local_status" onchange="this.form.submit()" class="input py-1 text-xs">
                                    @foreach (['em_execucao' => 'Em execução', 'pausada' => 'Pausada', 'concluida' => 'Concluída'] as $v => $l)
                                        <option value="{{ $v }}" @selected($campaign->pivot->local_status === $v)>{{ $l }}</option>
                                    @endforeach
                                </select>
                            </form>
                        </div>
                    @empty
                        <div class="px-5 py-8 text-center text-sm text-gray-400">
                            Nenhuma campanha vinculada.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

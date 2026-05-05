<x-app-layout>
    <x-slot name="title">{{ $planning->name }} · {{ $client->name }}</x-slot>

    <x-slot name="clientBar">
        <x-client-subnav :client="$client" />
    </x-slot>

    <div class="max-w-6xl mx-auto">
        {{-- Header --}}
        <div class="mb-8">
            <a href="{{ route('plannings.index', $client) }}" class="inline-flex items-center gap-2 text-gray-500 font-medium text-sm mb-4 hover:text-gray-900 transition-colors">
                <span class="material-symbols-outlined text-[18px]">arrow_back</span>
                Voltar para Planejamentos
            </a>
            
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <h1 class="section-title">{{ $planning->name }}</h1>
                    <div class="flex items-center gap-3 mt-2">
                        @if ($planning->period_start || $planning->period_end)
                            <span class="text-sm font-medium text-gray-500 flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-[16px]">calendar_today</span>
                                {{ $planning->period_start?->format('d/m/Y') ?? '—' }} até {{ $planning->period_end?->format('d/m/Y') ?? '—' }}
                            </span>
                        @endif
                        @php $badge = match($planning->status) {
                            'ativo'    => 'bg-green-100 text-green-700 border-green-200',
                            'pausado'  => 'bg-amber-100 text-amber-700 border-amber-200',
                            'concluido'=> 'bg-blue-100 text-blue-700 border-blue-200',
                            default    => 'bg-gray-100 text-gray-600 border-gray-200',
                        }; @endphp
                        <span class="badge border {{ $badge }}">{{ $planning->status_label }}</span>
                    </div>
                </div>
                <a href="{{ route('plannings.edit', [$client, $planning]) }}" class="btn-secondary inline-flex flex-shrink-0">
                    <span class="material-symbols-outlined text-[18px]">edit</span>
                    Editar Ciclo
                </a>
            </div>
        </div>

        @if ($planning->notes)
            <div class="card p-6 mb-8 bg-amber-50/50 border-amber-100/50 shadow-sm relative overflow-hidden">
                <div class="absolute -right-5 -top-5 w-20 h-20 bg-amber-200 rounded-full blur-2xl opacity-40 pointer-events-none"></div>
                <div class="relative z-10">
                    <h3 class="text-xs font-bold text-amber-800 uppercase tracking-wider mb-2 flex items-center gap-1">
                        <span class="material-symbols-outlined text-[16px]">info</span>
                        Contexto do Ciclo
                    </h3>
                    <p class="text-sm text-amber-900 leading-relaxed">{{ $planning->notes }}</p>
                </div>
            </div>
        @endif

        <div class="grid gap-8 lg:grid-cols-3">

            {{-- Metas --}}
            <div class="lg:col-span-2 space-y-6">
                <div class="card p-0 overflow-hidden border-gray-100 shadow-sm">
                    <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100 bg-gray-50/50">
                        <h2 class="font-bold text-gray-900 text-lg flex items-center gap-2">
                            <span class="material-symbols-outlined text-primary-foreground">track_changes</span>
                            Acompanhamento de Metas
                        </h2>
                        <span class="text-xs text-gray-500 font-medium flex items-center gap-1.5 bg-white px-3 py-1.5 rounded-full border border-gray-200 shadow-sm">
                            <span class="material-symbols-outlined text-[14px]">sync</span>
                            Sincroniza com Campanhas
                        </span>
                    </div>

                    <div class="divide-y divide-gray-50">
                        @forelse ($planning->goals as $goal)
                            @php
                                $pct = $goal->progress_percent;
                                $barColor = $pct >= 100 ? 'bg-green-500' : ($pct >= 60 ? 'bg-primary' : 'bg-amber-400');
                                $textColor = $pct >= 100 ? 'text-green-600' : 'text-gray-900';
                            @endphp
                            <div class="px-6 py-6 hover:bg-gray-50/50 transition-colors group">
                                <div class="flex items-start gap-5">
                                    <div class="w-12 h-12 rounded bg-primary/10 border border-primary/20 flex items-center justify-center flex-shrink-0 group-hover:bg-white group-hover:border-primary/40 transition-colors">
                                        <span class="material-symbols-outlined text-primary-foreground text-[22px]">{{ $goal->category_icon }}</span>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between mb-3">
                                            <span class="font-bold text-gray-900 text-base">{{ $goal->category_label }}</span>
                                            
                                            <form method="POST" action="{{ route('goals.destroy', [$client, $planning, $goal]) }}"
                                                  onsubmit="return confirm('Tem certeza que deseja excluir esta meta?')" class="opacity-0 group-hover:opacity-100 transition-opacity">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="text-xs font-semibold text-red-500 hover:text-red-700 flex items-center gap-1">
                                                    Excluir
                                                </button>
                                            </form>
                                        </div>

                                        <div class="flex items-end justify-between mb-2">
                                            <div>
                                                <span class="text-2xl font-black {{ $textColor }}">
                                                    @if (in_array($goal->category, ['cpc', 'spend']))
                                                        R$ {{ number_format((float)$goal->current_value, 2, ',', '.') }}
                                                    @elseif ($goal->category === 'ctr')
                                                        {{ number_format((float)$goal->current_value, 2, ',', '.') }}%
                                                    @else
                                                        {{ number_format((float)$goal->current_value, 0, ',', '.') }}
                                                    @endif
                                                </span>
                                                <span class="text-sm font-medium text-gray-400 ml-1">
                                                    / 
                                                    @if (in_array($goal->category, ['cpc', 'spend']))
                                                        R$ {{ number_format((float)$goal->target_value, 2, ',', '.') }}
                                                    @elseif ($goal->category === 'ctr')
                                                        {{ number_format((float)$goal->target_value, 2, ',', '.') }}%
                                                    @else
                                                        {{ number_format((float)$goal->target_value, 0, ',', '.') }} {{ $goal->category_unit }}
                                                    @endif
                                                </span>
                                            </div>
                                            <span class="text-sm font-bold {{ $textColor }}">{{ number_format($pct, 0) }}%</span>
                                        </div>

                                        <div class="w-full bg-gray-100 rounded-full h-2.5 overflow-hidden">
                                            <div class="{{ $barColor }} h-full rounded-full transition-all duration-1000 ease-out" style="width: {{ min(100, max(2, $pct)) }}%"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="px-6 py-16 text-center">
                                <div class="w-16 h-16 rounded-full bg-gray-50 flex items-center justify-center mx-auto mb-4 text-gray-300">
                                    <span class="material-symbols-outlined text-[32px]">sports_score</span>
                                </div>
                                <h3 class="text-base font-bold text-gray-900 mb-1">Nenhuma meta definida</h3>
                                <p class="text-sm text-gray-500">Adicione metas editando este planejamento.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Campanhas vinculadas (sidebar) --}}
            <div x-data="{ showAttach: false }" class="space-y-6">
                <div class="card p-0 border-gray-100 shadow-sm relative overflow-hidden">
                    <div class="absolute -right-10 -bottom-10 w-32 h-32 bg-blue-50 rounded-full blur-2xl pointer-events-none"></div>
                    
                    <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100 relative z-10">
                        <h2 class="font-bold text-gray-900 text-lg flex items-center gap-2">
                            <span class="material-symbols-outlined text-blue-500">campaign</span>
                            Campanhas
                        </h2>
                        @if ($availableCampaigns->isNotEmpty())
                            <button @click="showAttach = !showAttach"
                                    class="text-xs font-bold text-primary-foreground hover:text-primary transition-colors flex items-center gap-1 bg-primary/10 px-3 py-1.5 rounded">
                                <span class="material-symbols-outlined text-[16px]">add</span>
                                Vincular
                            </button>
                        @endif
                    </div>

                    {{-- Attach form --}}
                    <div x-show="showAttach" x-collapse x-cloak class="border-b border-gray-100 bg-gray-50/50 relative z-10">
                        <form method="POST" action="{{ route('plannings.attach-campaign', [$client, $planning]) }}" class="p-5">
                            @csrf
                            <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">Selecione campanhas ativas</p>
                            <div class="space-y-2 max-h-60 overflow-y-auto mb-4 custom-scrollbar pr-2">
                                @foreach ($availableCampaigns as $c)
                                    <label class="flex items-center gap-3 p-2 rounded hover:bg-white border border-transparent hover:border-gray-200 cursor-pointer transition-colors text-sm">
                                        <input type="checkbox" name="campaign_ids[]" value="{{ $c->id }}" class="text-primary focus:ring-primary rounded border-gray-300">
                                        <span class="truncate font-medium text-gray-700">{{ $c->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                            <div class="flex gap-2 justify-end pt-3 border-t border-gray-200">
                                <button type="button" @click="showAttach = false" class="text-sm font-semibold text-gray-500 hover:text-gray-900 transition-colors px-3 py-2">Cancelar</button>
                                <button type="submit" class="btn-primary py-2 text-sm">Vincular Selecionadas</button>
                            </div>
                        </form>
                    </div>

                    <div class="divide-y divide-gray-50 relative z-10 bg-white">
                        @forelse ($planning->campaigns as $campaign)
                            <div class="p-6 hover:bg-gray-50/50 transition-colors group">
                                <div class="flex items-start justify-between gap-3 mb-3">
                                    <div class="flex-1 min-w-0">
                                        <a href="{{ route('campaigns.show', [$client, $campaign]) }}"
                                           class="text-sm font-bold text-gray-900 hover:text-primary-foreground transition-colors block leading-snug">
                                            {{ $campaign->name }}
                                        </a>
                                        <div class="flex items-center gap-1.5 mt-1.5">
                                            <span class="w-2 h-2 rounded-full flex-shrink-0
                                                {{ $campaign->meta_status === 'ACTIVE' ? 'bg-green-500' : 'bg-gray-300' }}"></span>
                                            <span class="text-[11px] font-bold text-gray-500 uppercase tracking-wider">{{ $campaign->meta_status_label }}</span>
                                        </div>
                                    </div>
                                    <form method="POST" action="{{ route('plannings.detach-campaign', [$client, $planning, $campaign]) }}"
                                          onsubmit="return confirm('Tem certeza que deseja desvincular esta campanha?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="w-8 h-8 rounded flex items-center justify-center text-gray-400 hover:bg-red-50 hover:text-red-500 transition-colors opacity-0 group-hover:opacity-100" title="Desvincular">
                                            <span class="material-symbols-outlined text-[18px]">link_off</span>
                                        </button>
                                    </form>
                                </div>

                                {{-- Local status --}}
                                <div class="pt-3 border-t border-gray-100">
                                    <form method="POST" action="{{ route('plannings.campaign-status', [$client, $planning, $campaign]) }}">
                                        @csrf @method('PATCH')
                                        <div class="flex items-center justify-between">
                                            <span class="text-xs font-semibold text-gray-500">Status no Ciclo:</span>
                                            <select name="local_status" onchange="this.form.submit()" class="input py-1 text-xs w-32 bg-gray-50">
                                                @foreach (['em_execucao' => 'Em execução', 'pausada' => 'Pausada', 'concluida' => 'Concluída'] as $v => $l)
                                                    <option value="{{ $v }}" @selected($campaign->pivot->local_status === $v)>{{ $l }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <div class="px-6 py-12 text-center">
                                <span class="material-symbols-outlined text-3xl text-gray-300 mb-2">link_off</span>
                                <p class="text-sm font-medium text-gray-500">Nenhuma campanha associada.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<x-app-layout>
    <x-slot name="title">Novo planejamento · {{ $client->name }}</x-slot>

    <x-slot name="clientBar">
        <x-client-subnav :client="$client" />
    </x-slot>

    <div class="max-w-4xl mx-auto" x-data="planningForm()">
        <div class="mb-10">
            <a href="{{ route('plannings.index', $client) }}" class="inline-flex items-center gap-2 text-gray-500 font-medium text-sm mb-4 hover:text-gray-900 transition-colors">
                <span class="material-symbols-outlined text-[18px]">arrow_back</span>
                Voltar para Planejamentos
            </a>
            <h1 class="section-title">Novo Planejamento</h1>
            <p class="text-gray-500 mt-2">Defina o ciclo, vincule campanhas e escolha as metas que deseja alcançar com <strong>{{ $client->name }}</strong>.</p>
        </div>

        <form method="POST" action="{{ route('plannings.store', $client) }}" id="planning-form" class="grid md:grid-cols-3 gap-8">
            @csrf

            <div class="md:col-span-2 space-y-6">
                {{-- Informações Básicas --}}
                <div class="card p-8 border-gray-100 shadow-sm relative overflow-hidden">
                    <div class="absolute -right-10 -top-10 w-32 h-32 bg-primary/5 rounded-full blur-2xl pointer-events-none"></div>
                    <h2 class="font-bold text-gray-900 text-lg mb-6 relative z-10">Informações Básicas</h2>
                    
                    <div class="space-y-6 relative z-10">
                        <div>
                            <label class="label">Nome da estratégia <span class="text-red-500">*</span></label>
                            <input type="text" name="name" required maxlength="120" class="input font-semibold"
                                   placeholder="Ex: Campanha Q1 2025" value="{{ old('name') }}">
                            @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="label">Início</label>
                                <input type="date" name="period_start" class="input" value="{{ old('period_start') }}">
                            </div>
                            <div>
                                <label class="label">Fim</label>
                                <input type="date" name="period_end" class="input" value="{{ old('period_end') }}">
                            </div>
                        </div>

                        <div>
                            <label class="label flex justify-between items-center">
                                <span>Contexto e Observações</span>
                                <span class="text-gray-400 font-normal normal-case tracking-normal text-xs">Opcional</span>
                            </label>
                            <textarea name="notes" rows="4" class="input text-sm" placeholder="Contexto ou objetivos gerais...">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- Metas --}}
                <div class="card p-8 border-gray-100 shadow-sm relative overflow-hidden">
                    <div class="absolute -left-10 -bottom-10 w-32 h-32 bg-amber-50 rounded-full blur-2xl pointer-events-none"></div>
                    <div class="mb-6 relative z-10">
                        <h2 class="font-bold text-gray-900 text-lg">Metas de Performance</h2>
                        <p class="text-sm text-gray-500 mt-1">Selecione o que deseja monitorar e defina o alvo. Valores atualizam automaticamente com o Meta Ads.</p>
                    </div>

                    <div class="grid grid-cols-2 gap-3 relative z-10 mb-6">
                        @foreach (\App\Models\PlanningGoal::CATEGORIES as $key => $cat)
                            <label class="flex items-center gap-3 p-3 rounded border cursor-pointer transition-all duration-200"
                                   :class="hasGoal('{{ $key }}') ? 'bg-primary/5 border-primary/30 shadow-sm' : 'bg-white border-gray-200 hover:bg-gray-50'">
                                <div class="w-4 h-4 rounded border flex items-center justify-center flex-shrink-0 transition-colors"
                                     :class="hasGoal('{{ $key }}') ? 'bg-primary border-primary' : 'bg-white border-gray-300'">
                                    <svg x-show="hasGoal('{{ $key }}')" class="w-3 h-3 text-white" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-center gap-1.5">
                                        <span class="material-symbols-outlined text-[16px] text-gray-500" :class="hasGoal('{{ $key }}') ? 'text-primary-foreground' : ''">{{ $cat['icon'] }}</span>
                                        <span class="text-sm font-medium text-gray-700">{{ $cat['label'] }}</span>
                                    </div>
                                    <span class="text-xs text-gray-400 mt-0.5 block">{{ $cat['unit'] }}</span>
                                </div>
                                <input type="checkbox" class="sr-only" @change="toggleGoal('{{ $key }}')">
                            </label>
                        @endforeach
                    </div>

                    {{-- Target inputs for selected goals --}}
                    <div class="space-y-3 relative z-10" x-show="goals.length > 0" x-cloak>
                        <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Definir Alvos</h3>
                        <template x-for="(goal, index) in goals" :key="goal.category">
                            <div class="flex items-center justify-between p-3 rounded bg-gray-50 border border-gray-100">
                                <span class="text-sm font-semibold text-gray-900" x-text="categoryLabel(goal.category)"></span>
                                <div class="flex items-center gap-2">
                                    <input type="hidden" :name="`goals[${index}][category]`" :value="goal.category">
                                    <input type="number" :name="`goals[${index}][target_value]`" x-model="goal.target_value" min="0" step="any"
                                           class="input w-32 py-1.5 text-sm bg-white" placeholder="Alvo" required>
                                    <span class="text-xs font-bold text-gray-400 w-8" x-text="categoryUnit(goal.category)"></span>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                {{-- Campanhas --}}
                <div class="card p-8 border-gray-100 shadow-sm relative overflow-hidden">
                    <div class="absolute -right-10 -bottom-10 w-32 h-32 bg-blue-50 rounded-full blur-2xl pointer-events-none"></div>
                    <div class="mb-6 relative z-10">
                        <h2 class="font-bold text-gray-900 text-lg">Campanhas</h2>
                        <p class="text-sm text-gray-500 mt-1">Quais campanhas farão parte deste ciclo?</p>
                    </div>

                    <div class="relative z-10">
                        @if ($campaigns->isEmpty())
                            <div class="p-6 rounded border border-dashed border-gray-200 text-center text-sm text-gray-500 bg-gray-50/50">
                                Nenhuma campanha sincronizada do Meta.
                                <a href="{{ route('clients.campaigns', $client) }}" class="text-primary-foreground font-semibold hover:underline block mt-2">Sincronizar Agora →</a>
                            </div>
                        @else
                            <div class="space-y-2 max-h-80 overflow-y-auto pr-1 custom-scrollbar">
                                @foreach ($campaigns as $campaign)
                                    <label class="flex items-start gap-3 p-3 rounded border cursor-pointer transition-all duration-200 bg-white hover:bg-gray-50 border-gray-200"
                                           :class="selectedCampaigns.includes({{ $campaign->id }}) ? '!bg-primary/5 !border-primary/30 shadow-sm' : ''">
                                        <div class="w-4 h-4 mt-0.5 rounded border flex items-center justify-center flex-shrink-0 transition-colors bg-white border-gray-300"
                                             :class="selectedCampaigns.includes({{ $campaign->id }}) ? '!bg-primary !border-primary' : ''">
                                            <svg x-show="selectedCampaigns.includes({{ $campaign->id }})" class="w-3 h-3 text-white" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                            </svg>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="text-sm font-semibold text-gray-900 truncate mb-0.5">{{ $campaign->name }}</div>
                                            <div class="flex items-center gap-1.5 text-[11px] text-gray-500 font-medium">
                                                <span class="w-1.5 h-1.5 rounded-full {{ $campaign->meta_status === 'ACTIVE' ? 'bg-green-500' : 'bg-gray-300' }}"></span>
                                                {{ $campaign->meta_status_label }}
                                            </div>
                                        </div>
                                        <input type="checkbox" name="campaigns[]" value="{{ $campaign->id }}" class="sr-only" @change="toggleCampaign({{ $campaign->id }})">
                                    </label>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Submit --}}
                <div class="card p-6 border-gray-100 shadow-sm bg-gray-50/50 flex flex-col items-center text-center">
                    <button type="submit" class="btn-primary w-full">
                        <span class="material-symbols-outlined text-[20px]">save</span>
                        Criar Planejamento
                    </button>
                    <a href="{{ route('plannings.index', $client) }}" class="text-sm font-semibold text-gray-500 hover:text-gray-900 mt-4 transition-colors">
                        Cancelar
                    </a>
                </div>
            </div>
        </form>
    </div>

    <script>
        const CATEGORIES = @json(\App\Models\PlanningGoal::CATEGORIES);

        function planningForm() {
            return {
                goals: [],
                selectedCampaigns: [],
                hasGoal(category) {
                    return this.goals.some(g => g.category === category);
                },
                toggleGoal(category) {
                    const idx = this.goals.findIndex(g => g.category === category);
                    if (idx === -1) {
                        this.goals.push({ category, target_value: '' });
                    } else {
                        this.goals.splice(idx, 1);
                    }
                },
                toggleCampaign(id) {
                    const idx = this.selectedCampaigns.indexOf(id);
                    if (idx === -1) {
                        this.selectedCampaigns.push(id);
                    } else {
                        this.selectedCampaigns.splice(idx, 1);
                    }
                },
                categoryLabel(cat) {
                    return CATEGORIES[cat]?.label ?? cat;
                },
                categoryUnit(cat) {
                    return CATEGORIES[cat]?.unit ?? '';
                }
            }
        }
    </script>
</x-app-layout>

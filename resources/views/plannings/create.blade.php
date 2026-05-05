<x-app-layout>
    <x-slot name="title">Novo planejamento · {{ $client->name }}</x-slot>

    <x-slot name="clientBar">
        <x-client-subnav :client="$client" />
    </x-slot>

    <div class="max-w-xl mx-auto" x-data="planningWizard()">

        {{-- Progress bar --}}
        <div class="mb-8">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Novo planejamento</span>
                <span class="text-xs text-gray-400" x-text="`Passo ${step} de 3`"></span>
            </div>
            <div class="h-1.5 bg-gray-100  overflow-hidden">
                <div class="h-full bg-primary transition-all duration-300 ease-out  "
                     :style="`width: ${(step / 3) * 100}%`"></div>
            </div>
            <div class="flex justify-between mt-2">
                <template x-for="(label, i) in ['Informações', 'Campanhas', 'Metas']" :key="i">
                    <span class="text-xs font-medium transition-colors"
                          :class="step > i ? 'text-primary-foreground' : 'text-gray-400'"
                          x-text="label"></span>
                </template>
            </div>
        </div>

        <form method="POST" action="{{ route('plannings.store', $client) }}" id="planning-form" @submit.prevent="submitForm">
            @csrf

            {{-- Step 1: Informações --}}
            <div x-show="step === 1" x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 translate-x-4"
                 x-transition:enter-end="opacity-100 translate-x-0">
                <div class="card p-8 space-y-6">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">Sobre o planejamento</h2>
                        <p class="text-sm text-gray-500 mt-1">Dê um nome e defina o período.</p>
                    </div>

                    <div>
                        <label class="label">Nome <span class="text-red-400">*</span></label>
                        <input type="text" x-model="form.name" maxlength="120" class="input"
                               placeholder="Ex: Campanha Q1 2025" @keydown.enter.prevent="step1Next">
                        <p x-show="errors.name" x-text="errors.name" class="text-red-500 text-xs mt-1"></p>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="label">Início</label>
                            <input type="date" x-model="form.period_start" class="input">
                        </div>
                        <div>
                            <label class="label">Fim</label>
                            <input type="date" x-model="form.period_end" class="input">
                        </div>
                    </div>

                    <div>
                        <label class="label">Observações <span class="text-gray-400 font-normal">(opcional)</span></label>
                        <textarea x-model="form.notes" rows="2" class="input" placeholder="Contexto ou objetivos gerais..."></textarea>
                    </div>
                </div>

                <div class="flex justify-between mt-4">
                    <a href="{{ route('plannings.index', $client) }}" class="btn-outline">Cancelar</a>
                    <button type="button" @click="step1Next" class="btn-primary flex items-center gap-1">
                        Próximo
                        <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
                    </button>
                </div>
            </div>

            {{-- Step 2: Campanhas --}}
            <div x-show="step === 2" x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 translate-x-4"
                 x-transition:enter-end="opacity-100 translate-x-0">
                <div class="card p-8 space-y-5">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">Vincular campanhas</h2>
                        <p class="text-sm text-gray-500 mt-1">Selecione ao menos uma campanha para monitorar.</p>
                    </div>

                    @if ($campaigns->isEmpty())
                        <div class=" border border-dashed border-gray-200 p-6 text-center text-sm text-gray-400">
                            Nenhuma campanha sincronizada.
                            <a href="{{ route('clients.campaigns', $client) }}" class="text-primary-foreground font-semibold hover:underline ml-1">Sincronizar com Meta →</a>
                        </div>
                    @else
                        <div class="space-y-2 max-h-72 overflow-y-auto pr-1">
                            @foreach ($campaigns as $campaign)
                                <label class="flex items-center gap-3 px-4 py-3  border cursor-pointer transition-all duration-150"
                                       :class="form.campaigns.includes({{ $campaign->id }})
                                           ? 'border-primary bg-primary/5'
                                           : 'border-gray-200 hover:bg-gray-50'">
                                    <div class="w-4 h-4  border-2 flex items-center justify-center flex-shrink-0 transition-colors"
                                         :class="form.campaigns.includes({{ $campaign->id }}) ? 'bg-primary border-primary' : 'border-gray-300'">
                                        <svg x-show="form.campaigns.includes({{ $campaign->id }})" class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="text-sm font-medium text-gray-900 truncate">{{ $campaign->name }}</div>
                                        <div class="text-xs text-gray-400">{{ $campaign->meta_status_label }}
                                            @if($campaign->start_date) · {{ $campaign->start_date->format('d/m/Y') }} @endif
                                        </div>
                                    </div>
                                    <input type="checkbox" class="sr-only" @change="toggleCampaign({{ $campaign->id }})">
                                </label>
                            @endforeach
                        </div>
                    @endif

                    <p x-show="errors.campaigns" x-text="errors.campaigns" class="text-red-500 text-xs"></p>
                </div>

                <div class="flex justify-between mt-4">
                    <button type="button" @click="step--" class="btn-outline flex items-center gap-1">
                        <span class="material-symbols-outlined text-[18px]">arrow_back</span>
                        Voltar
                    </button>
                    <button type="button" @click="step2Next" class="btn-primary flex items-center gap-1">
                        Próximo
                        <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
                    </button>
                </div>
            </div>

            {{-- Step 3: Metas --}}
            <div x-show="step === 3" x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 translate-x-4"
                 x-transition:enter-end="opacity-100 translate-x-0">
                <div class="card p-8 space-y-5">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">Definir metas</h2>
                        <p class="text-sm text-gray-500 mt-1">Escolha o que quer monitorar e defina o alvo. Os valores são atualizados automaticamente ao sincronizar com o Meta.</p>
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        @foreach (\App\Models\PlanningGoal::CATEGORIES as $key => $cat)
                            <label class="flex items-center gap-3 px-4 py-3  border cursor-pointer transition-all duration-150"
                                   :class="hasGoal('{{ $key }}') ? 'border-primary bg-primary/5' : 'border-gray-200 hover:bg-gray-50'">
                                <div class="w-4 h-4  border-2 flex items-center justify-center flex-shrink-0 transition-colors"
                                     :class="hasGoal('{{ $key }}') ? 'bg-primary border-primary' : 'border-gray-300'">
                                    <svg x-show="hasGoal('{{ $key }}')" class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-center gap-1.5">
                                        <span class="material-symbols-outlined text-[15px] text-gray-500">{{ $cat['icon'] }}</span>
                                        <span class="text-sm font-medium text-gray-900">{{ $cat['label'] }}</span>
                                    </div>
                                    <span class="text-xs text-gray-400">{{ $cat['unit'] }}</span>
                                </div>
                                <input type="checkbox" class="sr-only" @change="toggleGoal('{{ $key }}')">
                            </label>
                        @endforeach
                    </div>

                    {{-- Target inputs for selected goals --}}
                    <template x-for="goal in form.goals" :key="goal.category">
                        <div class="flex items-center gap-3 px-4 py-3  bg-primary/5 border border-primary/20">
                            <span class="text-sm font-semibold text-gray-900 flex-1" x-text="categoryLabel(goal.category)"></span>
                            <div class="flex items-center gap-2">
                                <span class="text-xs text-gray-500">Alvo:</span>
                                <input type="number" x-model="goal.target_value" min="0" step="any"
                                       class="input w-32 py-1.5 text-sm" placeholder="0">
                                <span class="text-xs text-gray-400" x-text="categoryUnit(goal.category)"></span>
                            </div>
                        </div>
                    </template>

                    <template x-if="form.goals.length === 0">
                        <p class="text-xs text-gray-400 text-center py-2">Nenhuma meta selecionada — você pode adicionar depois.</p>
                    </template>

                    {{-- Hidden inputs for submission --}}
                    <input type="hidden" name="name" :value="form.name">
                    <input type="hidden" name="period_start" :value="form.period_start">
                    <input type="hidden" name="period_end" :value="form.period_end">
                    <input type="hidden" name="notes" :value="form.notes">
                    <template x-for="id in form.campaigns" :key="id">
                        <input type="hidden" name="campaigns[]" :value="id">
                    </template>
                    <template x-for="(goal, i) in form.goals" :key="goal.category">
                        <span>
                            <input type="hidden" :name="`goals[${i}][category]`" :value="goal.category">
                            <input type="hidden" :name="`goals[${i}][target_value]`" :value="goal.target_value">
                        </span>
                    </template>
                </div>

                <div class="flex justify-between mt-4">
                    <button type="button" @click="step--" class="btn-outline flex items-center gap-1">
                        <span class="material-symbols-outlined text-[18px]">arrow_back</span>
                        Voltar
                    </button>
                    <button type="submit" class="btn-primary flex items-center gap-1">
                        <span class="material-symbols-outlined text-[18px]">check</span>
                        Criar planejamento
                    </button>
                </div>
            </div>
        </form>
    </div>

    <script>
        const CATEGORIES = @json(\App\Models\PlanningGoal::CATEGORIES);

        function planningWizard() {
            return {
                step: 1,
                form: {
                    name: '',
                    period_start: '',
                    period_end: '',
                    notes: '',
                    campaigns: [],
                    goals: [],
                },
                errors: {},
                toggleCampaign(id) {
                    const idx = this.form.campaigns.indexOf(id);
                    idx === -1 ? this.form.campaigns.push(id) : this.form.campaigns.splice(idx, 1);
                },
                hasGoal(category) {
                    return this.form.goals.some(g => g.category === category);
                },
                toggleGoal(category) {
                    const idx = this.form.goals.findIndex(g => g.category === category);
                    if (idx === -1) {
                        this.form.goals.push({ category, target_value: '' });
                    } else {
                        this.form.goals.splice(idx, 1);
                    }
                },
                categoryLabel(cat) {
                    return CATEGORIES[cat]?.label ?? cat;
                },
                categoryUnit(cat) {
                    return CATEGORIES[cat]?.unit ?? '';
                },
                step1Next() {
                    this.errors = {};
                    if (!this.form.name.trim()) {
                        this.errors.name = 'O nome do planejamento é obrigatório.';
                        return;
                    }
                    this.step++;
                },
                step2Next() {
                    this.errors = {};
                    if (this.form.campaigns.length === 0) {
                        this.errors.campaigns = 'Selecione ao menos uma campanha.';
                        return;
                    }
                    this.step++;
                },
                submitForm() {
                    document.getElementById('planning-form').submit();
                },
            }
        }
    </script>
</x-app-layout>

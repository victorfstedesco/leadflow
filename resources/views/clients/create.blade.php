<x-app-layout>
    <x-slot name="title">Novo cliente</x-slot>

    <div class="max-w-xl mx-auto" x-data="clientWizard()">

        {{-- Progress bar --}}
        <div class="mb-8">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Novo cliente</span>
                <span class="text-xs text-gray-400" x-text="`Passo ${step} de 3`"></span>
            </div>
            <div class="h-1.5 bg-gray-100 rounded-none overflow-hidden">
                <div class="h-full bg-primary transition-all duration-300 ease-out rounded-none "
                     :style="`width: ${(step / 3) * 100}%`"></div>
            </div>
            <div class="flex justify-between mt-2">
                <template x-for="(label, i) in ['Informações', 'Canais', 'Revisão']" :key="i">
                    <span class="text-xs font-medium transition-colors"
                          :class="step > i ? 'text-primary-foreground' : 'text-gray-400'"
                          x-text="label"></span>
                </template>
            </div>
        </div>

        <form method="POST" action="{{ route('clients.store') }}" id="client-form" @submit.prevent="submitForm">
            @csrf

            {{-- Step 1: Nome + nicho --}}
            <div x-show="step === 1" x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 translate-x-4"
                 x-transition:enter-end="opacity-100 translate-x-0">
                <div class="card p-8 space-y-6">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">Quem é o cliente?</h2>
                        <p class="text-sm text-gray-500 mt-1">Comece com as informações básicas.</p>
                    </div>

                    <div>
                        <label class="label">Nome do cliente <span class="text-red-400">*</span></label>
                        <input type="text" x-model="form.name" maxlength="120"
                               class="input" placeholder="Ex: Clínica Vida & Saúde"
                               @keydown.enter.prevent="step1Next">
                        <p x-show="errors.name" x-text="errors.name" class="text-red-500 text-xs mt-1"></p>
                    </div>

                    <div>
                        <label class="label">Nicho de atuação</label>
                        <select x-model="form.niche" class="input">
                            <option value="">Selecione um nicho</option>
                            @foreach (['Saúde', 'Gastronomia', 'Moda', 'Tecnologia', 'Educação', 'Fitness', 'Beleza', 'Imobiliário'] as $niche)
                                <option value="{{ $niche }}">{{ $niche }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="flex justify-end mt-4">
                    <button type="button" @click="step1Next" class="btn-primary flex items-center gap-1">
                        Próximo
                        <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
                    </button>
                </div>
            </div>

            {{-- Step 2: Canais + notas --}}
            <div x-show="step === 2" x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 translate-x-4"
                 x-transition:enter-end="opacity-100 translate-x-0">
                <div class="card p-8 space-y-6">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">Onde o cliente está presente?</h2>
                        <p class="text-sm text-gray-500 mt-1">Selecione as redes sociais ativas. Você pode alterar depois.</p>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        @foreach (['Instagram', 'TikTok', 'Facebook', 'LinkedIn', 'YouTube', 'X (Twitter)'] as $channel)
                            <label class="flex items-center gap-3 px-4 py-3 rounded-none border cursor-pointer transition-all duration-150"
                                   :class="form.channels.includes('{{ $channel }}')
                                       ? 'border-primary bg-primary/5 text-primary-foreground font-semibold'
                                       : 'border-gray-200 hover:bg-gray-50 text-gray-700'">
                                <div class="w-4 h-4 rounded-none border-2 flex items-center justify-center flex-shrink-0 transition-colors"
                                     :class="form.channels.includes('{{ $channel }}') ? 'bg-primary border-primary' : 'border-gray-300'">
                                    <svg x-show="form.channels.includes('{{ $channel }}')" class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </div>
                                <span class="text-sm">{{ $channel }}</span>
                                <input type="checkbox" class="sr-only" @change="toggleChannel('{{ $channel }}')">
                            </label>
                        @endforeach
                    </div>

                    <div>
                        <label class="label">Observações <span class="text-gray-400 font-normal">(opcional)</span></label>
                        <textarea x-model="form.notes" rows="3" class="input"
                                  placeholder="Notas internas sobre o cliente..."></textarea>
                    </div>
                </div>

                <div class="flex justify-between mt-4">
                    <button type="button" @click="step--" class="btn-outline flex items-center gap-1">
                        <span class="material-symbols-outlined text-[18px]">arrow_back</span>
                        Voltar
                    </button>
                    <button type="button" @click="step++" class="btn-primary flex items-center gap-1">
                        Próximo
                        <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
                    </button>
                </div>
            </div>

            {{-- Step 3: Revisão --}}
            <div x-show="step === 3" x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 translate-x-4"
                 x-transition:enter-end="opacity-100 translate-x-0">
                <div class="card p-8 space-y-5">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">Tudo certo?</h2>
                        <p class="text-sm text-gray-500 mt-1">Revise os dados antes de criar o cliente.</p>
                    </div>

                    <div class="divide-y divide-gray-100 rounded-none border border-gray-100 overflow-hidden">
                        <div class="flex items-center justify-between px-5 py-3.5">
                            <span class="text-sm text-gray-500">Nome</span>
                            <span class="text-sm font-semibold text-gray-900" x-text="form.name || '—'"></span>
                        </div>
                        <div class="flex items-center justify-between px-5 py-3.5">
                            <span class="text-sm text-gray-500">Nicho</span>
                            <span class="text-sm font-semibold text-gray-900" x-text="form.niche || '—'"></span>
                        </div>
                        <div class="flex items-start justify-between px-5 py-3.5">
                            <span class="text-sm text-gray-500 mt-0.5">Canais</span>
                            <div class="flex flex-wrap gap-1.5 justify-end max-w-xs">
                                <template x-if="form.channels.length === 0">
                                    <span class="text-sm text-gray-400">Nenhum selecionado</span>
                                </template>
                                <template x-for="ch in form.channels" :key="ch">
                                    <span class="badge bg-primary/10 text-primary-foreground text-xs" x-text="ch"></span>
                                </template>
                            </div>
                        </div>
                        <template x-if="form.notes">
                            <div class="flex items-start justify-between px-5 py-3.5">
                                <span class="text-sm text-gray-500">Observações</span>
                                <span class="text-sm text-gray-700 max-w-xs text-right" x-text="form.notes"></span>
                            </div>
                        </template>
                    </div>

                    <p class="text-xs text-gray-400 flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[14px]">info</span>
                        Após criar, você poderá conectar a conta Meta Ads para sincronizar campanhas.
                    </p>

                    {{-- Hidden inputs para submissão --}}
                    <input type="hidden" name="name" :value="form.name">
                    <input type="hidden" name="niche" :value="form.niche">
                    <input type="hidden" name="notes" :value="form.notes">
                    <template x-for="ch in form.channels" :key="ch">
                        <input type="hidden" name="channels[]" :value="ch">
                    </template>
                </div>

                <div class="flex justify-between mt-4">
                    <button type="button" @click="step--" class="btn-outline flex items-center gap-1">
                        <span class="material-symbols-outlined text-[18px]">arrow_back</span>
                        Voltar
                    </button>
                    <button type="submit" class="btn-primary flex items-center gap-1">
                        <span class="material-symbols-outlined text-[18px]">check</span>
                        Criar cliente
                    </button>
                </div>
            </div>
        </form>
    </div>

    <script>
        function clientWizard() {
            return {
                step: 1,
                form: {
                    name: '',
                    niche: '',
                    channels: [],
                    notes: '',
                },
                errors: {},
                toggleChannel(channel) {
                    const idx = this.form.channels.indexOf(channel);
                    if (idx === -1) {
                        this.form.channels.push(channel);
                    } else {
                        this.form.channels.splice(idx, 1);
                    }
                },
                step1Next() {
                    this.errors = {};
                    if (!this.form.name.trim()) {
                        this.errors.name = 'O nome do cliente é obrigatório.';
                        return;
                    }
                    this.step++;
                },
                submitForm() {
                    document.getElementById('client-form').submit();
                },
            }
        }
    </script>
</x-app-layout>

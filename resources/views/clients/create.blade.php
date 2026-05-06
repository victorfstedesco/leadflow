<x-app-layout>
    <x-slot name="title">Novo cliente</x-slot>

    <div class="max-w-4xl mx-auto">
        <div class="mb-10">
            <a href="{{ route('clients.index') }}" class="inline-flex items-center gap-2 text-gray-500 font-medium text-sm mb-4 hover:text-gray-900 transition-colors">
                <span class="material-symbols-outlined text-[18px]">arrow_back</span>
                Voltar para Clientes
            </a>
            <h1 class="section-title">Novo Cliente</h1>
            <p class="text-gray-500 mt-2">Cadastre as informações básicas para iniciar o gerenciamento.</p>
        </div>

        <form method="POST" action="{{ route('clients.store') }}" class="grid md:grid-cols-3 gap-8" x-data="{ channels: [] }">
            @csrf

            <div class="md:col-span-2 space-y-6">
                {{-- Info Básica --}}
                <div class="card p-8 border-gray-100 shadow-sm relative overflow-hidden">
                    <div class="absolute -right-10 -top-10 w-32 h-32 bg-primary/5 rounded-full blur-2xl pointer-events-none"></div>
                    <h2 class="font-bold text-gray-900 text-lg mb-6 relative z-10">Informações Principais</h2>
                    
                    <div class="space-y-5 relative z-10">
                        <div>
                            <label class="label">Nome do cliente <span class="text-red-500">*</span></label>
                            <input type="text" name="name" required maxlength="120"
                                   class="input text-lg font-semibold" placeholder="Ex: Clínica Vida & Saúde">
                            @error('name')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="label">Nicho de atuação</label>
                            <select name="niche" class="input">
                                <option value="">Selecione um nicho</option>
                                @foreach (['Saúde', 'Gastronomia', 'Moda', 'Tecnologia', 'Educação', 'Fitness', 'Beleza', 'Imobiliário'] as $niche)
                                    <option value="{{ $niche }}">{{ $niche }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Canais --}}
                <div class="card p-8 border-gray-100 shadow-sm relative overflow-hidden">
                    <div class="absolute -left-10 -top-10 w-32 h-32 bg-blue-50 rounded-full blur-2xl pointer-events-none"></div>
                    <h2 class="font-bold text-gray-900 text-lg mb-2 relative z-10">Redes Sociais</h2>
                    <p class="text-sm text-gray-500 mb-6 relative z-10">Onde este cliente tem presença online?</p>
                    
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3 relative z-10">
                        @foreach (['Instagram', 'TikTok', 'Facebook', 'LinkedIn', 'YouTube', 'X (Twitter)'] as $channel)
                            <label class="flex items-center gap-3 p-3 rounded border cursor-pointer transition-all duration-200"
                                   :class="channels.includes('{{ $channel }}') ? 'bg-primary/5 border-primary/30 shadow-sm' : 'bg-white border-gray-200 hover:bg-gray-50 hover:border-gray-300'">
                                <div class="w-4 h-4 rounded border flex items-center justify-center transition-colors"
                                     :class="channels.includes('{{ $channel }}') ? 'bg-primary border-primary' : 'bg-white border-gray-300'">
                                    <svg x-show="channels.includes('{{ $channel }}')" class="w-3 h-3 text-white" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </div>
                                <span class="font-medium text-gray-700 text-sm">{{ $channel }}</span>
                                <input type="checkbox" name="channels[]" value="{{ $channel }}" class="sr-only" 
                                       @change="if($el.checked) { channels.push('{{ $channel }}') } else { channels = channels.filter(c => c !== '{{ $channel }}') }">
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                {{-- Observações --}}
                <div class="card p-8 border-gray-100 shadow-sm relative overflow-hidden">
                    <div class="absolute -right-10 -bottom-10 w-32 h-32 bg-amber-50 rounded-full blur-2xl pointer-events-none"></div>
                    <h2 class="font-bold text-gray-900 text-lg mb-4 relative z-10">Anotações Internas</h2>
                    
                    <div class="relative z-10">
                        <label class="label">Observações (Opcional)</label>
                        <textarea name="notes" rows="6" class="input text-sm"
                                  placeholder="Tom de voz da marca, público alvo, restrições ou regras de comunicação..."></textarea>
                    </div>
                </div>

                {{-- Submit --}}
                <div class="card p-6 border-gray-100 shadow-sm bg-gray-50/50 flex flex-col items-center text-center">
                    <div class="w-12 h-12 rounded-full bg-primary/20 text-primary-foreground flex items-center justify-center mb-4">
                        <span class="material-symbols-outlined">rocket_launch</span>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-1">Tudo pronto?</h3>
                    <p class="text-xs text-gray-500 mb-6">Você poderá conectar o Meta Ads na próxima tela.</p>
                    <button type="submit" class="btn-primary w-full">
                        Criar Cliente
                    </button>
                </div>
            </div>
        </form>
    </div>
</x-app-layout>

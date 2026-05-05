<x-app-layout>
    <x-slot name="title">Configurações · {{ $client->name }}</x-slot>

    <x-slot name="clientBar">
        <x-client-subnav :client="$client" />
    </x-slot>

    <div class="max-w-4xl mx-auto">

        @if (session('meta_prompt') && ! $client->isMetaConnected())
            <div class="mb-8 flex items-start gap-4 rounded bg-blue-50/50 border border-blue-100 p-6 shadow-sm">
                <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                    <span class="material-symbols-outlined text-blue-600 text-[20px]">rocket_launch</span>
                </div>
                <div>
                    <h3 class="text-base font-bold text-blue-900">Cliente criado com sucesso!</h3>
                    <p class="text-sm text-blue-700 mt-1 mb-3">Conecte a conta Meta Ads para começar a sincronizar campanhas e acompanhar resultados em tempo real.</p>
                    <a href="{{ route('meta.redirect', $client) }}" class="inline-flex items-center gap-2 text-sm font-bold text-white bg-blue-600 px-4 py-2 rounded hover:bg-blue-700 transition-colors shadow-sm">
                        Conectar Meta Ads agora
                        <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                    </a>
                </div>
            </div>
        @endif

        <div class="mb-8">
            <h1 class="section-title">Configurações</h1>
            <p class="text-gray-500 mt-2">Atualize os dados, nicho e configurações de integração deste cliente.</p>
        </div>

        <div class="grid md:grid-cols-3 gap-8">
            <div class="md:col-span-2 space-y-6">
                {{-- Dados do Cliente --}}
                <form method="POST" action="{{ route('clients.update', $client) }}" class="card p-8 border-gray-100 shadow-sm relative overflow-hidden">
                    @csrf
                    @method('PUT')
                    
                    <div class="absolute -left-10 -top-10 w-32 h-32 bg-primary/5 rounded-full blur-2xl pointer-events-none"></div>
                    <h2 class="font-bold text-gray-900 text-lg mb-6 relative z-10">Dados do Cliente</h2>

                    <div class="space-y-6 relative z-10">
                        <div>
                            <label class="label">Nome do cliente</label>
                            <input type="text" name="name" required class="input font-semibold" value="{{ old('name', $client->name) }}" placeholder="Ex: Clínica Vida & Saúde">
                            @error('name') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="label">Nicho</label>
                            <select name="niche" class="input">
                                <option value="">Selecione um nicho</option>
                                @foreach (['Saúde', 'Gastronomia', 'Moda', 'Tecnologia', 'Educação', 'Fitness', 'Beleza', 'Imobiliário'] as $niche)
                                    <option value="{{ $niche }}" @selected(old('niche', $client->niche) === $niche)>{{ $niche }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="label">Redes Sociais Ativas</label>
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                                @foreach (['Instagram', 'TikTok', 'Facebook', 'LinkedIn', 'YouTube', 'X (Twitter)'] as $channel)
                                    @php $isChecked = is_array($client->channels) && in_array($channel, $client->channels); @endphp
                                    <label class="flex items-center gap-3 p-3 rounded border cursor-pointer transition-all duration-200 {{ $isChecked ? 'bg-primary/5 border-primary/30 shadow-sm' : 'bg-white border-gray-200 hover:bg-gray-50' }}">
                                        <div class="w-4 h-4 rounded border flex items-center justify-center transition-colors {{ $isChecked ? 'bg-primary border-primary' : 'bg-white border-gray-300' }}">
                                            <svg class="w-3 h-3 text-white {{ $isChecked ? 'block' : 'hidden' }}" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                            </svg>
                                        </div>
                                        <span class="font-medium text-gray-700 text-sm">{{ $channel }}</span>
                                        <input type="checkbox" name="channels[]" value="{{ $channel }}" class="sr-only" 
                                               {{ $isChecked ? 'checked' : '' }}
                                               onchange="this.parentElement.classList.toggle('bg-primary/5'); this.parentElement.classList.toggle('border-primary/30'); this.parentElement.classList.toggle('shadow-sm'); this.previousElementSibling.previousElementSibling.classList.toggle('bg-primary'); this.previousElementSibling.previousElementSibling.classList.toggle('border-primary'); this.previousElementSibling.previousElementSibling.querySelector('svg').classList.toggle('hidden');">
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div>
                            <label class="label flex justify-between items-center">
                                <span>Observações Internas</span>
                                <span class="text-gray-400 font-normal normal-case tracking-normal text-xs">Opcional</span>
                            </label>
                            <textarea name="notes" rows="4" class="input" placeholder="Notas internas sobre o cliente...">{{ old('notes', $client->notes) }}</textarea>
                        </div>

                        <div class="flex items-center justify-between pt-6 border-t border-gray-100">
                            <button type="button" onclick="document.getElementById('delete-modal').classList.remove('hidden')" class="text-sm font-semibold text-red-500 hover:text-red-700 transition-colors flex items-center gap-1">
                                <span class="material-symbols-outlined text-[18px]">delete</span>
                                Excluir cliente
                            </button>
                            <button type="submit" class="btn-primary">
                                <span class="material-symbols-outlined text-[18px]">save</span>
                                Salvar alterações
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="space-y-6">
                {{-- Integração Meta --}}
                <div class="card p-8 border-gray-100 shadow-sm relative overflow-hidden">
                    <div class="absolute -right-10 -bottom-10 w-32 h-32 bg-blue-50 rounded-full blur-2xl pointer-events-none"></div>
                    <div class="flex items-center justify-between mb-6 relative z-10">
                        <h2 class="font-bold text-gray-900 text-lg">Integração Meta</h2>
                        @if ($client->isMetaConnected())
                            <span class="badge bg-green-100 text-green-700 border-green-200">Conectado</span>
                        @endif
                    </div>
                    
                    <div class="relative z-10">
                        @if (! $client->meta_access_token)
                            <div class="text-center py-4">
                                <div class="w-16 h-16 rounded-full bg-blue-50 flex items-center justify-center mx-auto mb-4 text-blue-500">
                                    <span class="material-symbols-outlined text-[32px]">campaign</span>
                                </div>
                                <p class="text-sm text-gray-500 mb-6">Conecte a conta do Facebook Ads para sincronizar campanhas e métricas automaticamente.</p>
                                <a href="{{ route('meta.redirect', $client) }}" class="btn-primary w-full">
                                    <span class="material-symbols-outlined text-[18px]">link</span>
                                    Conectar Conta
                                </a>
                            </div>
                        @else
                            <div class="space-y-4 mb-6">
                                <div class="p-4 rounded bg-gray-50 border border-gray-100">
                                    <div class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">ID do Usuário Meta</div>
                                    <div class="font-mono text-sm text-gray-700">{{ $client->meta_user_id ?? '—' }}</div>
                                </div>
                                
                                <div class="p-4 rounded bg-gray-50 border border-gray-100">
                                    <div class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">Conta de Anúncios</div>
                                    <div class="font-mono text-sm text-gray-700">{{ $client->meta_ad_account_id ?? 'Não selecionada' }}</div>
                                </div>
                            </div>

                            @if (! $client->meta_ad_account_id || session('meta_select_ad_account'))
                                <div x-data="adAccountPicker()" x-init="load()" class="mb-6 p-5 rounded border border-primary/20 bg-primary/5">
                                    <p class="text-sm font-semibold text-gray-900 mb-3">Vincular Conta de Anúncios</p>
                                    <form method="POST" action="{{ route('meta.ad-account', $client) }}" class="space-y-3">
                                        @csrf
                                        <select name="meta_ad_account_id" required class="input text-sm">
                                            <option value="">Carregando contas...</option>
                                            <template x-for="acc in accounts" :key="acc.id">
                                                <option :value="acc.id" x-text="acc.name + ' (' + acc.id + ')'"></option>
                                            </template>
                                        </select>
                                        <button type="submit" class="btn-primary w-full py-2.5">Confirmar Vínculo</button>
                                    </form>
                                </div>
                            @endif

                            <form method="POST" action="{{ route('meta.disconnect', $client) }}" class="border-t border-gray-100 pt-5 mt-2">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-full inline-flex justify-center items-center gap-2 px-4 py-2 text-sm font-semibold text-red-600 bg-red-50 hover:bg-red-100 rounded transition-colors" onclick="return confirm('Tem certeza? Você não receberá mais atualizações de campanhas deste cliente.')">
                                    <span class="material-symbols-outlined text-[18px]">link_off</span>
                                    Desconectar Meta
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function adAccountPicker() {
            return {
                accounts: [],
                async load() {
                    try {
                        const res = await fetch(@json(route('meta.list-ad-accounts', $client)));
                        const json = await res.json();
                        this.accounts = json.data || [];
                    } catch (e) {
                        this.accounts = [];
                    }
                }
            }
        }
    </script>

    {{-- Modal de exclusão --}}
    <div id="delete-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-gray-900/40 backdrop-blur-sm transition-opacity" onclick="document.getElementById('delete-modal').classList.add('hidden')"></div>
        <div class="relative bg-white rounded-lg shadow-xl w-full max-w-md p-8 transform transition-all">
            <div class="w-12 h-12 rounded-full bg-red-100 text-red-600 flex items-center justify-center mb-5">
                <span class="material-symbols-outlined text-[24px]">warning</span>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">Excluir cliente permanentemente?</h3>
            <p class="text-gray-500 mb-8">Todos os dados, postagens e conexões com o Meta deste cliente serão removidos. Essa ação não pode ser desfeita.</p>
            
            <div class="flex flex-col-reverse sm:flex-row gap-3 sm:justify-end">
                <button type="button" onclick="document.getElementById('delete-modal').classList.add('hidden')" class="btn-secondary w-full sm:w-auto">
                    Cancelar
                </button>
                <form method="POST" action="{{ route('clients.destroy', $client) }}" class="w-full sm:w-auto">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex justify-center items-center gap-2 bg-red-600 text-white px-6 py-3 rounded shadow-sm font-semibold hover:bg-red-700 transition-colors w-full">
                        Excluir Cliente
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

<x-app-layout>
    <x-slot name="title">Configurações · {{ $client->name }}</x-slot>

    <x-slot name="clientBar">
        <x-client-subnav :client="$client" />
    </x-slot>

    <div class="max-w-2xl">

        @if (session('meta_prompt') && ! $client->isMetaConnected())
            <div class="mb-6 flex items-start gap-3 rounded-none border border-blue-200 bg-blue-50 px-5 py-4">
                <span class="material-symbols-outlined text-blue-500 mt-0.5 text-[20px]">info</span>
                <div>
                    <p class="text-sm font-semibold text-blue-800">Cliente criado com sucesso!</p>
                    <p class="text-sm text-blue-700 mt-0.5">Conecte a conta Meta para começar a sincronizar campanhas e acompanhar resultados.</p>
                    <a href="{{ route('meta.redirect', $client) }}" class="mt-2 inline-flex items-center gap-1 text-sm font-semibold text-blue-700 hover:text-blue-900 underline underline-offset-2">
                        Conectar conta Meta agora →
                    </a>
                </div>
            </div>
        @endif

        <div class="mb-6">
            <h2 class="text-lg font-semibold text-gray-900">Configurações do cliente</h2>
            <p class="text-sm text-gray-500 mt-0.5">Atualize os dados, nicho e redes sociais deste cliente.</p>
        </div>

        <form method="POST" action="{{ route('clients.update', $client) }}" class="card p-8 space-y-6">
            @csrf
            @method('PUT')

            <div>
                <label class="label">Nome do cliente</label>
                <input type="text" name="name" required class="input" value="{{ old('name', $client->name) }}" placeholder="Ex: Clínica Vida & Saúde">
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
                <label class="label">Redes Sociais</label>
                <div class="grid grid-cols-2 gap-2">
                    @foreach (['Instagram', 'TikTok', 'Facebook', 'LinkedIn', 'YouTube', 'X (Twitter)'] as $channel)
                        <label class="flex items-center gap-2 px-4 py-2.5 rounded-none border border-gray-200 cursor-pointer hover:bg-gray-50 transition-colors">
                            <input type="checkbox" name="channels[]" value="{{ $channel }}" class="rounded-none text-primary focus:ring-primary"
                                   @checked(is_array($client->channels) && in_array($channel, $client->channels))>
                            <span class="text-sm text-gray-700">{{ $channel }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div>
                <label class="label">Observações</label>
                <textarea name="notes" rows="4" class="input" placeholder="Notas internas sobre o cliente...">{{ old('notes', $client->notes) }}</textarea>
            </div>

            <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                <button type="button" onclick="document.getElementById('delete-modal').classList.remove('hidden')" class="text-sm text-red-500 hover:text-red-700 font-medium transition-colors">Excluir cliente</button>
                <button type="submit" class="btn-primary">Salvar alterações</button>
            </div>
        </form>

        {{-- Bloco: Conta Meta --}}
        <div class="card p-8 mt-6 space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="font-semibold text-gray-900">Conta Meta (Facebook Ads)</h3>
                    <p class="text-sm text-gray-500 mt-0.5">Conecte para sincronizar campanhas e insights reais.</p>
                </div>
                @if ($client->isMetaConnected())
                    <span class="badge bg-green-100 text-green-700">Conectado</span>
                @else
                    <span class="badge bg-gray-100 text-gray-600">Desconectado</span>
                @endif
            </div>

            @if (! $client->meta_access_token)
                <a href="{{ route('meta.redirect', $client) }}" class="btn-primary inline-flex">
                    <span class="material-symbols-outlined text-[18px]">link</span>
                    Conectar conta Meta
                </a>
            @else
                <div class="text-sm text-gray-600 space-y-1">
                    <div><strong>Meta User ID:</strong> {{ $client->meta_user_id ?? '—' }}</div>
                    <div><strong>Conta de anúncios:</strong> {{ $client->meta_ad_account_id ?? 'Não selecionada' }}</div>
                    @if ($client->meta_token_expires_at)
                        <div><strong>Token expira:</strong> {{ $client->meta_token_expires_at->format('d/m/Y H:i') }}</div>
                    @endif
                </div>

                @if (! $client->meta_ad_account_id || session('meta_select_ad_account'))
                    <div x-data="adAccountPicker()" x-init="load()" class="border-t border-gray-100 pt-4">
                        <p class="text-sm font-semibold text-gray-700 mb-2">Selecione a conta de anúncios</p>
                        <form method="POST" action="{{ route('meta.ad-account', $client) }}" class="flex items-center gap-2">
                            @csrf
                            <select name="meta_ad_account_id" required class="input flex-1">
                                <option value="">Carregando...</option>
                                <template x-for="acc in accounts" :key="acc.id">
                                    <option :value="acc.id" x-text="acc.name + ' (' + acc.id + ')'"></option>
                                </template>
                            </select>
                            <button type="submit" class="btn-primary">Salvar</button>
                        </form>
                    </div>
                @endif

                <form method="POST" action="{{ route('meta.disconnect', $client) }}" class="border-t border-gray-100 pt-4">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-sm text-red-500 hover:text-red-700 font-medium" onclick="return confirm('Desconectar conta Meta? As campanhas já sincronizadas serão mantidas.')">
                        Desconectar Meta
                    </button>
                </form>
            @endif
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

    {{-- Modal de confirmação de exclusão --}}
    <div id="delete-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/40" onclick="document.getElementById('delete-modal').classList.add('hidden')"></div>
        <div class="relative bg-white rounded-none shadow-xl w-full max-w-sm p-6 space-y-4">
            <h3 class="text-base font-semibold text-gray-900">Excluir cliente</h3>
            <p class="text-sm text-gray-500">Tem certeza? Todos os dados deste cliente serão removidos permanentemente. Essa ação não pode ser desfeita.</p>
            <div class="flex gap-3 justify-end pt-2">
                <button type="button" onclick="document.getElementById('delete-modal').classList.add('hidden')" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-none transition-colors">Cancelar</button>
                <form method="POST" action="{{ route('clients.destroy', $client) }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-red-500 hover:bg-red-600 rounded-none transition-colors">Excluir</button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

<x-app-layout>
    <x-slot name="title">Configurações · {{ $client->name }}</x-slot>

    <x-slot name="clientBar">
        <x-client-subnav :client="$client" />
    </x-slot>

    <div class="max-w-2xl">
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
                        <label class="flex items-center gap-2 px-4 py-2.5 rounded-xl border border-gray-200 cursor-pointer hover:bg-gray-50 transition-colors">
                            <input type="checkbox" name="channels[]" value="{{ $channel }}" class="rounded text-primary focus:ring-primary"
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
    </div>

    {{-- Modal de confirmação de exclusão --}}
    <div id="delete-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/40" onclick="document.getElementById('delete-modal').classList.add('hidden')"></div>
        <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-sm p-6 space-y-4">
            <h3 class="text-base font-semibold text-gray-900">Excluir cliente</h3>
            <p class="text-sm text-gray-500">Tem certeza? Todos os dados deste cliente serão removidos permanentemente. Essa ação não pode ser desfeita.</p>
            <div class="flex gap-3 justify-end pt-2">
                <button type="button" onclick="document.getElementById('delete-modal').classList.add('hidden')" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-xl transition-colors">Cancelar</button>
                <form method="POST" action="{{ route('clients.destroy', $client) }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-red-500 hover:bg-red-600 rounded-xl transition-colors">Excluir</button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

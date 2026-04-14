<x-app-layout>
    <x-slot name="title">Novo cliente</x-slot>

    <div class="max-w-2xl mx-auto">
        <h1 class="section-title mb-2">Novo cliente</h1>
        <p class="text-gray-500 mb-8">Cadastre um cliente da sua agência e defina seu nicho e redes sociais.</p>

        <form method="POST" action="{{ route('clients.store') }}" class="card p-8 space-y-6">
            @csrf
            <div>
                <label class="label">Nome do cliente</label>
                <input type="text" name="name" required class="input" value="{{ old('name') }}" placeholder="Ex: Clínica Vida & Saúde">
                @error('name') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="label">Nicho</label>
                <select name="niche" class="input">
                    <option value="">Selecione um nicho</option>
                    @foreach (['Saúde', 'Gastronomia', 'Moda', 'Tecnologia', 'Educação', 'Fitness', 'Beleza', 'Imobiliário'] as $niche)
                        <option value="{{ $niche }}" @selected(old('niche') === $niche)>{{ $niche }}</option>
                    @endforeach
                </select>
                @error('niche') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="label">Redes Sociais</label>
                <div class="grid grid-cols-2 gap-2">
                    @foreach (['Instagram', 'TikTok', 'Facebook', 'LinkedIn', 'YouTube', 'X (Twitter)'] as $channel)
                        <label class="flex items-center gap-2 px-4 py-2.5 rounded-xl border border-gray-200 cursor-pointer hover:bg-gray-50">
                            <input type="checkbox" name="channels[]" value="{{ $channel }}" class="rounded text-primary focus:ring-primary"
                                   @checked(is_array(old('channels')) && in_array($channel, old('channels')))>
                            <span class="text-sm text-gray-700">{{ $channel }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div>
                <label class="label">Observações</label>
                <textarea name="notes" rows="3" class="input" placeholder="Notas internas sobre o cliente...">{{ old('notes') }}</textarea>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                <a href="{{ route('clients.index') }}" class="btn-secondary">Cancelar</a>
                <button type="submit" class="btn-primary">Salvar cliente</button>
            </div>
        </form>
    </div>
</x-app-layout>

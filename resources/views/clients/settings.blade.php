<x-app-layout>
    <x-slot name="title">Configurações · {{ $client->name }}</x-slot>

    {{-- Header do cliente --}}
    <div class="flex flex-wrap items-center justify-between gap-4 mb-2">
        <div class="flex items-center gap-4">
            <a href="{{ route('clients.index') }}" class="text-gray-400 hover:text-gray-900 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <div class="flex items-center gap-3">
                <div class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-primary/30 text-primary-foreground font-bold text-sm">
                    {{ strtoupper(substr($client->name, 0, 2)) }}
                </div>
                <h1 class="text-xl font-bold tracking-tight text-gray-900">{{ $client->name }}</h1>
            </div>
        </div>
    </div>

    {{-- Sub-navegação --}}
    <x-client-subnav :client="$client" />

    <div class="max-w-2xl">
        <div class="mb-6">
            <h2 class="text-lg font-semibold text-gray-900">Configurações do cliente</h2>
            <p class="text-sm text-gray-500 mt-0.5">Atualize os dados e canais deste cliente.</p>
        </div>

        <form method="POST" action="{{ route('clients.update', $client) }}" class="card p-8 space-y-6">
            @csrf
            @method('PUT')

            <div>
                <label class="label">Nome do cliente</label>
                <input type="text" name="name" required class="input" value="{{ old('name', $client->name) }}" placeholder="Ex: Clínica Dr. João">
                @error('name') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="label">Canais ativos</label>
                <div class="grid grid-cols-2 gap-2">
                    @foreach (['Meta Ads', 'Google Ads', 'TikTok Ads', 'Indicação', 'Orgânico', 'WhatsApp'] as $channel)
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
                <form method="POST" action="{{ route('clients.destroy', $client) }}" onsubmit="return confirm('Tem certeza? Todos os dados deste cliente serão removidos.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-sm text-red-500 hover:text-red-700 font-medium transition-colors">Excluir cliente</button>
                </form>
                <button type="submit" class="btn-primary">Salvar alterações</button>
            </div>
        </form>
    </div>
</x-app-layout>

<x-app-layout>
    <x-slot name="title">Novo lead</x-slot>

    <div class="max-w-2xl mx-auto">
        <a href="{{ route('clients.show', $client) }}" class="text-sm text-gray-500 hover:text-gray-900">← {{ $client->name }}</a>
        <h1 class="section-title mt-2 mb-2">Novo lead</h1>
        <p class="text-gray-500 mb-8">Adicione um novo lead ao funil de <strong>{{ $client->name }}</strong>.</p>

        <form method="POST" action="{{ route('leads.store', $client) }}" class="card p-8 space-y-6">
            @csrf
            <div class="grid md:grid-cols-2 gap-5">
                <div class="md:col-span-2">
                    <label class="label">Nome</label>
                    <input type="text" name="name" required class="input" value="{{ old('name') }}" placeholder="Nome do lead">
                    @error('name') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="label">E-mail</label>
                    <input type="email" name="email" class="input" value="{{ old('email') }}">
                </div>
                <div>
                    <label class="label">Telefone</label>
                    <input type="text" name="phone" class="input" value="{{ old('phone') }}">
                </div>
                <div>
                    <label class="label">Canal de origem</label>
                    <select name="source" class="input">
                        <option value="">—</option>
                        @foreach (['Meta Ads', 'Google Ads', 'TikTok Ads', 'Indicação', 'Orgânico', 'WhatsApp'] as $c)
                            <option value="{{ $c }}" @selected(old('source') === $c)>{{ $c }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="label">Etapa inicial</label>
                    <select name="funnel_stage_id" class="input">
                        @foreach ($client->stages as $stage)
                            <option value="{{ $stage->id }}">{{ $stage->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="label">Observações</label>
                    <textarea name="notes" rows="3" class="input">{{ old('notes') }}</textarea>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                <a href="{{ route('clients.show', $client) }}" class="btn-secondary">Cancelar</a>
                <button class="btn-primary">Adicionar lead</button>
            </div>
        </form>
    </div>
</x-app-layout>

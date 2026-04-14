<x-app-layout>
    <x-slot name="title">Nova postagem · {{ $client->name }}</x-slot>

    <div class="max-w-2xl mx-auto">
        <a href="{{ route('clients.posts', $client) }}" class="text-sm text-gray-500 hover:text-gray-900">← {{ $client->name }}</a>
        <h1 class="section-title mt-2 mb-2">Nova postagem</h1>
        <p class="text-gray-500 mb-8">Crie um novo conteúdo para <strong>{{ $client->name }}</strong>.</p>

        <form method="POST" action="{{ route('posts.store', $client) }}" class="card p-8 space-y-6">
            @csrf

            <div>
                <label class="label">Título</label>
                <input type="text" name="title" required class="input" value="{{ old('title', request('suggested_title')) }}" placeholder="Ex: 5 dicas para cuidar da pele no verão">
                @error('title') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="label">Copy da postagem</label>
                <textarea name="copy" rows="4" class="input" placeholder="Texto que acompanhará a publicação nas redes sociais...">{{ old('copy') }}</textarea>
                @error('copy') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid md:grid-cols-2 gap-5">
                <div>
                    <label class="label">Tipo de conteúdo</label>
                    <select name="content_type" required class="input">
                        <option value="">Selecione</option>
                        @foreach (['imagem' => 'Imagem', 'video' => 'Vídeo', 'carrossel' => 'Carrossel', 'story' => 'Story', 'reels' => 'Reels'] as $value => $label)
                            <option value="{{ $value }}" @selected(old('content_type', request('suggested_type')) === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('content_type') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="label">Objetivo</label>
                    <select name="objective" class="input">
                        <option value="">Selecione</option>
                        @foreach (['engajamento' => 'Engajamento', 'conversao' => 'Conversão', 'branding' => 'Branding', 'educacao' => 'Educação'] as $value => $label)
                            <option value="{{ $value }}" @selected(old('objective', request('suggested_objective')) === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('objective') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label class="label">Campanha (opcional)</label>
                <select name="campaign" class="input">
                    <option value="">Sem campanha</option>
                    @foreach ($campaigns as $campaign)
                        <option value="{{ $campaign }}" @selected(old('campaign') === $campaign)>{{ $campaign }}</option>
                    @endforeach
                </select>
                <p class="text-xs text-gray-400 mt-1.5">Vincule esta postagem a uma campanha ativa.</p>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                <a href="{{ route('clients.posts', $client) }}" class="btn-secondary">Cancelar</a>
                <button type="submit" class="btn-primary">
                    <span class="material-symbols-outlined text-[18px]">save</span>
                    Salvar postagem
                </button>
            </div>
        </form>
    </div>
</x-app-layout>

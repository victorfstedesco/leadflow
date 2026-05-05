<x-app-layout>
    <x-slot name="title">Editar postagem · {{ $client->name }}</x-slot>

    <div class="max-w-4xl mx-auto">
        <div class="mb-10">
            <a href="{{ route('clients.posts', $client) }}" class="inline-flex items-center gap-2 text-gray-500 font-medium text-sm mb-4 hover:text-gray-900 transition-colors">
                <span class="material-symbols-outlined text-[18px]">arrow_back</span>
                Voltar para Postagens
            </a>
            <h1 class="section-title">Editar postagem</h1>
            <p class="text-gray-500 mt-2">Atualize os detalhes do conteúdo criado para <strong>{{ $client->name }}</strong>.</p>
        </div>

        <form method="POST" action="{{ route('posts.update', [$client, $post]) }}" class="grid md:grid-cols-3 gap-8">
            @csrf
            @method('PUT')

            <div class="md:col-span-2 space-y-6">
                {{-- Conteúdo --}}
                <div class="card p-8 border-gray-100 shadow-sm relative overflow-hidden">
                    <div class="absolute -right-10 -top-10 w-32 h-32 bg-primary/5 rounded-full blur-2xl pointer-events-none"></div>
                    <h2 class="font-bold text-gray-900 text-lg mb-6 relative z-10">Conteúdo da Publicação</h2>
                    
                    <div class="space-y-6 relative z-10">
                        <div>
                            <label class="label">Título interno</label>
                            <input type="text" name="title" required class="input font-semibold" value="{{ old('title', $post->title) }}" placeholder="Ex: 5 dicas para cuidar da pele no verão">
                            @error('title') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="label flex justify-between items-center">
                                <span>Copy (Legenda)</span>
                                <span class="text-gray-400 font-normal normal-case tracking-normal text-xs">Opcional</span>
                            </label>
                            <textarea name="copy" rows="6" class="input" placeholder="Texto que acompanhará a publicação nas redes sociais...">{{ old('copy', $post->copy) }}</textarea>
                            @error('copy') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                {{-- Configurações --}}
                <div class="card p-8 border-gray-100 shadow-sm relative overflow-hidden">
                    <div class="absolute -right-10 -bottom-10 w-32 h-32 bg-blue-50 rounded-full blur-2xl pointer-events-none"></div>
                    <h2 class="font-bold text-gray-900 text-lg mb-6 relative z-10">Configurações</h2>
                    
                    <div class="space-y-5 relative z-10">
                        <div>
                            <label class="label">Formato</label>
                            <select name="content_type" required class="input">
                                <option value="">Selecione o formato</option>
                                @foreach (['imagem' => 'Imagem', 'video' => 'Vídeo', 'carrossel' => 'Carrossel', 'story' => 'Story', 'reels' => 'Reels'] as $value => $label)
                                    <option value="{{ $value }}" @selected(old('content_type', $post->content_type) === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('content_type') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="label">Objetivo</label>
                            <select name="objective" class="input">
                                <option value="">Qual o foco?</option>
                                @foreach (['engajamento' => 'Engajamento', 'conversao' => 'Conversão', 'branding' => 'Branding', 'educacao' => 'Educação'] as $value => $label)
                                    <option value="{{ $value }}" @selected(old('objective', $post->objective) === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('objective') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="pt-2">
                            <label class="label">Vincular a Campanha</label>
                            <select name="campaign_id" class="input">
                                <option value="">Sem campanha</option>
                                @foreach ($campaigns as $campaign)
                                    <option value="{{ $campaign->id }}" @selected((int) old('campaign_id', $post->campaign_id) === $campaign->id)>{{ $campaign->name }}</option>
                                @endforeach
                            </select>
                            @if ($campaigns->isEmpty())
                                <p class="text-[11px] text-gray-400 mt-2">
                                    Nenhuma campanha ativa. <a href="{{ route('clients.campaigns', $client) }}" class="text-primary-foreground hover:underline">Sincronize com Meta</a>.
                                </p>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Submit and Actions --}}
                <div class="card p-6 border-gray-100 shadow-sm bg-gray-50/50 flex flex-col items-center text-center">
                    <button type="submit" class="btn-primary w-full">
                        <span class="material-symbols-outlined text-[20px]">save</span>
                        Salvar Alterações
                    </button>
                    <a href="{{ route('clients.posts', $client) }}" class="text-sm font-semibold text-gray-500 hover:text-gray-900 mt-4 transition-colors">
                        Cancelar
                    </a>
                </div>

                {{-- Danger Zone --}}
                <div class="text-center pt-2">
                    <button type="button"
                            onclick="if(confirm('Tem certeza que deseja excluir esta postagem?')) document.getElementById('delete-post-form').submit()"
                            class="inline-flex items-center gap-1.5 text-xs font-semibold text-red-500 hover:text-red-700 transition-colors">
                        <span class="material-symbols-outlined text-[16px]">delete</span>
                        Excluir postagem
                    </button>
                </div>
            </div>
        </form>

        <form id="delete-post-form" method="POST" action="{{ route('posts.destroy', [$client, $post]) }}" class="hidden">
            @csrf
            @method('DELETE')
        </form>
    </div>
</x-app-layout>

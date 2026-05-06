<x-app-layout>
    <x-slot name="title">Postagens · {{ $client->name }}</x-slot>

    <x-slot name="clientBar">
        <x-client-subnav :client="$client" />
    </x-slot>

    <div class="max-w-6xl mx-auto">
        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="section-title">Postagens</h1>
                <p class="text-gray-500 mt-2">Gerencie todo o conteúdo criado para <strong>{{ $client->name }}</strong>.</p>
            </div>
            <a href="{{ route('posts.create', $client) }}" class="btn-primary flex-shrink-0">
                <span class="material-symbols-outlined text-[20px]">add</span>
                Nova postagem
            </a>
        </div>

        @if ($posts->isEmpty())
            <div class="card p-12 text-center flex flex-col items-center justify-center border-gray-100 shadow-sm relative overflow-hidden">
                <div class="absolute inset-0 bg-primary/5 blur-3xl rounded-full w-64 h-64 mx-auto top-1/2 -translate-y-1/2 pointer-events-none"></div>
                <div class="w-20 h-20 rounded-full bg-primary/10 flex items-center justify-center mb-6 text-primary-foreground relative z-10">
                    <span class="material-symbols-outlined text-[40px]">edit_note</span>
                </div>
                <h3 class="font-bold text-2xl text-gray-900 mb-2 relative z-10">Nenhuma postagem criada</h3>
                <p class="text-gray-500 text-base mb-8 max-w-md relative z-10">Você ainda não criou nenhum conteúdo para este cliente. Comece agora para organizar suas ideias e campanhas.</p>
                <a href="{{ route('posts.create', $client) }}" class="btn-primary relative z-10">
                    Começar primeira postagem
                </a>
            </div>
        @else
            <div class="card p-0 overflow-hidden border-gray-100 shadow-sm">
                <div class="hidden md:grid grid-cols-12 gap-4 px-6 py-4 bg-gray-50/50 border-b border-gray-100 text-xs font-bold text-gray-500 uppercase tracking-wider">
                    <div class="col-span-5">Postagem</div>
                    <div class="col-span-2 text-center">Formato</div>
                    <div class="col-span-2 text-center">Objetivo</div>
                    <div class="col-span-3">Campanha Vinculada</div>
                </div>
                
                <div class="divide-y divide-gray-50 bg-white">
                    @foreach ($posts as $post)
                        <a href="{{ route('posts.edit', [$client, $post]) }}" class="grid grid-cols-1 md:grid-cols-12 gap-4 px-6 py-5 hover:bg-primary/5 transition-colors group items-center">
                            
                            {{-- Titulo e Copy --}}
                            <div class="col-span-1 md:col-span-5 flex items-start gap-4">
                                <div class="w-10 h-10 rounded bg-gray-50 border border-gray-100 flex items-center justify-center flex-shrink-0 group-hover:bg-white group-hover:border-primary/20 transition-colors">
                                    <span class="material-symbols-outlined text-[20px] text-gray-400 group-hover:text-primary-foreground transition-colors">{{ $post->content_type_icon }}</span>
                                </div>
                                <div class="min-w-0">
                                    <h3 class="font-bold text-gray-900 text-base group-hover:text-primary-foreground transition-colors truncate">{{ $post->title }}</h3>
                                    @if ($post->copy)
                                        <p class="text-sm text-gray-500 mt-1 line-clamp-1 group-hover:text-gray-600 transition-colors">{{ $post->copy }}</p>
                                    @else
                                        <p class="text-sm text-gray-400 mt-1 italic">Sem legenda descrita.</p>
                                    @endif
                                </div>
                            </div>

                            {{-- Formato (Mobile & Desktop) --}}
                            <div class="col-span-1 md:col-span-2 md:text-center flex items-center md:justify-center gap-2">
                                <span class="md:hidden text-xs font-bold text-gray-400 uppercase">Formato:</span>
                                <span class="text-sm font-medium text-gray-700">{{ $post->content_type_label }}</span>
                            </div>

                            {{-- Objetivo (Mobile & Desktop) --}}
                            <div class="col-span-1 md:col-span-2 md:text-center flex items-center md:justify-center gap-2">
                                <span class="md:hidden text-xs font-bold text-gray-400 uppercase">Objetivo:</span>
                                @if ($post->objective)
                                    <span class="badge bg-surface text-gray-600 border border-gray-200 shadow-sm">{{ $post->objective_label }}</span>
                                @else
                                    <span class="text-sm text-gray-400">—</span>
                                @endif
                            </div>

                            {{-- Campanha --}}
                            <div class="col-span-1 md:col-span-3 flex items-center justify-between gap-4">
                                <div class="flex items-center gap-2 min-w-0">
                                    <span class="md:hidden text-xs font-bold text-gray-400 uppercase">Campanha:</span>
                                    @if ($post->campaign)
                                        <span class="material-symbols-outlined text-[16px] text-gray-400 flex-shrink-0 hidden md:block">campaign</span>
                                        <span class="text-sm text-gray-600 truncate">{{ $post->campaign->name }}</span>
                                    @else
                                        <span class="text-sm text-gray-400 italic">Não vinculada</span>
                                    @endif
                                </div>
                                <span class="material-symbols-outlined text-[20px] text-gray-300 group-hover:text-primary-foreground transition-transform group-hover:translate-x-1 flex-shrink-0">arrow_forward</span>
                            </div>

                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</x-app-layout>
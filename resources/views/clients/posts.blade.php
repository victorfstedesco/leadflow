<x-app-layout>
    <x-slot name="title">Postagens · {{ $client->name }}</x-slot>

    <x-slot name="clientBar">
        <x-client-subnav :client="$client" />
    </x-slot>

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-lg font-semibold text-gray-900">Postagens</h2>
            <p class="text-sm text-gray-500 mt-0.5">Conteúdos criados para este cliente.</p>
        </div>
        <a href="{{ route('posts.create', $client) }}" class="btn-primary">
            <span class="material-symbols-outlined text-[18px]">add</span>
            Nova postagem
        </a>
    </div>

    @if ($posts->isEmpty())
        <div class="card p-12 text-center">
            <div class="w-16 h-16 mx-auto rounded-none bg-primary/10 flex items-center justify-center mb-6">
                <span class="material-symbols-outlined text-[32px] text-primary-foreground">edit_note</span>
            </div>
            <h3 class="font-semibold text-gray-900 mb-1">Nenhuma postagem criada</h3>
            <p class="text-gray-500 text-sm mb-6">Comece criando a primeira postagem deste cliente.</p>
            <a href="{{ route('posts.create', $client) }}" class="btn-primary">+ Nova postagem</a>
        </div>
    @else
        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
            @foreach ($posts as $post)
                <a href="{{ route('posts.edit', [$client, $post]) }}" class="card p-5 hover:shadow-md transition group block">
                    {{-- Header: tipo + objetivo --}}
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-2">
                            <div
                                class="w-8 h-8 rounded-none bg-gray-50 border border-gray-100 flex items-center justify-center">
                                <span
                                    class="material-symbols-outlined text-[16px] text-gray-700">{{ $post->content_type_icon }}</span>
                            </div>
                            <span class="text-xs font-medium text-gray-500">{{ $post->content_type_label }}</span>
                        </div>
                        @if ($post->objective)
                            <span class="badge bg-primary/20 text-primary-foreground">{{ $post->objective_label }}</span>
                        @endif
                    </div>

                    {{-- Preview visual --}}
                    <div
                        class="w-full h-24 rounded-sm bg-gradient-to-br from-gray-50 to-gray-100 mb-4 flex items-center justify-center border border-gray-100">
                        <span class="material-symbols-outlined text-3xl text-gray-300">{{ $post->content_type_icon }}</span>
                    </div>

                    {{-- Título + Copy --}}
                    <h3 class="font-semibold text-sm text-gray-900 group-hover:text-primary-foreground transition-colors">
                        {{ $post->title }}</h3>
                    @if ($post->copy)
                        <p class="text-xs text-gray-500 mt-1.5 line-clamp-2">{{ $post->copy }}</p>
                    @endif

                    {{-- Campanha vinculada --}}
                    @if ($post->campaign)
                        <div class="mt-3 pt-3 border-t border-gray-100">
                            <div class="flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-[14px] text-gray-400">campaign</span>
                                <span class="text-xs text-gray-500 truncate">{{ $post->campaign->name }}</span>
                            </div>
                        </div>
                    @endif
                </a>
            @endforeach
        </div>
    @endif

    {{-- Ideias de postagem baseadas no nicho --}}
    @if (!empty($suggestions))
        <div class="mt-12">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 rounded-none bg-amber-50 border border-amber-100 flex items-center justify-center">
                    <span class="material-symbols-outlined text-amber-500 text-lg">lightbulb</span>
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">Ideias de postagem</h2>
                    <p class="text-sm text-gray-500">Sugestões baseadas na análise do nicho
                        <strong>{{ $client->niche ?? 'geral' }}</strong></p>
                </div>
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                @foreach ($suggestions as $suggestion)
                        <div class="card p-5 group hover:shadow-md transition-all relative overflow-hidden">
                            {{-- Accent bar --}}
                            <div class="absolute top-0 left-0 w-1 h-full bg-amber-400"></div>

                            <div class="flex items-start gap-4 pl-3">
                                {{-- Content type icon --}}
                                <div
                                    class="w-10 h-10 rounded-none bg-gray-50 border border-gray-100 flex items-center justify-center flex-shrink-0">
                                    <span class="material-symbols-outlined text-[18px] text-gray-600">
                                        {{ match ($suggestion['type']) {
                        'video' => 'videocam',
                        'reels' => 'smart_display',
                        'carrossel' => 'view_carousel',
                        'story' => 'amp_stories',
                        default => 'image',
                    } }}
                                    </span>
                                </div>

                                <div class="flex-1 min-w-0">
                                    {{-- Header --}}
                                    <div class="flex items-center gap-2 mb-1.5">
                                        <span
                                            class="badge bg-gray-100 text-gray-600 text-[10px]">{{ ucfirst($suggestion['type']) }}</span>
                                        <span
                                            class="badge bg-primary/15 text-primary-foreground text-[10px]">{{ $suggestion['objective'] }}</span>
                                    </div>

                                    {{-- Title --}}
                                    <h3 class="font-semibold text-sm text-gray-900 leading-snug">{{ $suggestion['title'] }}</h3>

                                    {{-- Reason --}}
                                    <p class="text-xs text-gray-500 mt-2 leading-relaxed">{{ $suggestion['reason'] }}</p>

                                    {{-- CTA --}}
                                    <a href="{{ route('posts.create', ['client' => $client, 'suggested_title' => $suggestion['title'], 'suggested_type' => $suggestion['type'], 'suggested_objective' => match ($suggestion['objective']) { 'Engajamento' => 'engajamento', 'Conversão' => 'conversao', 'Branding' => 'branding', 'Educação' => 'educacao', default => ''}]) }}"
                                        class="inline-flex items-center gap-1.5 mt-3 text-xs font-bold text-primary-foreground hover:text-primary transition-colors">
                                        <span class="material-symbols-outlined text-[14px]">add</span>
                                        Criar esta postagem
                                    </a>
                                </div>
                            </div>
                        </div>
                @endforeach
            </div>
        </div>
    @endif
</x-app-layout>
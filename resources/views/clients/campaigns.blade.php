<x-app-layout>
    <x-slot name="title">Campanhas · {{ $client->name }}</x-slot>

    <x-slot name="clientBar">
        <x-client-subnav :client="$client" />
    </x-slot>

    <div class="mb-6">
        <h2 class="text-lg font-semibold text-gray-900">Campanhas</h2>
        <p class="text-sm text-gray-500 mt-0.5">Gerencie campanhas e vincule postagens a cada uma delas.</p>
    </div>

    <div x-data="{ expandedCampaign: null, showLinkModal: false, linkTarget: '' }" class="space-y-6">

        @foreach ($campaigns as $i => $campaign)
            <div class="card overflow-hidden">
                {{-- Campaign Header --}}
                <div class="p-6 cursor-pointer hover:bg-gray-50/50 transition-colors"
                     @click="expandedCampaign = expandedCampaign === {{ $i }} ? null : {{ $i }}">
                    <div class="flex flex-wrap items-start justify-between gap-4">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-2">
                                <div class="w-10 h-10 rounded-none flex items-center justify-center {{ match($campaign['status']) {
                                    'Ativa' => 'bg-green-50 border border-green-100',
                                    'Pausada' => 'bg-amber-50 border border-amber-100',
                                    default => 'bg-gray-50 border border-gray-100'
                                } }}">
                                    <span class="material-symbols-outlined text-lg {{ match($campaign['status']) {
                                        'Ativa' => 'text-green-600',
                                        'Pausada' => 'text-amber-600',
                                        default => 'text-gray-400'
                                    } }}">campaign</span>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-900 text-lg">{{ $campaign['name'] }}</h3>
                                    <div class="flex items-center gap-3 text-xs text-gray-500 mt-0.5">
                                        <span>{{ $campaign['period'] }}</span>
                                        <span>{{ $campaign['budget'] }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="badge {{ match($campaign['status']) {
                                'Ativa' => 'bg-green-100 text-green-700',
                                'Pausada' => 'bg-amber-100 text-amber-700',
                                default => 'bg-gray-100 text-gray-600'
                            } }}">{{ $campaign['status'] }}</span>
                            <span class="material-symbols-outlined text-gray-400 transition-transform duration-300"
                                  :class="expandedCampaign === {{ $i }} ? 'rotate-180' : ''">expand_more</span>
                        </div>
                    </div>

                    {{-- Métricas inline (always visible) --}}
                    <div class="grid grid-cols-5 gap-3 mt-4">
                        @foreach ([
                            ['label' => 'Alcance', 'value' => $campaign['reach']],
                            ['label' => 'Impressões', 'value' => $campaign['impressions']],
                            ['label' => 'Cliques', 'value' => $campaign['clicks']],
                            ['label' => 'CTR', 'value' => $campaign['ctr']],
                            ['label' => 'CPC', 'value' => $campaign['cpc']],
                        ] as $metric)
                            <div class="text-center">
                                <div class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">{{ $metric['label'] }}</div>
                                <div class="text-sm font-bold text-gray-900 mt-0.5">{{ $metric['value'] }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Expandable Section: Linked Posts --}}
                <div x-show="expandedCampaign === {{ $i }}"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 -translate-y-2"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     class="border-t border-gray-100">

                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-[18px] text-gray-400">link</span>
                                <h4 class="text-sm font-semibold text-gray-700">Postagens vinculadas</h4>
                                <span class="badge bg-gray-100 text-gray-600">{{ $campaign['linked_posts']->count() }}</span>
                            </div>
                            <button @click.stop="showLinkModal = true; linkTarget = '{{ $campaign['name'] }}'"
                                    class="inline-flex items-center gap-1.5 text-xs font-bold text-primary-foreground hover:text-primary transition-colors">
                                <span class="material-symbols-outlined text-[16px]">add_link</span>
                                Vincular postagem
                            </button>
                        </div>

                        @if ($campaign['linked_posts']->isEmpty())
                            <div class="rounded-sm bg-gray-50 border border-dashed border-gray-200 p-8 text-center">
                                <span class="material-symbols-outlined text-3xl text-gray-300 mb-2">post_add</span>
                                <p class="text-sm text-gray-400 mt-2">Nenhuma postagem vinculada a esta campanha.</p>
                                <button @click.stop="showLinkModal = true; linkTarget = '{{ $campaign['name'] }}'"
                                        class="mt-3 text-xs font-bold text-primary-foreground hover:underline">
                                    + Vincular primeira postagem
                                </button>
                            </div>
                        @else
                            <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                                @foreach ($campaign['linked_posts'] as $post)
                                    <div class="group relative bg-gray-50 rounded-sm border border-gray-100 p-4 hover:border-primary/30 hover:bg-primary/5 transition-all">
                                        <div class="flex items-start gap-3">
                                            <div class="w-9 h-9 rounded-none bg-white border border-gray-200 flex items-center justify-center flex-shrink-0 shadow-sm">
                                                <span class="material-symbols-outlined text-[16px] text-gray-600">{{ $post->content_type_icon }}</span>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <div class="font-medium text-sm text-gray-900 truncate">{{ $post->title }}</div>
                                                <div class="flex items-center gap-2 mt-1">
                                                    <span class="text-[10px] font-semibold text-gray-400 uppercase">{{ $post->content_type_label }}</span>
                                                    @if ($post->objective)
                                                        <span class="text-[10px] text-gray-300">·</span>
                                                        <span class="text-[10px] font-semibold text-gray-400 uppercase">{{ $post->objective_label }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Unlink button --}}
                                        <form method="POST" action="{{ route('posts.link-campaign', [$client, $post]) }}"
                                              class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="campaign" value="">
                                            <button type="submit" class="w-6 h-6 rounded-full bg-red-50 border border-red-100 flex items-center justify-center hover:bg-red-100 transition-colors"
                                                    title="Desvincular">
                                                <span class="material-symbols-outlined text-[14px] text-red-500">close</span>
                                            </button>
                                        </form>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach

        {{-- Modal: Link Post to Campaign --}}
        <div x-show="showLinkModal" x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center p-4"
             @keydown.escape.window="showLinkModal = false">

            {{-- Backdrop --}}
            <div class="absolute inset-0 bg-black/30 backdrop-blur-sm" @click="showLinkModal = false"></div>

            {{-- Modal --}}
            <div class="relative bg-white rounded-sm shadow-2xl w-full max-w-lg border border-gray-100 overflow-hidden"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100">

                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                    <div>
                        <h3 class="font-semibold text-gray-900">Vincular postagem</h3>
                        <p class="text-xs text-gray-500 mt-0.5">Selecione uma postagem para vincular à campanha <strong x-text="linkTarget"></strong></p>
                    </div>
                    <button @click="showLinkModal = false" class="w-8 h-8 rounded-full hover:bg-gray-100 flex items-center justify-center transition-colors">
                        <span class="material-symbols-outlined text-gray-400">close</span>
                    </button>
                </div>

                <div class="p-6 max-h-[400px] overflow-y-auto">
                    @if ($unlinkedPosts->isEmpty())
                        <div class="text-center py-8">
                            <span class="material-symbols-outlined text-3xl text-gray-300">inbox</span>
                            <p class="text-sm text-gray-400 mt-2">Todas as postagens já estão vinculadas a campanhas.</p>
                            <a href="{{ route('posts.create', $client) }}" class="mt-3 inline-flex items-center gap-1.5 text-xs font-bold text-primary-foreground hover:underline">
                                <span class="material-symbols-outlined text-[14px]">add</span>
                                Criar nova postagem
                            </a>
                        </div>
                    @else
                        <div class="space-y-2">
                            @foreach ($unlinkedPosts as $post)
                                <form method="POST" action="{{ route('posts.link-campaign', [$client, $post]) }}">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="campaign" x-bind:value="linkTarget">
                                    <button type="submit"
                                            class="w-full flex items-center gap-3 p-3 rounded-sm border border-gray-100 hover:border-primary hover:bg-primary/5 transition-all text-left group">
                                        <div class="w-9 h-9 rounded-none bg-gray-50 border border-gray-200 flex items-center justify-center flex-shrink-0 group-hover:border-primary group-hover:bg-primary/10 transition-colors">
                                            <span class="material-symbols-outlined text-[16px] text-gray-600 group-hover:text-primary-foreground transition-colors">{{ $post->content_type_icon }}</span>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="font-medium text-sm text-gray-900 truncate group-hover:text-primary-foreground transition-colors">{{ $post->title }}</div>
                                            <div class="flex items-center gap-2 mt-0.5">
                                                <span class="text-[10px] font-semibold text-gray-400 uppercase">{{ $post->content_type_label }}</span>
                                                @if ($post->objective)
                                                    <span class="text-[10px] text-gray-300">·</span>
                                                    <span class="text-[10px] font-semibold text-gray-400 uppercase">{{ $post->objective_label }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        <span class="material-symbols-outlined text-gray-300 group-hover:text-primary-foreground transition-colors">add_link</span>
                                    </button>
                                </form>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

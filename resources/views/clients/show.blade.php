<x-app-layout>
    <x-slot name="title">{{ $client->name }}</x-slot>

    <x-slot name="clientBar">
        <x-client-subnav :client="$client" />
    </x-slot>

    {{-- KPIs --}}
    <div class="grid gap-4 grid-cols-2 lg:grid-cols-4 mb-8">
        <div class="card p-5">
            <div class="flex items-center justify-between mb-3">
                <div class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Alcance</div>
                <div class="w-8 h-8 rounded-none bg-blue-50 flex items-center justify-center">
                    <span class="material-symbols-outlined text-blue-500 text-lg">visibility</span>
                </div>
            </div>
            <div class="text-2xl font-bold text-gray-900">{{ $metrics['reach'] }}</div>
            <div class="text-xs text-green-600 font-medium mt-1">{{ $metrics['reach_change'] }} vs mês anterior</div>
        </div>

        <div class="card p-5">
            <div class="flex items-center justify-between mb-3">
                <div class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Postagens</div>
                <div class="w-8 h-8 rounded-none bg-primary/10 flex items-center justify-center">
                    <span class="material-symbols-outlined text-primary-foreground text-lg">edit_note</span>
                </div>
            </div>
            <div class="text-2xl font-bold text-gray-900">{{ $metrics['posts_count'] }}</div>
            <div class="text-xs text-gray-500 font-medium mt-1">total criadas</div>
        </div>

        <div class="card p-5">
            <div class="flex items-center justify-between mb-3">
                <div class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Campanhas</div>
                <div class="w-8 h-8 rounded-none bg-violet-50 flex items-center justify-center">
                    <span class="material-symbols-outlined text-violet-500 text-lg">campaign</span>
                </div>
            </div>
            <div class="text-2xl font-bold text-gray-900">{{ $metrics['campaigns_active'] }}</div>
            <div class="text-xs text-gray-500 font-medium mt-1">ativas</div>
        </div>

        <div class="card p-5">
            <div class="flex items-center justify-between mb-3">
                <div class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Engajamento</div>
                <div class="w-8 h-8 rounded-none bg-amber-50 flex items-center justify-center">
                    <span class="material-symbols-outlined text-amber-500 text-lg">trending_up</span>
                </div>
            </div>
            <div class="text-2xl font-bold text-gray-900">{{ $metrics['engagement'] }}</div>
            <div class="text-xs text-green-600 font-medium mt-1">{{ $metrics['engagement_change'] }}</div>
        </div>
    </div>

    {{-- Conteúdo principal --}}
    <div class="grid gap-6 lg:grid-cols-3">
        {{-- Postagens recentes --}}
        <div class="lg:col-span-2">
            <div class="card">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                    <h2 class="font-semibold text-gray-900">Últimas postagens</h2>
                    <a href="{{ route('clients.posts', $client) }}"
                        class="text-sm text-gray-500 hover:text-primary-foreground font-medium transition-colors">Ver
                        todas →</a>
                </div>
                <div class="divide-y divide-gray-50">
                    @forelse ($recentPosts as $post)
                        <a href="{{ route('posts.edit', [$client, $post]) }}"
                            class="flex items-center gap-4 px-6 py-4 hover:bg-gray-50/50 transition-colors group">
                            <div
                                class="w-10 h-10 rounded-none bg-gray-50 border border-gray-100 flex items-center justify-center flex-shrink-0">
                                <span
                                    class="material-symbols-outlined text-[18px] text-gray-500">{{ $post->content_type_icon }}</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div
                                    class="font-medium text-sm text-gray-900 truncate group-hover:text-primary-foreground transition-colors">
                                    {{ $post->title }}</div>
                                <div class="text-xs text-gray-500 mt-0.5">{{ $post->content_type_label }} ·
                                    {{ $post->objective_label }}</div>
                            </div>
                            @if ($post->campaign)
                                <span
                                    class="badge bg-primary/20 text-primary-foreground">{{ Str::limit($post->campaign->name, 20) }}</span>
                            @else
                                <span class="badge bg-gray-100 text-gray-500">Sem campanha</span>
                            @endif
                        </a>
                    @empty
                        <div class="px-6 py-10 text-center text-sm text-gray-400">
                            Nenhuma postagem criada.
                            <a href="{{ route('posts.create', $client) }}"
                                class="text-primary-foreground font-semibold hover:underline ml-1">Criar postagem</a>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">
            {{-- Engajamento últimos 7 dias --}}
            <div class="card p-6">
                <h2 class="font-semibold text-gray-900 mb-4">Engajamento — últimos 7 dias</h2>
                <div class="flex items-end gap-1.5 h-32">
                    @foreach ([65, 40, 78, 52, 90, 85, 72] as $i => $val)
                        <div class="flex-1 flex flex-col items-center gap-1">
                            <div class="w-full rounded-none bg-primary/{{ $val > 70 ? '60' : '30' }} transition-all hover:bg-primary"
                                style="height: {{ $val }}%"></div>
                            <span
                                class="text-[10px] text-gray-400">{{ ['Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb', 'Dom'][$i] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Redes sociais ativas --}}
            <div class="card p-6">
                <h2 class="font-semibold text-gray-900 mb-4">Redes sociais</h2>
                @if (!empty($client->channels))
                    <div class="space-y-2">
                        @foreach ($client->channels as $channel)
                            <div class="flex items-center gap-3 py-2">
                                <div class="w-2 h-2 rounded-full bg-primary"></div>
                                <span class="text-sm text-gray-700">{{ $channel }}</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-400">Nenhuma rede social configurada.</p>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
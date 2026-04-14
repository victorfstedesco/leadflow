<x-app-layout>
    <x-slot name="title">Postagens · {{ $client->name }}</x-slot>

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

    {{-- Filtros / Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-lg font-semibold text-gray-900">Postagens</h2>
            <p class="text-sm text-gray-500 mt-0.5">Conteúdos sendo produzidos e publicados para este cliente.</p>
        </div>
    </div>

    {{-- Lista de Postagens --}}
    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
        @foreach ($posts as $post)
            <div class="card p-5 hover:shadow-md transition group">
                {{-- Plataforma + Status --}}
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-none {{ $post['platform_bg'] }} flex items-center justify-center">
                            <span class="material-symbols-outlined text-[16px] text-gray-700">{{ $post['platform_icon'] }}</span>
                        </div>
                        <span class="text-xs font-medium text-gray-500">{{ $post['platform'] }}</span>
                    </div>
                    <span class="badge {{ $post['status'] === 'Publicada' ? 'bg-green-100 text-green-700' : ($post['status'] === 'Agendada' ? 'bg-blue-100 text-blue-700' : ($post['status'] === 'Em produção' ? 'bg-amber-100 text-amber-700' : 'bg-gray-100 text-gray-600')) }}">
                        {{ $post['status'] }}
                    </span>
                </div>

                {{-- Preview visual --}}
                <div class="w-full h-32 rounded-sm bg-gradient-to-br {{ $post['gradient'] }} mb-4 flex items-center justify-center border border-gray-100">
                    <span class="material-symbols-outlined text-4xl text-gray-800 opacity-50">{{ $post['platform_icon'] }}</span>
                </div>

                {{-- Info --}}
                <h3 class="font-semibold text-sm text-gray-900 group-hover:text-primary-foreground transition-colors">{{ $post['title'] }}</h3>
                <p class="text-xs text-gray-500 mt-1.5 line-clamp-2">{{ $post['description'] }}</p>

                {{-- Footer --}}
                <div class="flex items-center justify-between mt-4 pt-3 border-t border-gray-100">
                    <span class="text-xs text-gray-400">{{ $post['date'] }}</span>
                    @if ($post['status'] === 'Publicada')
                        <div class="flex items-center gap-3 text-xs text-gray-500">
                            <span class="flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                                {{ $post['likes'] }}
                            </span>
                            <span class="flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                                {{ $post['comments'] }}
                            </span>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</x-app-layout>

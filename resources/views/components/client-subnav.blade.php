@props(['client'])

{{-- Client context bar — sticky below navbar, green bg,  bottom --}}
<div class="sticky justify-center top-16 z-30 w-full">
    <div class="bg-zinc-50 border-gray-200/80 border-b">
        <div class="max-w-6xl mx-auto px-6">
            <div class="flex items-center justify-between h-12 gap-6">

                {{-- Left: Back + Avatar + Name --}}
                <div class="flex items-center gap-3 flex-shrink-0">
                    <a href="{{ route('clients.index') }}"
                       class="flex items-center justify-center w-7 h-7  text-primary-foreground/50 hover:text-primary-foreground hover:bg-primary/15 transition-all duration-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </a>
                    <div class="w-px h-5 bg-primary/20"></div>
                    <div class="flex items-center gap-2.5">
                        <div class="inline-flex h-7 w-7 items-center justify-center  bg-primary/30 text-primary-foreground font-bold text-[11px] ring-1 ring-primary/20">
                            {{ strtoupper(substr($client->name, 0, 2)) }}
                        </div>
                        <span class="text-sm font-semibold text-primary-foreground tracking-tight">{{ $client->name }}</span>
                        @if($client->niche)
                            <span class="hidden sm:inline text-xs text-primary-foreground/50 font-medium">· {{ $client->niche }}</span>
                        @endif
                    </div>
                </div>

                {{-- Right: Navigation tabs --}}
                <nav class="flex items-center gap-1 -mb-px overflow-x-auto">
                    @php
                        $tabs = [
                            ['route' => 'clients.show', 'icon' => 'grid_view', 'label' => 'Dashboard', 'match' => request()->routeIs('clients.show')],
                            ['route' => 'clients.posts', 'icon' => 'edit_note', 'label' => 'Postagens', 'match' => request()->routeIs('clients.posts') || request()->routeIs('posts.*')],
                            ['route' => 'clients.campaigns', 'icon' => 'campaign', 'label' => 'Campanhas', 'match' => request()->routeIs('clients.campaigns') || request()->routeIs('campaigns.*')],
                            ['route' => 'plannings.index', 'icon' => 'flag', 'label' => 'Planejamento', 'match' => request()->routeIs('plannings.*') || request()->routeIs('goals.*')],
                            ['route' => 'clients.settings', 'icon' => 'settings', 'label' => 'Configurações', 'match' => request()->routeIs('clients.settings')],
                        ];
                    @endphp

                    @foreach($tabs as $tab)
                        <a href="{{ route($tab['route'], $client) }}"
                           class="flex items-center gap-1.5 px-4 py-3 text-sm font-medium border-b-2 transition-colors whitespace-nowrap
                                  {{ $tab['match']
                                      ? 'border-primary text-primary-foreground'
                                      : 'border-transparent text-gray-500 hover:text-gray-900 hover:border-gray-300' }}">
                            <span class="material-symbols-outlined text-[18px]">{{ $tab['icon'] }}</span>
                            {{ $tab['label'] }}
                        </a>
                    @endforeach
                </nav>

            </div>
        </div>
    </div>
</div>
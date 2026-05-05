<nav x-data="{ open: false }" class="sticky top-0 z-40 bg-white/90 backdrop-blur-xl">
    <div class="max-w-6xl mx-auto px-6">
        <div class="flex items-center justify-between h-16">
            <div class="flex items-center gap-10">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                    <span
                        class="inline-flex items-center justify-center h-8 w-8 rounded-none bg-primary text-primary-foreground font-bold">L</span>
                    <span class="font-bold text-lg tracking-tight text-gray-900">LeadFlow</span>
                </a>

                <div class="hidden sm:flex items-center gap-2">
                    <a href="{{ route('dashboard') }}"
                        class="px-4 py-2 rounded-none text-sm font-semibold transition-all duration-300 {{ request()->routeIs('dashboard') ? 'bg-primary/20 text-primary-foreground' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}">
                        Dashboard
                    </a>
                    <a href="{{ route('clients.index') }}"
                        class="px-4 py-2 rounded-none text-sm font-semibold transition-all duration-300 {{ request()->routeIs('clients.*') || request()->routeIs('posts.*') ? 'bg-primary/20 text-primary-foreground' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}">
                        Clientes
                    </a>
                </div>
            </div>

            <div class="hidden sm:flex items-center gap-3">
                <button
                    class="material-symbols-outlined text-gray-400 hover:text-gray-700 transition">notifications</button>
                <button class="material-symbols-outlined text-gray-400 hover:text-gray-700 transition">settings</button>
                <div class="w-px h-6 bg-gray-200 mx-2"></div>
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button
                            class="inline-flex items-center gap-2 px-3 py-2 rounded-none text-sm font-medium text-gray-700 hover:bg-gray-100 transition">
                            <span
                                class="inline-flex h-8 w-8 items-center justify-center rounded-none bg-gray-900 text-white shadow-sm font-bold tracking-wider">
                                {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}
                            </span>
                        </button>
                    </x-slot>
                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">Perfil</x-dropdown-link>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                onclick="event.preventDefault(); this.closest('form').submit();">
                                Sair
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="p-2 rounded-none text-gray-500 hover:bg-gray-100">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round"
                            stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden border-t border-gray-200">
        <div class="px-4 py-3 space-y-1">
            <a href="{{ route('dashboard') }}"
                class="block px-3 py-2 rounded-none text-sm font-medium text-gray-700 hover:bg-gray-100">Dashboard</a>
            <a href="{{ route('clients.index') }}"
                class="block px-3 py-2 rounded-none text-sm font-medium text-gray-700 hover:bg-gray-100">Clientes</a>
            <a href="{{ route('profile.edit') }}"
                class="block px-3 py-2 rounded-none text-sm font-medium text-gray-700 hover:bg-gray-100">Perfil</a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="block w-full text-left px-3 py-2 rounded-none text-sm font-medium text-gray-700 hover:bg-gray-100">Sair</button>
            </form>
        </div>
    </div>
</nav>
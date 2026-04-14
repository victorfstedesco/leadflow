@props(['client'])

<div class="border-b border-gray-200 bg-white/60 backdrop-blur-sm -mx-6 px-6 mb-8">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-1 -mb-px overflow-x-auto">
            <a href="{{ route('clients.show', $client) }}"
               class="flex items-center gap-2 px-4 py-3.5 text-sm font-medium border-b-2 transition-colors whitespace-nowrap
                      {{ request()->routeIs('clients.show') ? 'border-primary text-primary-foreground' : 'border-transparent text-gray-500 hover:text-gray-900 hover:border-gray-300' }}">
                <span class="material-symbols-outlined text-[18px]">grid_view</span>
                Dashboard
            </a>
            <a href="{{ route('clients.posts', $client) }}"
               class="flex items-center gap-2 px-4 py-3.5 text-sm font-medium border-b-2 transition-colors whitespace-nowrap
                      {{ request()->routeIs('clients.posts') ? 'border-primary text-primary-foreground' : 'border-transparent text-gray-500 hover:text-gray-900 hover:border-gray-300' }}">
                <span class="material-symbols-outlined text-[18px]">campaign</span>
                Postagens
            </a>
            <a href="{{ route('clients.analytics', $client) }}"
               class="flex items-center gap-2 px-4 py-3.5 text-sm font-medium border-b-2 transition-colors whitespace-nowrap
                      {{ request()->routeIs('clients.analytics') ? 'border-primary text-primary-foreground' : 'border-transparent text-gray-500 hover:text-gray-900 hover:border-gray-300' }}">
                <span class="material-symbols-outlined text-[18px]">bar_chart</span>
                Analytics
            </a>
            <a href="{{ route('clients.settings', $client) }}"
               class="flex items-center gap-2 px-4 py-3.5 text-sm font-medium border-b-2 transition-colors whitespace-nowrap
                      {{ request()->routeIs('clients.settings') ? 'border-primary text-primary-foreground' : 'border-transparent text-gray-500 hover:text-gray-900 hover:border-gray-300' }}">
                <span class="material-symbols-outlined text-[18px]">settings</span>
                Configurações
            </a>
        </div>
    </div>
</div>

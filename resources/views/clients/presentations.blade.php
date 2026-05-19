<x-app-layout>
    <x-slot name="title">Apresentações · {{ $client->name }}</x-slot>

    <x-slot name="clientBar">
        <x-client-subnav :client="$client" />
    </x-slot>

    <div class="space-y-6">

        {{-- Header --}}
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <h2 class="text-lg font-semibold text-gray-900">Apresentações</h2>
                <p class="text-sm text-gray-500 mt-0.5">Links gerados para compartilhar relatórios com clientes.</p>
            </div>
            <a href="{{ route('clients.campaigns', $client) }}"
               class="inline-flex items-center gap-1.5 px-4 py-2 bg-primary-foreground text-white text-sm font-bold hover:bg-primary-foreground/90 transition-colors">
                <span class="material-symbols-outlined text-[16px]">add</span>
                Nova apresentação
            </a>
        </div>

        @if (session('success'))
            <div class="flex items-center gap-3 px-4 py-3 bg-green-50 border border-green-100 text-green-700 text-sm font-medium">
                <span class="material-symbols-outlined text-[18px]">check_circle</span>
                {{ session('success') }}
            </div>
        @endif

        {{-- Lista --}}
        @forelse ($presentations as $p)
            @php
                $isActive  = $p->isAvailable();
                $isExpired = $p->isExpired();

                [$badgeBg, $badgeText, $dot] = match(true) {
                    !$p->active  => ['bg-gray-100', 'text-gray-500',  'bg-gray-300'],
                    $isExpired   => ['bg-red-50',   'text-red-600',   'bg-red-400'],
                    default      => ['bg-green-50', 'text-green-700', 'bg-green-400'],
                };

                $campaignCount = count($p->campaign_ids ?? []);
            @endphp
            <div class="card p-5">
                <div class="flex flex-wrap items-start justify-between gap-4">

                    {{-- Info --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2.5 mb-1.5">
                            <span class="w-2 h-2 rounded-full {{ $dot }} flex-shrink-0"></span>
                            <h3 class="font-semibold text-gray-900 truncate">
                                {{ $p->title ?? 'Relatório de Campanhas' }}
                            </h3>
                            <span class="text-xs font-semibold px-2 py-0.5 rounded-full {{ $badgeBg }} {{ $badgeText }}">
                                {{ $p->statusLabel() }}
                            </span>
                        </div>

                        <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-gray-500 mt-1">
                            <span class="flex items-center gap-1">
                                <span class="material-symbols-outlined text-[14px]">campaign</span>
                                {{ $campaignCount }} {{ Str::plural('campanha', $campaignCount) }}
                            </span>
                            @if ($p->since && $p->until)
                                <span class="flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[14px]">date_range</span>
                                    {{ \Carbon\Carbon::parse($p->since)->format('d/m/Y') }} → {{ \Carbon\Carbon::parse($p->until)->format('d/m/Y') }}
                                </span>
                            @endif
                            <span class="flex items-center gap-1">
                                <span class="material-symbols-outlined text-[14px]">schedule</span>
                                Criada {{ $p->created_at->diffForHumans() }}
                            </span>
                            @if ($p->expires_at)
                                <span class="flex items-center gap-1 {{ $isExpired ? 'text-red-500 font-semibold' : '' }}">
                                    <span class="material-symbols-outlined text-[14px]">timer</span>
                                    {{ $isExpired ? 'Expirou' : 'Expira' }} {{ $p->expires_at->format('d/m/Y \à\s H:i') }}
                                </span>
                            @else
                                <span class="flex items-center gap-1 text-gray-400">
                                    <span class="material-symbols-outlined text-[14px]">all_inclusive</span>
                                    Sem expiração
                                </span>
                            @endif
                        </div>

                        {{-- URL --}}
                        @if ($isActive)
                            <div class="mt-3 flex items-center gap-2"
                                 x-data="{ copied: false }">
                                <input type="text" readonly
                                       value="{{ route('presentation.show', $p->token) }}"
                                       class="flex-1 px-3 py-1.5 text-xs border border-gray-200 bg-gray-50 text-gray-600 min-w-0 truncate select-all">
                                <button @click="navigator.clipboard.writeText('{{ route('presentation.show', $p->token) }}'); copied = true; setTimeout(() => copied = false, 2000)"
                                        class="flex-shrink-0 inline-flex items-center gap-1 px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-xs font-semibold text-gray-700 transition-colors">
                                    <span class="material-symbols-outlined text-[13px]" x-text="copied ? 'check' : 'content_copy'"></span>
                                    <span x-text="copied ? 'Copiado!' : 'Copiar'"></span>
                                </button>
                                <a href="{{ route('presentation.show', $p->token) }}" target="_blank"
                                   class="flex-shrink-0 inline-flex items-center gap-1 px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-xs font-semibold text-gray-700 transition-colors">
                                    <span class="material-symbols-outlined text-[13px]">open_in_new</span>
                                    Abrir
                                </a>
                            </div>
                        @else
                            <div class="mt-3 text-xs text-gray-400 italic">
                                Link indisponível — apresentação {{ $p->active ? 'expirada' : 'desativada' }}.
                            </div>
                        @endif
                    </div>

                    {{-- Ações --}}
                    @if ($p->active)
                        <form method="POST"
                              action="{{ route('presentations.deactivate', [$client, $p]) }}"
                              x-data
                              @submit.prevent="if(confirm('Desativar esta apresentação? O link deixará de funcionar imediatamente.')) $el.submit()">
                            @csrf @method('PATCH')
                            <button type="submit"
                                    class="inline-flex items-center gap-1.5 px-3 py-2 border border-red-200 bg-red-50 text-red-600 text-xs font-bold hover:bg-red-100 transition-colors">
                                <span class="material-symbols-outlined text-[15px]">link_off</span>
                                Desativar
                            </button>
                        </form>
                    @endif

                </div>
            </div>
        @empty
            <div class="card p-12 text-center">
                <span class="material-symbols-outlined text-4xl text-gray-300">present_to_all</span>
                <h3 class="text-sm font-semibold text-gray-900 mt-3">Nenhuma apresentação criada</h3>
                <p class="text-sm text-gray-500 mt-1">Vá para Campanhas e clique em "Apresentar" para gerar um link.</p>
                <a href="{{ route('clients.campaigns', $client) }}"
                   class="inline-flex items-center gap-1.5 mt-4 px-4 py-2 bg-primary-foreground text-white text-sm font-bold hover:bg-primary-foreground/90 transition-colors">
                    <span class="material-symbols-outlined text-[16px]">campaign</span>
                    Ir para Campanhas
                </a>
            </div>
        @endforelse

    </div>
</x-app-layout>

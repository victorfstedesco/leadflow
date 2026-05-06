<x-app-layout>
    <x-slot name="title">Clientes</x-slot>

    <div class="mb-12 flex flex-col md:flex-row md:items-end justify-between gap-4 text-center sm:text-left">
        <div>
            <h1 class="section-title">Clientes</h1>
            <p class="text-gray-500 mt-2 text-sm sm:text-base">Gerencie os clientes da sua agência e acesse seus ambientes.</p>
        </div>
        @if ($clients->isNotEmpty())
            <a href="{{ route('clients.create') }}" class="btn-primary shrink-0 mx-auto sm:mx-0">
                <span class="material-symbols-outlined text-[20px]">add</span>
                Novo Cliente
            </a>
        @endif
    </div>

    @if ($clients->isEmpty())
        <div class="card p-12 text-center max-w-2xl mx-auto flex flex-col items-center">
            <div class="w-20 h-20 rounded bg-primary/10 flex items-center justify-center mb-6 text-primary-foreground">
                <span class="material-symbols-outlined text-[40px]">group_add</span>
            </div>
            <h3 class="font-bold text-2xl text-gray-900 mb-2">Nenhum cliente cadastrado</h3>
            <p class="text-gray-500 text-base mb-8 max-w-sm">Comece adicionando o primeiro cliente da sua agência para gerenciar campanhas e postagens.</p>
            <a href="{{ route('clients.create') }}" class="btn-primary">
                <span class="material-symbols-outlined text-[20px]">add</span>
                Novo Cliente
            </a>
        </div>
    @else
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($clients as $client)
                <a href="{{ route('clients.show', $client) }}" class="card p-0 flex flex-col hover:border-primary/40 hover:shadow-xl transition-all duration-300 group overflow-hidden bg-white/50 backdrop-blur-sm">
                    <div class="p-6 md:p-8 flex-1">
                        <div class="flex items-start justify-between mb-6">
                            <div class="inline-flex h-16 w-16 items-center justify-center rounded bg-primary/10 text-primary-foreground font-bold text-2xl shadow-sm group-hover:bg-primary group-hover:text-primary-foreground transition-all duration-500 group-hover:-translate-y-1 group-hover:shadow-md">
                                {{ strtoupper(substr($client->name, 0, 2)) }}
                            </div>
                            @if ($client->niche)
                                <span class="badge bg-white border border-gray-100 text-gray-600 shadow-sm">{{ $client->niche }}</span>
                            @endif
                        </div>
                        <h2 class="font-bold text-xl text-gray-900 mb-2 group-hover:text-primary-foreground transition-colors line-clamp-1">{{ $client->name }}</h2>

                        @if (!empty($client->channels))
                            <div class="flex flex-wrap gap-2 mt-4">
                                @foreach (array_slice($client->channels, 0, 3) as $channel)
                                    <span class="badge bg-surface text-gray-600 border border-gray-100">{{ $channel }}</span>
                                @endforeach
                                @if (count($client->channels) > 3)
                                    <span class="badge bg-surface text-gray-600 border border-gray-100">+{{ count($client->channels) - 3 }}</span>
                                @endif
                            </div>
                        @else
                            <div class="mt-4 text-xs text-gray-400 italic">Nenhum canal configurado</div>
                        @endif
                    </div>

                    <div class="bg-gray-50/50 px-6 py-4 border-t border-gray-100 group-hover:bg-primary/5 transition-colors">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-[18px] text-gray-400 group-hover:text-primary-foreground transition-colors">edit_note</span>
                                <span class="text-sm font-medium text-gray-600 group-hover:text-primary-foreground transition-colors">{{ $client->posts_count }} postagens</span>
                            </div>
                            <span class="material-symbols-outlined text-[20px] text-gray-300 group-hover:text-primary-foreground transition-all group-hover:translate-x-1">arrow_forward</span>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    @endif
</x-app-layout>

<x-app-layout>
    <x-slot name="title">Clientes</x-slot>

    <div class="mb-10 text-center sm:text-left">
        <h1 class="section-title">Clientes</h1>
        <p class="text-gray-500 mt-2 text-sm sm:text-base">Gerencie os clientes da sua agência e acesse seus ambientes.</p>
    </div>

    @if ($clients->isEmpty())
        <div class="card p-12 text-center">
            <div class="w-16 h-16 mx-auto rounded-none bg-primary/10 flex items-center justify-center mb-6">
                <span class="material-symbols-outlined text-[32px] text-primary-foreground">group_add</span>
            </div>
            <h3 class="font-semibold text-gray-900 mb-1">Nenhum cliente cadastrado</h3>
            <p class="text-gray-500 text-sm mb-6">Comece adicionando o primeiro cliente da sua agência.</p>
            <a href="{{ route('clients.create') }}" class="btn-primary">+ Novo cliente</a>
        </div>
    @else
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            {{-- Card para criar novo cliente --}}
            <a href="{{ route('clients.create') }}" class="card flex flex-col items-center justify-center p-8 bg-gray-50/50 border-2 border-dashed border-gray-200 hover:border-primary hover:bg-primary/5 transition-all group min-h-[220px]">
                <div class="w-12 h-12 rounded-none bg-white border border-gray-200 flex items-center justify-center mb-4 group-hover:border-primary group-hover:text-primary-foreground transition-colors shadow-sm">
                    <span class="material-symbols-outlined text-gray-500 group-hover:text-primary-foreground">add</span>
                </div>
                <h3 class="font-bold text-gray-700 group-hover:text-primary-foreground">Adicionar Cliente</h3>
                <p class="text-xs text-gray-400 mt-2 text-center">Configure um novo ambiente de social media</p>
            </a>

            @foreach ($clients as $client)
                <a href="{{ route('clients.show', $client) }}" class="card p-8 hover:shadow-md hover:border-primary/20 transition group">
                    <div class="flex items-start justify-between mb-6">
                        <div class="inline-flex h-12 w-12 items-center justify-center rounded-none bg-primary/20 text-primary-foreground font-bold text-lg">
                            {{ strtoupper(substr($client->name, 0, 2)) }}
                        </div>
                        @if ($client->niche)
                            <span class="badge bg-primary/20 text-primary-foreground">{{ $client->niche }}</span>
                        @endif
                    </div>
                    <h2 class="font-semibold text-lg text-gray-900 group-hover:text-primary-foreground transition-colors">{{ $client->name }}</h2>

                    @if (!empty($client->channels))
                        <div class="flex flex-wrap gap-1.5 mt-3">
                            @foreach (array_slice($client->channels, 0, 3) as $channel)
                                <span class="badge bg-gray-100 text-gray-600">{{ $channel }}</span>
                            @endforeach
                            @if (count($client->channels) > 3)
                                <span class="badge bg-gray-100 text-gray-600">+{{ count($client->channels) - 3 }}</span>
                            @endif
                        </div>
                    @endif

                    <div class="mt-auto pt-5 border-t border-gray-100">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-[16px] text-gray-400">edit_note</span>
                                <span class="text-sm text-gray-500">{{ $client->posts_count }} postagens</span>
                            </div>
                            <span class="material-symbols-outlined text-[18px] text-gray-300 group-hover:text-primary-foreground transition-colors">arrow_forward</span>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    @endif
</x-app-layout>

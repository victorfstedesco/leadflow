<x-app-layout>
    <x-slot name="title">Editar planejamento · {{ $client->name }}</x-slot>

    <x-slot name="clientBar">
        <x-client-subnav :client="$client" />
    </x-slot>

    <div class="max-w-3xl mx-auto">
        <div class="mb-10">
            <a href="{{ route('plannings.show', [$client, $planning]) }}" class="inline-flex items-center gap-2 text-gray-500 font-medium text-sm mb-4 hover:text-gray-900 transition-colors">
                <span class="material-symbols-outlined text-[18px]">arrow_back</span>
                Voltar para Planejamento
            </a>
            <h1 class="section-title">Editar planejamento</h1>
            <p class="text-gray-500 mt-2">Atualize o contexto geral do ciclo <strong>{{ $planning->name }}</strong>.</p>
        </div>

        @include('plannings._form', ['client' => $client, 'planning' => $planning, 'action' => route('plannings.update', [$client, $planning]), 'method' => 'PUT'])

        <div class="text-center mt-8">
            <form method="POST" action="{{ route('plannings.destroy', [$client, $planning]) }}">
                @csrf
                @method('DELETE')
                <button type="submit" class="inline-flex items-center gap-1.5 text-sm font-semibold text-red-500 hover:text-red-700 transition-colors" onclick="return confirm('Excluir este planejamento permanentemente? As campanhas associadas não serão deletadas do Meta Ads, mas perderão o vínculo aqui.')">
                    <span class="material-symbols-outlined text-[18px]">delete</span>
                    Excluir este planejamento
                </button>
            </form>
        </div>
    </div>
</x-app-layout>

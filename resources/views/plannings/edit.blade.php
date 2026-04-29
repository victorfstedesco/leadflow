<x-app-layout>
    <x-slot name="title">Editar planejamento · {{ $client->name }}</x-slot>

    <x-slot name="clientBar">
        <x-client-subnav :client="$client" />
    </x-slot>

    <div class="max-w-2xl mx-auto">
        <a href="{{ route('plannings.show', [$client, $planning]) }}" class="text-sm text-gray-500 hover:text-gray-900">← {{ $planning->name }}</a>
        <h1 class="section-title mt-2 mb-2">Editar planejamento</h1>

        @include('plannings._form', ['client' => $client, 'planning' => $planning, 'action' => route('plannings.update', [$client, $planning]), 'method' => 'PUT'])

        <form method="POST" action="{{ route('plannings.destroy', [$client, $planning]) }}" class="mt-6">
            @csrf
            @method('DELETE')
            <button type="submit" class="text-sm text-red-500 hover:text-red-700 font-medium" onclick="return confirm('Excluir este planejamento? Metas e vínculos serão removidos.')">
                Excluir planejamento
            </button>
        </form>
    </div>
</x-app-layout>

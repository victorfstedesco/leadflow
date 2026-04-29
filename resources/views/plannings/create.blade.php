<x-app-layout>
    <x-slot name="title">Novo planejamento · {{ $client->name }}</x-slot>

    <x-slot name="clientBar">
        <x-client-subnav :client="$client" />
    </x-slot>

    <div class="max-w-2xl mx-auto">
        <a href="{{ route('plannings.index', $client) }}" class="text-sm text-gray-500 hover:text-gray-900">← Planejamentos</a>
        <h1 class="section-title mt-2 mb-2">Novo planejamento</h1>
        <p class="text-gray-500 mb-8">Defina o ciclo de planejamento de <strong>{{ $client->name }}</strong>.</p>

        @include('plannings._form', ['client' => $client, 'action' => route('plannings.store', $client), 'method' => 'POST'])
    </div>
</x-app-layout>

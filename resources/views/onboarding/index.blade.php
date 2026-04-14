<x-app-layout>
    <x-slot name="title">Onboarding</x-slot>

    <div class="max-w-2xl mx-auto">
        <div class="text-center mb-10">
            <span class="badge bg-primary/20 text-primary-foreground mb-4">Bem-vindo ao LeadFlow</span>
            <h1 class="section-title">Vamos configurar sua agência</h1>
            <p class="text-gray-500 mt-2">Em 2 passos você terá seu primeiro cliente pronto para receber leads.</p>
        </div>

        <form method="POST" action="{{ route('onboarding.store') }}" class="card p-8 space-y-8">
            @csrf

            <div>
                <h2 class="font-semibold text-gray-900 mb-4">1. Sua agência</h2>
                <label class="label">Nome da agência</label>
                <input type="text" name="agency_name" required class="input" value="{{ old('agency_name', auth()->user()->name) }}">
                @error('agency_name') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="border-t border-gray-100 pt-8">
                <h2 class="font-semibold text-gray-900 mb-4">2. Primeiro cliente</h2>
                <label class="label">Nome do cliente</label>
                <input type="text" name="first_client" required class="input" value="{{ old('first_client') }}" placeholder="Ex: E-commerce X">
                @error('first_client') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror

                <label class="label mt-6">Canais ativos do cliente</label>
                <div class="grid grid-cols-2 gap-2">
                    @foreach (['Meta Ads', 'Google Ads', 'TikTok Ads', 'Indicação', 'Orgânico', 'WhatsApp'] as $channel)
                        <label class="flex items-center gap-2 px-4 py-2.5 rounded-xl border border-gray-200 cursor-pointer hover:bg-gray-50">
                            <input type="checkbox" name="channels[]" value="{{ $channel }}" class="rounded text-primary focus:ring-primary">
                            <span class="text-sm text-gray-700">{{ $channel }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                <a href="{{ route('clients.index') }}" class="btn-secondary">Pular</a>
                <button class="btn-primary">Concluir</button>
            </div>
        </form>
    </div>
</x-app-layout>

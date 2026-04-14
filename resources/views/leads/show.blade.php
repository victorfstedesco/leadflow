<x-app-layout>
    <x-slot name="title">{{ $lead->name }}</x-slot>

    <div class="mb-8">
        <a href="{{ route('clients.show', $lead->client) }}" class="text-sm text-gray-500 hover:text-gray-900">← {{ $lead->client->name }}</a>
        <div class="flex flex-wrap items-center justify-between gap-4 mt-2">
            <div>
                <h1 class="section-title">{{ $lead->name }}</h1>
                <div class="flex items-center gap-2 mt-2">
                    <span class="badge bg-primary/20 text-primary-foreground">{{ $lead->stage->name }}</span>
                    @if ($lead->source)
                        <span class="badge bg-gray-100 text-gray-700">{{ $lead->source }}</span>
                    @endif
                </div>
            </div>
            <form method="POST" action="{{ route('leads.move', $lead) }}" class="flex items-center gap-2">
                @csrf
                <select name="to_stage_id" class="input !py-2">
                    @foreach ($lead->client->stages as $stage)
                        <option value="{{ $stage->id }}" @selected($stage->id === $lead->funnel_stage_id)>{{ $stage->name }}</option>
                    @endforeach
                </select>
                <button class="btn-primary !py-2 text-sm">Mover</button>
            </form>
        </div>
    </div>

    <div class="grid gap-6 md:grid-cols-3">
        <div class="md:col-span-1 space-y-6">
            <div class="card p-6">
                <h2 class="font-semibold text-gray-900 mb-4">Dados</h2>
                <dl class="space-y-3 text-sm">
                    <div>
                        <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wide">E-mail</dt>
                        <dd class="text-gray-900 mt-0.5">{{ $lead->email ?: '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Telefone</dt>
                        <dd class="text-gray-900 mt-0.5">{{ $lead->phone ?: '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Entrou em</dt>
                        <dd class="text-gray-900 mt-0.5">{{ optional($lead->entered_at)->format('d/m/Y H:i') ?: '—' }}</dd>
                    </div>
                    @if ($lead->notes)
                        <div>
                            <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Observações</dt>
                            <dd class="text-gray-700 mt-0.5 whitespace-pre-line">{{ $lead->notes }}</dd>
                        </div>
                    @endif
                </dl>
            </div>

            <div class="card p-6">
                <h2 class="font-semibold text-gray-900 mb-4">Histórico de funil</h2>
                @if ($lead->histories->isEmpty())
                    <p class="text-sm text-gray-500">Sem movimentações.</p>
                @else
                    <ul class="space-y-3">
                        @foreach ($lead->histories as $h)
                            <li class="text-sm">
                                <div class="text-gray-900">
                                    {{ $h->fromStage?->name ?? 'Entrada' }} <span class="text-gray-400">→</span> <strong>{{ $h->toStage->name }}</strong>
                                </div>
                                <div class="text-xs text-gray-500">{{ $h->moved_at->format('d/m/Y H:i') }}</div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>

        <div class="md:col-span-2 space-y-6">
            <div class="card p-6">
                <h2 class="font-semibold text-gray-900 mb-4">Nova interação</h2>
                <form method="POST" action="{{ route('interactions.store', $lead) }}" class="space-y-4">
                    @csrf
                    <div class="grid md:grid-cols-4 gap-3">
                        <select name="type" class="input md:col-span-1" required>
                            <option value="Ligação">Ligação</option>
                            <option value="WhatsApp">WhatsApp</option>
                            <option value="E-mail">E-mail</option>
                            <option value="Reunião">Reunião</option>
                            <option value="Nota">Nota</option>
                        </select>
                        <textarea name="description" class="input md:col-span-3" rows="2" placeholder="O que aconteceu?" required></textarea>
                    </div>
                    <div class="flex justify-end">
                        <button class="btn-primary">Registrar</button>
                    </div>
                </form>
            </div>

            <div class="card p-6">
                <h2 class="font-semibold text-gray-900 mb-4">Interações</h2>
                @if ($lead->interactions->isEmpty())
                    <p class="text-sm text-gray-500">Nenhuma interação registrada.</p>
                @else
                    <ul class="space-y-4">
                        @foreach ($lead->interactions as $int)
                            <li class="border-l-2 border-primary pl-4">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="badge bg-primary/20 text-primary-foreground">{{ $int->type }}</span>
                                    <span class="text-xs text-gray-500">{{ $int->created_at->format('d/m/Y H:i') }}</span>
                                </div>
                                <p class="text-sm text-gray-700 whitespace-pre-line">{{ $int->description }}</p>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>

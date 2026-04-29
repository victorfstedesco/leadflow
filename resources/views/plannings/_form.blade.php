@props(['client', 'planning' => null, 'action', 'method' => 'POST'])

<form method="POST" action="{{ $action }}" class="card p-8 space-y-6">
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif

    <div>
        <label class="label">Nome do planejamento</label>
        <input type="text" name="name" required class="input" value="{{ old('name', $planning?->name) }}" placeholder="Ex: Planejamento Q2 2026">
        @error('name') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    <div class="grid md:grid-cols-2 gap-5">
        <div>
            <label class="label">Início do período</label>
            <input type="date" name="period_start" class="input" value="{{ old('period_start', $planning?->period_start?->format('Y-m-d')) }}">
            @error('period_start') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="label">Fim do período</label>
            <input type="date" name="period_end" class="input" value="{{ old('period_end', $planning?->period_end?->format('Y-m-d')) }}">
            @error('period_end') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
        </div>
    </div>

    <div>
        <label class="label">Status</label>
        <select name="status" required class="input">
            @foreach (['ativo' => 'Ativo', 'pausado' => 'Pausado', 'concluido' => 'Concluído', 'arquivado' => 'Arquivado'] as $v => $l)
                <option value="{{ $v }}" @selected(old('status', $planning?->status ?? 'ativo') === $v)>{{ $l }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="label">Observações</label>
        <textarea name="notes" rows="4" class="input" placeholder="Estratégia geral, premissas, contexto...">{{ old('notes', $planning?->notes) }}</textarea>
    </div>

    <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
        <a href="{{ $planning ? route('plannings.show', [$client, $planning]) : route('plannings.index', $client) }}" class="btn-secondary">Cancelar</a>
        <button type="submit" class="btn-primary">
            <span class="material-symbols-outlined text-[18px]">save</span>
            Salvar
        </button>
    </div>
</form>

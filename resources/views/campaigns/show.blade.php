<x-app-layout>
    <x-slot name="title">{{ $campaign->name }} · {{ $client->name }}</x-slot>

    <x-slot name="clientBar">
        <x-client-subnav :client="$client" />
    </x-slot>

    <div class="mb-6">
        <a href="{{ route('clients.campaigns', $client) }}" class="text-sm text-gray-500 hover:text-gray-900">← Campanhas</a>
        <h2 class="text-lg font-semibold text-gray-900 mt-2">{{ $campaign->name }}</h2>
        <div class="text-sm text-gray-500 mt-0.5 flex items-center gap-3">
            <span class="badge bg-gray-100 text-gray-700">{{ $campaign->meta_status_label }}</span>
            @if ($campaign->objective)
                <span class="text-xs uppercase tracking-wide">{{ $campaign->objective }}</span>
            @endif
            @if ($campaign->last_synced_at)
                <span class="text-xs text-gray-400">Sincronizada {{ $campaign->last_synced_at->diffForHumans() }}</span>
            @endif
        </div>
    </div>

    @php $insights = $campaign->insights ?? []; @endphp
    <div class="card p-6 mb-6">
        <h3 class="font-semibold text-gray-900 text-sm mb-4">Insights</h3>
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
            @foreach ([
                ['Alcance', isset($insights['reach']) ? number_format($insights['reach'], 0, ',', '.') : '—'],
                ['Impressões', isset($insights['impressions']) ? number_format($insights['impressions'], 0, ',', '.') : '—'],
                ['Cliques', isset($insights['clicks']) ? number_format($insights['clicks'], 0, ',', '.') : '—'],
                ['CTR', isset($insights['ctr']) ? number_format((float)$insights['ctr'], 2, ',', '.') . '%' : '—'],
                ['CPC', isset($insights['cpc']) ? 'R$ ' . number_format((float)$insights['cpc'], 2, ',', '.') : '—'],
            ] as [$label, $value])
                <div>
                    <div class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">{{ $label }}</div>
                    <div class="text-base font-bold text-gray-900 mt-0.5">{{ $value }}</div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="card p-6">
        <h3 class="font-semibold text-gray-900 text-sm mb-4">Postagens vinculadas ({{ $campaign->posts->count() }})</h3>
        @if ($campaign->posts->isEmpty())
            <p class="text-sm text-gray-400">Nenhuma postagem vinculada.</p>
        @else
            <div class="space-y-2">
                @foreach ($campaign->posts as $post)
                    <a href="{{ route('posts.edit', [$client, $post]) }}" class="flex items-center gap-3 p-3 rounded-none border border-gray-100 hover:border-primary/30 hover:bg-primary/5 transition-all">
                        <span class="material-symbols-outlined text-[18px] text-gray-500">{{ $post->content_type_icon }}</span>
                        <div class="flex-1 min-w-0">
                            <div class="text-sm font-medium text-gray-900 truncate">{{ $post->title }}</div>
                            <div class="text-[10px] uppercase text-gray-400">{{ $post->content_type_label }} @if($post->objective) · {{ $post->objective_label }} @endif</div>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</x-app-layout>

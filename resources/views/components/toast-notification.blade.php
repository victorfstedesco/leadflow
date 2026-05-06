{{-- Toast Notification Component --}}
{{-- Listens for session('status'), session('error'), session('warning') --}}

@php
    $toast = null;
    if (session('status')) {
        $toast = ['type' => 'success', 'title' => 'Concluído', 'message' => session('status')];
    } elseif (session('error')) {
        $toast = ['type' => 'error', 'title' => 'Erro', 'message' => session('error')];
    } elseif (session('warning')) {
        $toast = ['type' => 'warning', 'title' => 'Aviso', 'message' => session('warning')];
    }
@endphp

@if ($toast)
<div x-data="{ show: true }"
     x-init="setTimeout(() => show = false, 5000)"
     x-show="show"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0 translate-y-4"
     x-transition:enter-end="opacity-100 translate-y-0"
     x-transition:leave="transition ease-in duration-300"
     x-transition:leave-start="opacity-100 translate-y-0"
     x-transition:leave-end="opacity-0 translate-y-4"
     class="fixed bottom-8 left-1/2 -translate-x-1/2 z-[100] w-full max-w-md px-4">
    
    <div class="bg-white  border border-gray-100 shadow-[0_8px_30px_-4px_rgba(0,0,0,0.12)] px-5 py-4 flex items-start gap-4">
        {{-- Icon --}}
        <div class="w-9 h-9  flex items-center justify-center flex-shrink-0
            {{ match($toast['type']) {
                'success' => 'bg-green-50 border border-green-100',
                'error' => 'bg-red-50 border border-red-100',
                'warning' => 'bg-amber-50 border border-amber-100',
            } }}">
            <span class="material-symbols-outlined text-lg
                {{ match($toast['type']) {
                    'success' => 'text-green-600',
                    'error' => 'text-red-600',
                    'warning' => 'text-amber-600',
                } }}">
                {{ match($toast['type']) {
                    'success' => 'check_circle',
                    'error' => 'error',
                    'warning' => 'warning',
                } }}
            </span>
        </div>

        {{-- Content --}}
        <div class="flex-1 min-w-0">
            <h4 class="text-sm font-bold
                {{ match($toast['type']) {
                    'success' => 'text-green-700',
                    'error' => 'text-red-700',
                    'warning' => 'text-amber-700',
                } }}">
                {{ $toast['title'] }}
            </h4>
            <p class="text-sm text-gray-600 mt-0.5 leading-snug">{{ $toast['message'] }}</p>
        </div>

        {{-- Close button --}}
        <button @click="show = false"
                class="w-7 h-7  hover:bg-gray-100 flex items-center justify-center flex-shrink-0 transition-colors -mt-0.5 -mr-1">
            <span class="material-symbols-outlined text-[16px] text-gray-400">close</span>
        </button>
    </div>
</div>
@endif

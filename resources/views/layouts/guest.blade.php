<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
        <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased bg-gray-50/50">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
            <div>
                <a href="/" class="flex flex-col items-center group">
                    <div class="w-14 h-14 rounded-none bg-primary/10 flex items-center justify-center group-hover:bg-primary transition-colors border border-primary/20 shadow-sm">
                        <span class="material-symbols-outlined text-[32px] text-primary-foreground group-hover:text-white transition-colors">query_stats</span>
                    </div>
                </a>
            </div>

            <div class="card w-full sm:max-w-md mt-8 p-8 sm:p-10 border border-gray-100 shadow-sm">
                {{ $slot }}
            </div>
            
            <div class="mt-8 text-xs text-gray-400">
                &copy; {{ date('Y') }} {{ config('app.name', 'Leadflow') }}. Todos os direitos reservados.
            </div>
        </div>
    </body>
</html>
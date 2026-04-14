<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? 'LeadFlow' }} · LeadFlow</title>

        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,300,0,0" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased min-h-screen bg-surface">
        @include('layouts.navigation')

        @isset($header)
            <header class="max-w-6xl mx-auto px-6 pt-10">
                {{ $header }}
            </header>
        @endisset

        <main class="max-w-6xl mx-auto px-6 py-10">
            @if (session('status'))
                <div class="mb-6 rounded-xl bg-primary/20 border border-primary/40 px-4 py-3 text-sm font-medium text-primary-foreground">
                    {{ session('status') }}
                </div>
            @endif
            {{ $slot }}
        </main>
    </body>
</html>

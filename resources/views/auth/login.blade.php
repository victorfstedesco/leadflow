<x-guest-layout>
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="mb-10 text-center">
        <h1 class="section-title text-2xl font-bold text-gray-900">Bem-vindo(a) de volta</h1>
        <p class="text-gray-500 mt-2 text-sm">Insira suas credenciais para acessar a plataforma.</p>
    </div>

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-sm font-semibold text-gray-700 mb-1.5">{{ __('E-mail') }}</label>
            <input id="email" class="block w-full px-4 py-2.5 rounded-none border border-gray-200 focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none transition-all text-sm placeholder-gray-400 bg-gray-50/50" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="seu@email.com.br" />
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-500 text-xs font-semibold" />
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="block text-sm font-semibold text-gray-700 mb-1.5">{{ __('Senha') }}</label>
            <input id="password" class="block w-full px-4 py-2.5 rounded-none border border-gray-200 focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none transition-all text-sm placeholder-gray-400 bg-gray-50/50" type="password" name="password" required autocomplete="current-password" placeholder="••••••••" />
            <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-500 text-xs font-semibold" />
        </div>

        <!-- Remember Me & Register link -->
        <div class="flex items-center justify-between pt-1">
            <label for="remember_me" class="inline-flex items-center group cursor-pointer">
                <input id="remember_me" type="checkbox" class="rounded-none border-gray-300 text-primary shadow-sm focus:ring-primary focus:ring-offset-0" name="remember">
                <span class="ms-2 text-sm text-gray-600 group-hover:text-gray-900 transition-colors font-medium">{{ __('Lembrar de mim') }}</span>
            </label>
            
            <a class="text-sm font-bold text-primary hover:text-primary-foreground transition-colors group flex items-center gap-1" href="{{ route('register') }}">
                Se registrar
                <span class="material-symbols-outlined text-[16px] transition-transform group-hover:translate-x-1">arrow_forward</span>
            </a>
        </div>

        <div class="pt-5 mt-6 border-t border-gray-100">
            <button type="submit" class="btn-primary w-full justify-center flex items-center gap-2 py-2.5 shadow-sm">
                <span class="material-symbols-outlined text-[20px]">login</span>
                Acessar Plataforma
            </button>
        </div>
    </form>
</x-guest-layout>
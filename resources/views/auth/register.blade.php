<x-guest-layout>
    <div class="mb-10 text-center">
        <h1 class="section-title text-2xl font-bold text-gray-900">Criar nova conta</h1>
        <p class="text-gray-500 mt-2 text-sm">Preencha os dados abaixo para começar.</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        <!-- Name -->
        <div>
            <label for="name" class="block text-sm font-semibold text-gray-700 mb-1.5">{{ __('Nome completo') }}</label>
            <input id="name" class="block w-full px-4 py-2.5 rounded-sm border border-gray-200 focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none transition-all text-sm placeholder-gray-400 bg-gray-50/50" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" placeholder="Ex: João da Silva" />
            <x-input-error :messages="$errors->get('name')" class="mt-2 text-red-500 text-xs font-semibold" />
        </div>

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-sm font-semibold text-gray-700 mb-1.5">{{ __('E-mail') }}</label>
            <input id="email" class="block w-full px-4 py-2.5 rounded-sm border border-gray-200 focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none transition-all text-sm placeholder-gray-400 bg-gray-50/50" type="email" name="email" :value="old('email')" required autocomplete="username" placeholder="seu@email.com.br" />
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-500 text-xs font-semibold" />
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <!-- Password -->
            <div>
                <label for="password" class="block text-sm font-semibold text-gray-700 mb-1.5">{{ __('Senha') }}</label>
                <input id="password" class="block w-full px-4 py-2.5 rounded-sm border border-gray-200 focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none transition-all text-sm placeholder-gray-400 bg-gray-50/50" type="password" name="password" required autocomplete="new-password" placeholder="Mínimo 8 carac." />
                <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-500 text-xs font-semibold" />
            </div>

            <!-- Confirm Password -->
            <div>
                <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-1.5">{{ __('Confirmar Senha') }}</label>
                <input id="password_confirmation" class="block w-full px-4 py-2.5 rounded-sm border border-gray-200 focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none transition-all text-sm placeholder-gray-400 bg-gray-50/50" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Repita a senha" />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-red-500 text-xs font-semibold" />
            </div>
        </div>

        <div class="flex items-center justify-between pt-2">
            <span class="text-sm font-medium text-gray-600">Já possui conta?</span>
            <a class="text-sm font-bold text-primary hover:text-primary-foreground transition-colors group flex items-center gap-1" href="{{ route('login') }}">
                Entrar
                <span class="material-symbols-outlined text-[16px] transition-transform group-hover:translate-x-1">arrow_forward</span>
            </a>
        </div>

        <div class="pt-5 mt-4 border-t border-gray-100">
            <button type="submit" class="btn-primary w-full justify-center flex items-center gap-2 py-2.5 shadow-sm">
                <span class="material-symbols-outlined text-[20px]">person_add</span>
                Cadastrar
            </button>
        </div>
    </form>
</x-guest-layout>
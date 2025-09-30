<x-guest-layout>
    <div class="flex flex-col items-center justify-center mb-6 mt-4">
        <img src="/pusbinjfmkg.png" alt="Logo" class="w-32 h-32 rounded-full shadow mb-4">
    </div>
    <div class="text-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800 mb-1">Aplikasi Manajemen Survei</h1>
        <div class="text-base text-gray-700">Pusat Pembinaan Jabatan Fungsional</div>
        <div class="text-base text-gray-700">Meteorologi, Klimatologi, dan Geofisika</div>
    </div>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-6">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="login-email" :value="__('Email')" class="text-base font-semibold text-gray-700 dark:text-gray-200" />
            <x-text-input id="login-email" class="block mt-1 w-full rounded-lg bg-gray-50 dark:bg-gray-800 dark:text-gray-100 border border-gray-300 dark:border-gray-700 focus:border-green-500 focus:ring-green-200 dark:focus:border-green-400 dark:focus:ring-green-900 transition" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <x-input-label for="login-password" :value="__('Password')" class="text-base font-semibold text-gray-700 dark:text-gray-200" />
            <x-text-input id="login-password" class="block mt-1 w-full rounded-lg bg-gray-50 dark:bg-gray-800 dark:text-gray-100 border border-gray-300 dark:border-gray-700 focus:border-green-500 focus:ring-green-200 dark:focus:border-green-400 dark:focus:ring-green-900 transition" type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center justify-between">
            <label for="login-remember_me" class="inline-flex items-center">
                <input id="login-remember_me" type="checkbox" class="rounded border-gray-300 dark:border-gray-600 text-green-600 shadow-sm focus:ring-green-500 dark:focus:ring-green-400" name="remember">
                <span class="ml-2 text-sm text-gray-600 dark:text-gray-300">{{ __('Remember me') }}</span>
            </label>
            @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 dark:text-gray-300 hover:text-green-700 dark:hover:text-green-400 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif
        </div>

        <div class="flex items-center justify-end">
            <button type="submit" class="ml-3 px-6 py-2 bg-gradient-to-r from-green-500 to-blue-500 hover:from-green-600 hover:to-blue-600 text-white font-bold rounded-lg shadow transition focus:outline-none focus:ring-2 focus:ring-green-400 dark:focus:ring-green-600">
                {{ __('Log in') }}
            </button>
        </div>
    </form>
</x-guest-layout>

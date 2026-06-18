{{-- <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mediconnect - Login</title>

    @vite('resources/css/app.css')
</head>
<body class="bg-gradient-to-r from-teal-400 to-blue-500 min-h-screen flex items-center justify-center">

<div class="w-full max-w-5xl bg-white shadow-2xl rounded-lg overflow-hidden grid grid-cols-1 md:grid-cols-2">

    <!-- LEFT SIDE (LOGIN FORM) -->
    <div class="p-10">

        <!-- Logo + Title -->
        <div class="flex items-center mb-6 space-x-2">
            <!-- Simple medical icon -->
            <div class="bg-teal-500 text-white p-2 rounded-full">
                💊
            </div>
            <h1 class="text-2xl font-bold text-gray-700">MediConnect</h1>
        </div>

        <h2 class="text-xl font-semibold text-gray-600 mb-6">
            Login to your account
        </h2>

        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <!-- Form -->
        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf

            <!-- Email -->
            <div>
                <label class="block text-sm text-gray-600">Email</label>
                <input type="email" name="email" required autofocus
                    class="w-full mt-1 px-3 py-2 border rounded-lg focus:ring-2 focus:ring-teal-400 focus:outline-none">
            </div>

            <!-- Password -->
            <div>
                <label class="block text-sm text-gray-600">Password</label>
                <input type="password" name="password" required
                    class="w-full mt-1 px-3 py-2 border rounded-lg focus:ring-2 focus:ring-teal-400 focus:outline-none">
            </div>

            <!-- Remember + Forgot -->
            <div class="flex items-center justify-between text-sm">
                <label class="flex items-center space-x-2">
                    <input type="checkbox" name="remember" class="rounded">
                    <span>Remember me</span>
                </label>

                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-teal-600 hover:underline">
                        Forgot password?
                    </a>
                @endif
            </div>

            <!-- Button -->
            <button type="submit"
                class="w-full bg-green-500 hover:bg-green-600 text-white py-2 rounded-lg transition duration-300">
                LOGIN
            </button>
        </form>

        <p class="mt-6 text-center text-xs leading-5 text-gray-500">
            Accounts are created and verified by the MediConnect administrator.
        </p>
    </div>

    <!-- RIGHT SIDE (IMAGE) -->
    <div class="hidden md:block relative">
        <img src="https://images.unsplash.com/photo-1582750433449-648ed127bb54"
             class="w-full h-full object-cover"
             alt="Doctor">

        <div class="absolute inset-0 bg-blue-500 opacity-20"></div>
    </div>

</div>

</body>
</html> --}}


<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif

            <x-primary-button class="ms-3">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>

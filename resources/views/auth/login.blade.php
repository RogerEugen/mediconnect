<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mediconnect - Login</title>

    @vite('resources/css/app.css')
</head>
<body class="bg-gradient-to-r from-teal-400 to-blue-500 min-h-screen flex items-center justify-center p-4">

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
                <div class="relative mt-1">
                    <svg class="pointer-events-none absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21.75 6.75v10.5A2.25 2.25 0 0119.5 19.5h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0l-8.54 5.69a2.25 2.25 0 01-2.42 0L2.25 6.75" />
                    </svg>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="email"
                        placeholder="doctor@hospital.com"
                        class="w-full rounded-lg border py-2 pl-10 pr-3 focus:outline-none focus:ring-2 focus:ring-teal-400">
                </div>
                <x-input-error :messages="$errors->get('email')" class="mt-1" />
            </div>

            <!-- Password -->
            <div>
                <label class="block text-sm text-gray-600">Password</label>
                <div class="relative mt-1">
                    <svg class="pointer-events-none absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16.5 10.5V6.75a4.5 4.5 0 00-9 0v3.75m-.75 10.125h10.5A2.625 2.625 0 0019.875 18v-4.875a2.625 2.625 0 00-2.625-2.625H6.75a2.625 2.625 0 00-2.625 2.625V18a2.625 2.625 0 002.625 2.625z" />
                    </svg>
                    <input id="login-password" type="password" name="password" required autocomplete="current-password"
                        class="w-full rounded-lg border py-2 pl-10 pr-11 focus:outline-none focus:ring-2 focus:ring-teal-400">
                    <button type="button" id="toggle-password" class="absolute right-3 top-1/2 -translate-y-1/2 rounded text-gray-400 hover:text-teal-600 focus:outline-none" aria-label="Show password">
                        <svg id="eye-icon" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.04 12.32a1.01 1.01 0 010-.64C3.42 7.51 7.35 4.5 12 4.5c4.64 0 8.57 3 9.96 7.18.07.21.07.43 0 .64C20.58 16.49 16.65 19.5 12 19.5c-4.64 0-8.57-3-9.96-7.18z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </button>
                </div>
                <x-input-error :messages="$errors->get('password')" class="mt-1" />
            </div>

            <!-- Remember + Forgot -->
            <div class="flex items-center justify-between text-sm">
                <label class="flex items-center space-x-2">
                    <input type="checkbox" name="remember" class="rounded border-gray-300 text-teal-600 focus:ring-teal-500">
                    <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16.5 10.5V6.75a4.5 4.5 0 00-9 0v3.75m-.75 10.125h10.5A2.625 2.625 0 0019.875 18v-4.875a2.625 2.625 0 00-2.625-2.625H6.75a2.625 2.625 0 00-2.625 2.625V18a2.625 2.625 0 002.625 2.625z" /></svg>
                    <span>Remember me</span>
                </label>

                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="inline-flex items-center gap-1.5 text-teal-600 hover:underline">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15.75 5.25a3 3 0 11-4.24 4.24M18 8.25l2.25-2.25M15.75 10.5L18 8.25m-6.44 1.19L5.25 15.75V18h2.25v2.25h2.25v-2.5l5.8-5.8" /></svg>
                        Forgot password?
                    </a>
                @endif
            </div>

            <!-- Button -->
            <button type="submit"
                class="flex w-full items-center justify-center gap-2 bg-green-500 hover:bg-green-600 text-white py-2 rounded-lg transition duration-300">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3-3H9m0 0l3-3m-3 3l3 3" /></svg>
                LOGIN
            </button>
        </form>

        <p class="mt-6 text-center text-sm leading-5 text-gray-500">
            New clinician?
            <a href="{{ route('register') }}" class="font-semibold text-teal-600 hover:underline">Create an account</a>
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

<script>
    const password = document.getElementById('login-password');
    const togglePassword = document.getElementById('toggle-password');

    togglePassword.addEventListener('click', () => {
        const showing = password.type === 'text';
        password.type = showing ? 'password' : 'text';
        togglePassword.setAttribute('aria-label', showing ? 'Show password' : 'Hide password');
    });
</script>

</body>
</html>

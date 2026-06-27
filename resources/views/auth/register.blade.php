<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MediConnect - Clinician Registration</title>
    @vite('resources/css/app.css')
</head>
<body class="min-h-screen bg-gradient-to-r from-teal-400 to-blue-500 px-4 py-8 sm:px-6 lg:py-12">

<main class="mx-auto grid w-full max-w-6xl overflow-hidden rounded-xl bg-white shadow-2xl lg:grid-cols-2">
    <section class="p-6 sm:p-10 lg:p-12">
        <div class="mb-7 flex items-center gap-3">
            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-teal-500 text-lg text-white shadow-sm">💊</div>
            <div>
                <h1 class="text-2xl font-bold text-gray-700">MediConnect</h1>
                <p class="text-xs font-medium uppercase tracking-wider text-teal-600">Clinician network</p>
            </div>
        </div>

        <div class="mb-6">
            <h2 class="text-xl font-semibold text-gray-700">Create your account</h2>
            <p class="mt-1 text-sm leading-6 text-gray-500">Register your professional details. An administrator will verify your staff ID before activating access.</p>
        </div>

        <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data" class="space-y-4">
            @csrf

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="name" class="block text-sm text-gray-600">Full name</label>
                    <div class="relative mt-1">
                        <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.5 20.12a7.5 7.5 0 0115 0A17.93 17.93 0 0112 21.75c-2.68 0-5.23-.58-7.5-1.63z"/></svg>
                        <input id="name" name="name" value="{{ old('name') }}" required autofocus autocomplete="name"
                               placeholder="Dr. Full Name" class="w-full rounded-lg border-gray-300 py-2 pl-9 pr-3 text-sm focus:border-teal-400 focus:ring-teal-400">
                    </div>
                    <x-input-error :messages="$errors->get('name')" class="mt-1" />
                </div>
                <div>
                    <label for="email" class="block text-sm text-gray-600">Professional email</label>
                    <div class="relative mt-1">
                        <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21.75 6.75v10.5A2.25 2.25 0 0119.5 19.5h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0l-8.54 5.69a2.25 2.25 0 01-2.42 0L2.25 6.75"/></svg>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email"
                               placeholder="doctor@hospital.com" class="w-full rounded-lg border-gray-300 py-2 pl-9 pr-3 text-sm focus:border-teal-400 focus:ring-teal-400">
                    </div>
                    <x-input-error :messages="$errors->get('email')" class="mt-1" />
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="role" class="block text-sm text-gray-600">Account type</label>
                    <select id="role" name="role" required class="mt-1 w-full rounded-lg border-gray-300 py-2 text-sm focus:border-teal-400 focus:ring-teal-400">
                        <option value="doctor" @selected(old('role', 'doctor') === 'doctor')>Doctor</option>
                        <option value="specialist" @selected(old('role') === 'specialist')>Specialist</option>
                    </select>
                    <x-input-error :messages="$errors->get('role')" class="mt-1" />
                </div>
                <div>
                    <label for="phone" class="block text-sm text-gray-600">Phone number</label>
                    <div class="relative mt-1">
                        <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.25 6.75c0 8.28 6.72 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.37c0-.52-.35-.98-.85-1.1l-4.42-1.1c-.44-.11-.9.05-1.18.4l-.97 1.21c-.28.35-.75.5-1.17.35a12.04 12.04 0 01-7.05-7.05c-.15-.42 0-.89.35-1.17l1.21-.97c.35-.28.51-.74.4-1.18l-1.1-4.42a1.13 1.13 0 00-1.1-.85H4.5A2.25 2.25 0 002.25 4.5v2.25z"/></svg>
                        <input id="phone" name="phone" value="{{ old('phone') }}" required autocomplete="tel"
                               placeholder="07XXXXXXXX" class="w-full rounded-lg border-gray-300 py-2 pl-9 pr-3 text-sm focus:border-teal-400 focus:ring-teal-400">
                    </div>
                    <x-input-error :messages="$errors->get('phone')" class="mt-1" />
                </div>
            </div>

            <div>
                <label for="hospital_name" class="block text-sm text-gray-600">Hospital name</label>
                <div class="relative mt-1">
                    <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3.75 21h16.5M4.5 3h15v18h-15V3zm4.5 4.5h6m-3-3v6m-3 4.5h.01m2.99 0h.01m2.99 0h.01"/></svg>
                    <input id="hospital_name" name="hospital_name" value="{{ old('hospital_name') }}" list="hospital-suggestions" required
                           placeholder="Type your hospital name" autocomplete="organization"
                           class="w-full rounded-lg border-gray-300 py-2 pl-9 pr-3 text-sm focus:border-teal-400 focus:ring-teal-400">
                    <datalist id="hospital-suggestions">
                        @foreach($hospitals as $hospital)
                            <option value="{{ $hospital->name }}"></option>
                        @endforeach
                    </datalist>
                </div>
                <p class="mt-1 text-xs text-gray-400">Type the official hospital name; existing names will be matched automatically.</p>
                <x-input-error :messages="$errors->get('hospital_name')" class="mt-1" />
            </div>

            <div id="specialization-fields" class="rounded-xl border border-teal-200 bg-teal-50 p-4">
                <div class="mb-3">
                    <p class="text-sm font-semibold text-gray-700">Clinical specialty</p>
                    <p class="mt-0.5 text-xs text-gray-500">Specialists only receive cases matching the selected specialty.</p>
                </div>
                <div class="relative">
                    <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <select id="specialization_id" name="specialization_ids[]" class="w-full rounded-lg border-teal-200 bg-white py-2 pl-9 pr-8 text-sm focus:border-teal-400 focus:ring-teal-400">
                        <option value="">Select your specialty</option>
                        @foreach($specializations as $specialization)
                            <option value="{{ $specialization->id }}" @selected(in_array($specialization->id, old('specialization_ids', [])))>{{ $specialization->name }}</option>
                        @endforeach
                    </select>
                </div>
                <x-input-error :messages="$errors->get('specialization_ids')" class="mt-2" />
                <x-input-error :messages="$errors->get('specialization_ids.*')" class="mt-2" />
            </div>

            <div>
                <label for="staff_card" class="block text-sm text-gray-600">Hospital staff ID</label>
                <label for="staff_card" class="mt-1 flex cursor-pointer items-center gap-3 rounded-lg border border-dashed border-teal-300 bg-teal-50/60 px-4 py-3 hover:bg-teal-50">
                    <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-teal-100 text-teal-700">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 9h3.75M15 12h3.75M15 15h3.75M4.5 19.5h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5zm6-9.75a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm1.13 6.75H4.87a3.38 3.38 0 016.76 0z"/></svg>
                    </span>
                    <span>
                        <span class="block text-sm font-semibold text-teal-700">Choose staff card</span>
                        <span id="file-name" class="block text-xs text-gray-500">JPG, PNG or PDF — maximum 5 MB</span>
                    </span>
                </label>
                <input id="staff_card" type="file" name="staff_card" required accept=".jpg,.jpeg,.png,.pdf" class="sr-only">
                <x-input-error :messages="$errors->get('staff_card')" class="mt-1" />
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="password" class="block text-sm text-gray-600">Password</label>
                    <div class="relative mt-1">
                        <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16.5 10.5V6.75a4.5 4.5 0 00-9 0v3.75m-.75 10.125h10.5A2.625 2.625 0 0019.875 18v-4.875a2.625 2.625 0 00-2.625-2.625H6.75a2.625 2.625 0 00-2.625 2.625V18a2.625 2.625 0 002.625 2.625z"/></svg>
                        <input id="password" type="password" name="password" required autocomplete="new-password"
                               class="w-full rounded-lg border-gray-300 py-2 pl-9 pr-3 text-sm focus:border-teal-400 focus:ring-teal-400">
                    </div>
                    <x-input-error :messages="$errors->get('password')" class="mt-1" />
                </div>
                <div>
                    <label for="password_confirmation" class="block text-sm text-gray-600">Confirm password</label>
                    <div class="relative mt-1">
                        <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12.75L11.25 15 15 9.75m1.5.75V6.75a4.5 4.5 0 00-9 0v3.75m-.75 10.125h10.5A2.625 2.625 0 0019.875 18v-4.875a2.625 2.625 0 00-2.625-2.625H6.75a2.625 2.625 0 00-2.625 2.625V18a2.625 2.625 0 002.625 2.625z"/></svg>
                        <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                               class="w-full rounded-lg border-gray-300 py-2 pl-9 pr-3 text-sm focus:border-teal-400 focus:ring-teal-400">
                    </div>
                </div>
            </div>

            <button type="submit" class="w-full rounded-lg bg-green-500 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-400 focus:ring-offset-2">
                SUBMIT REGISTRATION
            </button>
        </form>

        <p class="mt-6 text-center text-sm text-gray-500">
            Already have an approved account?
            <a href="{{ route('login') }}" class="font-semibold text-teal-600 hover:underline">Login here</a>
        </p>
    </section>

    <aside class="relative hidden min-h-full overflow-hidden lg:block">
        <img src="https://images.unsplash.com/photo-1582750433449-648ed127bb54"
             class="absolute inset-0 h-full w-full object-cover" alt="MediConnect doctor">
        <div class="absolute inset-0 bg-gradient-to-t from-blue-900/75 via-blue-500/15 to-teal-400/20"></div>
        <div class="absolute inset-x-0 bottom-0 p-10 text-white">
            <span class="inline-flex rounded-full bg-white/20 px-3 py-1 text-xs font-semibold backdrop-blur">Verified clinicians only</span>
            <h2 class="mt-4 text-3xl font-bold leading-tight">Connect expertise.<br>Improve patient care.</h2>
            <p class="mt-3 max-w-sm text-sm leading-6 text-blue-50">Your staff ID is stored privately and can only be reviewed by a MediConnect administrator.</p>
        </div>
    </aside>
</main>

<script>
    const role = document.getElementById('role');
    const specialties = document.getElementById('specialization-fields');
    const specialty = document.getElementById('specialization_id');
    const staffCard = document.getElementById('staff_card');
    const fileName = document.getElementById('file-name');

    const updateSpecialties = () => {
        const isSpecialist = role.value === 'specialist';
        specialties.classList.toggle('hidden', !isSpecialist);
        specialty.required = isSpecialist;
        specialty.disabled = !isSpecialist;
        if (!isSpecialist) specialty.value = '';
    };
    role.addEventListener('change', updateSpecialties);
    staffCard.addEventListener('change', () => {
        fileName.textContent = staffCard.files[0]?.name ?? 'JPG, PNG or PDF — maximum 5 MB';
    });
    updateSpecialties();
</script>
</body>
</html>

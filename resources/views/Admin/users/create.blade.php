{{-- resources/views/admin/users/create.blade.php --}}

<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-700 leading-tight">
                Add User
            </h2>
            <a href="{{ route('admin.users.index') }}"
               class="text-sm text-gray-600 hover:text-gray-900 dark:text-gray-400 transition">
                &larr; Back
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">

                <form action="{{ route('admin.users.store') }}" method="POST">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        {{-- Left --}}
                        <div class="space-y-5">

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Full Name <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="name" value="{{ old('name') }}"
                                       placeholder="e.g. Dr. John Doe"
                                       class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm @error('name') border-red-400 @enderror">
                                @error('name')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Email Address <span class="text-red-500">*</span>
                                </label>
                                <input type="email" name="email" value="{{ old('email') }}"
                                       placeholder="doctor@hospital.com"
                                       class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm @error('email') border-red-400 @enderror">
                                @error('email')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Role <span class="text-red-500">*</span>
                                </label>
                                <select name="role" id="roleSelect"
                                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm @error('role') border-red-400 @enderror">
                                    <option value="">— Select role —</option>
                                    <option value="doctor" {{ old('role') === 'doctor' ? 'selected' : '' }}>Doctor</option>
                                    <option value="specialist" {{ old('role') === 'specialist' ? 'selected' : '' }}>Specialist</option>
                                </select>
                                @error('role')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Hospital <span class="text-red-500">*</span>
                                </label>
                                <select name="hospital_id"
                                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm @error('hospital_id') border-red-400 @enderror">
                                    <option value="">— Select hospital —</option>
                                    @foreach($hospitals as $hospital)
                                        <option value="{{ $hospital->id }}"
                                            {{ old('hospital_id') == $hospital->id ? 'selected' : '' }}>
                                            {{ $hospital->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('hospital_id')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Password <span class="text-red-500">*</span>
                                </label>
                                <input type="password" name="password"
                                       placeholder="Minimum 8 characters"
                                       class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm @error('password') border-red-400 @enderror">
                                @error('password')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-xs text-gray-400">
                                    Tip: use <code class="bg-gray-100 px-1 rounded">Lastname@123</code> as default
                                </p>
                            </div>

                        </div>

                        {{-- Right: conditional sections --}}
                        <div>

                            {{-- Specialization section (specialist only) --}}
                            <div id="specializationSection"
                                 class="{{ old('role') === 'specialist' ? '' : 'hidden' }} rounded-2xl border border-purple-200 bg-purple-50/60 p-5 dark:border-purple-900 dark:bg-purple-950/20">
                                <p class="text-sm font-bold text-purple-900 dark:text-purple-200">
                                    Specialist access <span class="text-red-500">*</span>
                                </p>
                                <p class="mb-3 mt-1 text-xs leading-5 text-purple-700 dark:text-purple-300">
                                    This selection controls which clinical cases the specialist can see and which notifications they receive.
                                </p>
                                <div class="space-y-2 max-h-80 overflow-y-auto pr-1">
                                    @foreach($specializations as $spec)
                                    <div class="flex items-center justify-between gap-3 rounded-xl border border-purple-100 bg-white p-3 transition hover:border-purple-300 dark:border-purple-900 dark:bg-slate-900">
                                        <div class="flex items-center gap-2">
                                            <input type="checkbox"
                                                   name="specialization_ids[]"
                                                   value="{{ $spec->id }}"
                                                   id="spec_{{ $spec->id }}"
                                                   class="rounded border-gray-300 text-blue-600"
                                                   {{ is_array(old('specialization_ids')) && in_array($spec->id, old('specialization_ids')) ? 'checked' : '' }}>
                                            <label for="spec_{{ $spec->id }}"
                                                   class="cursor-pointer text-sm font-medium text-gray-700 dark:text-gray-200">
                                                {{ $spec->name }}
                                            </label>
                                        </div>
                                        <div class="flex items-center gap-1">
                                            <input type="radio"
                                                   name="is_primary_spec"
                                                   value="{{ $spec->id }}"
                                                   id="primary_{{ $spec->id }}"
                                                   class="border-gray-300 text-green-600"
                                                   {{ old('is_primary_spec') == $spec->id ? 'checked' : '' }}>
                                            <label for="primary_{{ $spec->id }}"
                                                   class="text-xs text-green-600 font-medium cursor-pointer">
                                                ★ Primary
                                            </label>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                                @error('specialization_ids')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Doctor info --}}
                            <div id="doctorInfo"
                                 class="{{ old('role') === 'doctor' ? '' : 'hidden' }}">
                                <div class="rounded-2xl border border-blue-200 bg-blue-50 p-5 text-sm text-blue-800 dark:border-blue-900 dark:bg-blue-950/20 dark:text-blue-200">
                                    <p class="mb-1 font-semibold">Doctor account</p>
                                    <p class="text-xs leading-5 text-blue-600 dark:text-blue-300">
                                        Doctors can publish anonymized clinical cases, follow discussions and collaborate with matched specialists.
                                    </p>
                                </div>
                            </div>

                            {{-- No role selected --}}
                            <div id="noRoleInfo"
                                 class="{{ old('role') ? 'hidden' : '' }}">
                                <div class="p-4 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-500">
                                    Select a role to see additional options.
                                </div>
                            </div>

                        </div>

                    </div>

                    <div class="mt-6 pt-5 border-t border-gray-100 flex items-center gap-3">
                        <button type="submit"
                                class="px-6 py-2 bg-gray-800 hover:bg-gray-900 text-white text-sm font-medium rounded-lg transition">
                            Create User Account
                        </button>
                        <a href="{{ route('admin.users.index') }}"
                           class="px-5 py-2 border border-gray-300 text-gray-600 hover:bg-gray-50 text-sm font-medium rounded-lg transition">
                            Cancel
                        </a>
                    </div>

                </form>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('roleSelect').addEventListener('change', function () {
            const role = this.value;
            document.getElementById('specializationSection').classList.toggle('hidden', role !== 'specialist');
            document.getElementById('doctorInfo').classList.toggle('hidden', role !== 'doctor');
            document.getElementById('noRoleInfo').classList.toggle('hidden', role !== '');
        });
    </script>

</x-app-layout>

{{-- resources/views/doctor/patients/create.blade.php --}}

<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Register New Patient
            </h2>
            <a href="{{ route('doctor.patients.index') }}"
               class="text-sm text-gray-600 hover:text-gray-900 dark:text-gray-400 transition">
                &larr; Back
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">

                <form action="{{ route('doctor.patients.store') }}" method="POST" class="space-y-6">
                    @csrf

                    {{-- Personal Information --}}
                    <div>
                        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wide mb-4 pb-2 border-b border-gray-100">
                            Personal Information
                        </h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    First Name <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="first_name" value="{{ old('first_name') }}"
                                       placeholder="e.g. John"
                                       class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm @error('first_name') border-red-400 @enderror">
                                @error('first_name')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Last Name <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="last_name" value="{{ old('last_name') }}"
                                       placeholder="e.g. Doe"
                                       class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm @error('last_name') border-red-400 @enderror">
                                @error('last_name')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Date of Birth <span class="text-red-500">*</span>
                                </label>
                                <input type="date" name="date_of_birth"
                                       value="{{ old('date_of_birth') }}"
                                       max="{{ now()->subDay()->toDateString() }}"
                                       class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm @error('date_of_birth') border-red-400 @enderror">
                                @error('date_of_birth')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Gender <span class="text-red-500">*</span>
                                </label>
                                <select name="gender"
                                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm @error('gender') border-red-400 @enderror">
                                    <option value="">— Select —</option>
                                    <option value="male"   {{ old('gender') === 'male'   ? 'selected' : '' }}>Male</option>
                                    <option value="female" {{ old('gender') === 'female' ? 'selected' : '' }}>Female</option>
                                    <option value="other"  {{ old('gender') === 'other'  ? 'selected' : '' }}>Other</option>
                                </select>
                                @error('gender')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Blood Group
                                </label>
                                <select name="blood_group"
                                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                                    <option value="">— Unknown —</option>
                                    @foreach(['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $bg)
                                        <option value="{{ $bg }}" {{ old('blood_group') === $bg ? 'selected' : '' }}>
                                            {{ $bg }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    National ID
                                </label>
                                <input type="text" name="national_id" value="{{ old('national_id') }}"
                                       placeholder="Government ID number"
                                       class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm @error('national_id') border-red-400 @enderror">
                                @error('national_id')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                        </div>
                    </div>

                    {{-- Contact Information --}}
                    <div>
                        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wide mb-4 pb-2 border-b border-gray-100">
                            Contact Information
                        </h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Phone Number
                                </label>
                                <input type="text" name="phone" value="{{ old('phone') }}"
                                       placeholder="+255 xxx xxx xxx"
                                       class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm @error('phone') border-red-400 @enderror">
                                @error('phone')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Address
                                </label>
                                <input type="text" name="address" value="{{ old('address') }}"
                                       placeholder="Home or residential address"
                                       class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                            </div>

                        </div>
                    </div>

                    {{-- Emergency Contact --}}
                    <div>
                        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wide mb-4 pb-2 border-b border-gray-100">
                            Emergency Contact <span class="text-gray-400 font-normal normal-case">(optional)</span>
                        </h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Contact Name
                                </label>
                                <input type="text" name="emergency_contact_name"
                                       value="{{ old('emergency_contact_name') }}"
                                       placeholder="e.g. Mary Doe"
                                       class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Contact Phone
                                </label>
                                <input type="text" name="emergency_contact_phone"
                                       value="{{ old('emergency_contact_phone') }}"
                                       placeholder="+255 xxx xxx xxx"
                                       class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-gray-100 flex items-center gap-3">
                        <button type="submit"
                                class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition">
                            Register Patient
                        </button>
                        <a href="{{ route('doctor.patients.index') }}"
                           class="px-5 py-2 border border-gray-300 text-gray-600 hover:bg-gray-50 text-sm font-medium rounded-lg transition">
                            Cancel
                        </a>
                    </div>

                </form>
            </div>
        </div>
    </div>
</x-app-layout>
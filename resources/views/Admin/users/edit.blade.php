{{-- resources/views/admin/users/edit.blade.php --}}

<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-700 leading-tight">
                Edit User — {{ $user->name }}
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

                <form action="{{ route('admin.users.update', $user) }}" method="POST">
                    @csrf @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        <div class="space-y-5">

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Full Name <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="name" value="{{ old('name', $user->name) }}"
                                       class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm @error('name') border-red-400 @enderror">
                                @error('name')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Email Address <span class="text-red-500">*</span>
                                </label>
                                <input type="email" name="email" value="{{ old('email', $user->email) }}"
                                       class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm @error('email') border-red-400 @enderror">
                                @error('email')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Role</label>
                                <div class="w-full rounded-lg border border-gray-300 bg-gray-50 px-3 py-2 text-sm text-gray-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-400">
                                    {{ ucfirst($user->role) }}
                                </div>
                                <p class="mt-1 text-xs text-gray-400">Role cannot be changed after creation.</p>
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
                                            {{ old('hospital_id', $user->hospital_id) == $hospital->id ? 'selected' : '' }}>
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
                                    New Password
                                </label>
                                <input type="password" name="password"
                                       placeholder="Leave blank to keep current password"
                                       class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm @error('password') border-red-400 @enderror">
                                @error('password')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                        </div>

                        @if($user->role === 'specialist')
                        <div>
                            <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Specializations <span class="text-red-500">*</span>
                            </p>
                            <p class="text-xs text-gray-400 mb-3">Mark the primary one with ★</p>

                            @php
                                $assignedIds = $user->specializations->pluck('id')->toArray();
                                $primaryId   = $user->specializations->where('pivot.is_primary', true)->first()?->id;
                            @endphp

                            <div class="space-y-2 max-h-80 overflow-y-auto pr-1">
                                @foreach($specializations as $spec)
                                <div class="flex items-center justify-between gap-3 p-3 border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                                    <div class="flex items-center gap-2">
                                        <input type="checkbox"
                                               name="specialization_ids[]"
                                               value="{{ $spec->id }}"
                                               id="spec_{{ $spec->id }}"
                                               class="rounded border-gray-300 text-blue-600"
                                               {{ in_array($spec->id, old('specialization_ids', $assignedIds)) ? 'checked' : '' }}>
                                        <label for="spec_{{ $spec->id }}"
                                               class="text-sm font-medium text-gray-700 cursor-pointer">
                                            {{ $spec->name }}
                                        </label>
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <input type="radio"
                                               name="is_primary_spec"
                                               value="{{ $spec->id }}"
                                               id="primary_{{ $spec->id }}"
                                               class="border-gray-300 text-green-600"
                                               {{ old('is_primary_spec', $primaryId) == $spec->id ? 'checked' : '' }}>
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
                        @endif

                    </div>

                    <div class="mt-6 pt-5 border-t border-gray-100 flex items-center gap-3">
                        <button type="submit"
                                class="px-6 py-2 bg-gray-800 hover:bg-gray-900 text-white text-sm font-medium rounded-lg transition">
                            Update User
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
</x-app-layout>
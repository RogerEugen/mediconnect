{{-- resources/views/doctor/patients/index.blade.php --}}

<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Patients
            </h2>
            <a href="{{ route('doctor.patients.create') }}"
               class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition">
                + Register Patient
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-800 rounded-lg text-sm">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Search & Filter bar --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 mb-4">
                <form method="GET" action="{{ route('doctor.patients.index') }}"
                      class="flex flex-wrap gap-3 items-end">

                    <div class="flex-1 min-w-56">
                        <label class="block text-xs font-medium text-gray-500 mb-1">
                            Search by name, UID or phone
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                     stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>
                            <input type="text" name="search" value="{{ request('search') }}"
                                   placeholder="e.g. MC-2024-00001 or John Doe"
                                   class="w-full pl-9 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                        </div>
                    </div>

                    <div class="w-36">
                        <label class="block text-xs font-medium text-gray-500 mb-1">Gender</label>
                        <select name="gender"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                            <option value="">All</option>
                            <option value="male"   {{ request('gender') === 'male'   ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ request('gender') === 'female' ? 'selected' : '' }}>Female</option>
                            <option value="other"  {{ request('gender') === 'other'  ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>

                    <div class="flex gap-2">
                        <button type="submit"
                                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition">
                            Search
                        </button>
                        @if(request()->hasAny(['search', 'gender']))
                            <a href="{{ route('doctor.patients.index') }}"
                               class="px-4 py-2 border border-gray-300 text-gray-600 hover:bg-gray-50 text-sm font-medium rounded-lg transition">
                                Clear
                            </a>
                        @endif
                    </div>

                </form>
            </div>

            {{-- Results count --}}
            @if(request()->hasAny(['search', 'gender']))
                <p class="text-sm text-gray-500 mb-3">
                    Found <span class="font-semibold text-gray-700">{{ $patients->total() }}</span> result(s)
                    @if(request('search')) for "<em>{{ request('search') }}</em>" @endif
                </p>
            @endif

            {{-- Table --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Patient</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">UID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Age / Gender</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Blood Group</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phone</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Records</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse($patients as $patient)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full flex items-center justify-content-center text-white text-sm font-bold flex-shrink-0
                                        {{ $patient->gender === 'male' ? 'bg-blue-500' : 'bg-pink-500' }}"
                                         style="display:flex;align-items:center;justify-content:center">
                                        {{ strtoupper(substr($patient->first_name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-gray-800 dark:text-white">
                                            {{ $patient->full_name }}
                                        </p>
                                        <p class="text-xs text-gray-400">
                                            Registered {{ $patient->created_at->diffForHumans() }}
                                        </p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-xs font-mono bg-gray-100 text-gray-700 px-2 py-1 rounded">
                                    {{ $patient->patient_uid }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">
                                {{ $patient->age }} yrs /
                                <span class="{{ $patient->gender === 'male' ? 'text-blue-600' : 'text-pink-600' }} capitalize">
                                    {{ $patient->gender }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @if($patient->blood_group)
                                    <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-700">
                                        {{ $patient->blood_group }}
                                    </span>
                                @else
                                    <span class="text-gray-400 text-sm">—</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">
                                {{ $patient->phone ?? '—' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $patient->medical_records_count ?? $patient->medicalRecords->count() }} visit(s)
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex gap-1">
                                    <a href="{{ route('doctor.patients.show', $patient) }}"
                                       class="text-xs px-2.5 py-1 rounded border border-blue-300 text-blue-600 hover:bg-blue-50 transition">
                                        View
                                    </a>
                                    <a href="{{ route('doctor.patients.edit', $patient) }}"
                                       class="text-xs px-2.5 py-1 rounded border border-gray-300 text-gray-600 hover:bg-gray-50 transition">
                                        Edit
                                    </a>
                                    <a href="{{ route('doctor.patients.records', $patient) }}"
                                       class="text-xs px-2.5 py-1 rounded border border-green-300 text-green-600 hover:bg-green-50 transition">
                                        Records
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="text-gray-400">
                                    <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor"
                                         stroke-width="1" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    <p class="text-sm font-medium">No patients found</p>
                                    <p class="text-xs mt-1">
                                        @if(request()->hasAny(['search','gender']))
                                            Try a different search term.
                                        @else
                                            <a href="{{ route('doctor.patients.create') }}"
                                               class="text-blue-500 hover:underline">Register the first patient</a>
                                        @endif
                                    </p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="px-6 py-4 border-t border-gray-100">
                    {{ $patients->links() }}
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
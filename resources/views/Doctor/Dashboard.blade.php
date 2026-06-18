{{-- resources/views/doctor/dashboard.blade.php --}}

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-700 leading-tight">
            Doctor Dashboard
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Welcome --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 mb-6">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-full bg-blue-600 flex items-center justify-content-center text-white text-2xl font-bold flex-shrink-0"
                         style="display:flex;align-items:center;justify-content:center">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-white">
                            Welcome back, {{ auth()->user()->name }}
                        </h3>
                        <p class="text-sm text-gray-500">
                            {{ auth()->user()->hospital?->name ?? 'No hospital assigned' }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- Stats --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5 flex items-center gap-4">
                    <div class="bg-blue-100 text-blue-600 rounded-lg p-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">My Patients</p>
                        <p class="text-2xl font-bold text-gray-800 dark:text-white">{{ $totalPatients }}</p>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5 flex items-center gap-4">
                    <div class="bg-green-100 text-green-600 rounded-lg p-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5l2 2h3a2 2 0 012 2v13a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Medical Records</p>
                        <p class="text-2xl font-bold text-gray-800 dark:text-white">{{ $totalRecords }}</p>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5 flex items-center gap-4">
                    <div class="bg-orange-100 text-orange-600 rounded-lg p-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Open Cases</p>
                        <p class="text-2xl font-bold text-gray-800 dark:text-white">{{ $openCases }}</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- Quick actions --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
                    <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wide mb-4">
                        Quick Actions
                    </h3>
                    <div class="space-y-2">
                        <a href="{{ route('doctor.patients.index') }}"
                           class="flex items-center gap-3 p-3 rounded-lg border border-gray-200 hover:bg-blue-50 hover:border-blue-300 transition group">
                            <div class="bg-blue-100 text-blue-600 rounded-lg p-2 group-hover:bg-blue-200 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>
                            <span class="text-sm font-medium text-gray-700">Search Patients</span>
                        </a>
                        <a href="{{ route('doctor.patients.create') }}"
                           class="flex items-center gap-3 p-3 rounded-lg border border-gray-200 hover:bg-green-50 hover:border-green-300 transition group">
                            <div class="bg-green-100 text-green-600 rounded-lg p-2 group-hover:bg-green-200 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                                </svg>
                            </div>
                            <span class="text-sm font-medium text-gray-700">Register New Patient</span>
                        </a>
                    </div>

                    {{-- Add to resources/views/doctor/dashboard.blade.php quick actions --}}

                    <a href="{{ route('doctor.cases.index') }}" class="flex items-center gap-3 p-3 rounded-lg border border-gray-200 hover:bg-orange-50 hover:border-orange-300 transition group">
                        <div class="bg-orange-100 text-orange-600 rounded-lg p-2 group-hover:bg-orange-200 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                        </div>
                        <span class="text-sm font-medium text-gray-700">My Posted Cases</span>
                    </a>

                </div>

                {{-- Recent patients --}}
                <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wide">
                            Recent Patients
                        </h3>
                        <a href="{{ route('doctor.patients.index') }}"
                           class="text-xs text-blue-600 hover:underline">View all</a>
                    </div>
                    <div class="divide-y divide-gray-100">
                        @forelse($recentPatients as $patient)
                        <div class="flex items-center justify-between px-6 py-3 hover:bg-gray-50 transition">
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
                                    <p class="text-xs text-gray-400">{{ $patient->patient_uid }}</p>
                                </div>
                            </div>
                            <a href="{{ route('doctor.patients.show', $patient) }}"
                               class="text-xs px-3 py-1 rounded border border-blue-300 text-blue-600 hover:bg-blue-50 transition">
                                View
                            </a>
                        </div>
                        @empty
                        <div class="px-6 py-8 text-center text-sm text-gray-400">
                            No patients registered yet.
                        </div>
                        @endforelse
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
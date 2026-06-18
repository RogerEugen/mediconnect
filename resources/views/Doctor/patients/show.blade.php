{{-- resources/views/doctor/patients/show.blade.php --}}

<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Patient Profile
            </h2>
            <div class="flex items-center gap-2">
                <a href="{{ route('doctor.patients.edit', $patient) }}"
                   class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition">
                    Edit
                </a>
                <a href="{{ route('doctor.patients.index') }}"
                   class="text-sm text-gray-600 hover:text-gray-900 dark:text-gray-400 transition">
                    &larr; Back
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-800 rounded-lg text-sm">
                    {{ session('success') }}
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- Left: Patient info --}}
                <div class="space-y-4">

                    {{-- Identity card --}}
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
                        <div class="text-center mb-5">
                            <div class="w-20 h-20 rounded-full flex items-center justify-content-center text-white text-3xl font-bold mx-auto mb-3
                                {{ $patient->gender === 'male' ? 'bg-blue-500' : 'bg-pink-500' }}"
                                 style="display:flex;align-items:center;justify-content:center">
                                {{ strtoupper(substr($patient->first_name, 0, 1)) }}
                            </div>
                            <h3 class="text-lg font-bold text-gray-800 dark:text-white">
                                {{ $patient->full_name }}
                            </h3>
                            <p class="text-xs font-mono bg-gray-100 text-gray-600 inline-block px-2 py-0.5 rounded mt-1">
                                {{ $patient->patient_uid }}
                            </p>
                        </div>

                        <dl class="space-y-3 text-sm">
                            <div class="flex justify-between">
                                <dt class="text-gray-500">Age</dt>
                                <dd class="font-medium text-gray-800 dark:text-white">{{ $patient->age }} years</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-gray-500">Date of Birth</dt>
                                <dd class="font-medium text-gray-800 dark:text-white">
                                    {{ $patient->date_of_birth->format('d M Y') }}
                                </dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-gray-500">Gender</dt>
                                <dd class="font-medium capitalize text-gray-800 dark:text-white">
                                    {{ $patient->gender }}
                                </dd>
                            </div>
                            <div class="flex justify-between items-center">
                                <dt class="text-gray-500">Blood Group</dt>
                                <dd>
                                    @if($patient->blood_group)
                                        <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-700">
                                            {{ $patient->blood_group }}
                                        </span>
                                    @else
                                        <span class="text-gray-400">Unknown</span>
                                    @endif
                                </dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-gray-500">Phone</dt>
                                <dd class="font-medium text-gray-800 dark:text-white">{{ $patient->phone ?? '—' }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-gray-500">Address</dt>
                                <dd class="font-medium text-gray-800 dark:text-white text-right">{{ $patient->address ?? '—' }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-gray-500">National ID</dt>
                                <dd class="font-medium text-gray-800 dark:text-white">{{ $patient->national_id ?? '—' }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-gray-500">Registered by</dt>
                                <dd class="font-medium text-gray-800 dark:text-white">
                                    {{ $patient->registeredBy?->name ?? '—' }}
                                </dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-gray-500">Registered on</dt>
                                <dd class="font-medium text-gray-800 dark:text-white">
                                    {{ $patient->created_at->format('d M Y') }}
                                </dd>
                            </div>
                        </dl>
                        </div>
                        {{-- Quick actions --}}
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5 space-y-2">
                            <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Actions</h3>

                            <a href="{{ route('doctor.patients.records', $patient) }}" class="flex items-center gap-2 w-full py-2 px-3 text-sm font-medium rounded-lg border border-green-300 text-green-700 hover:bg-green-50 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5l2 2h3a2 2 0 012 2v13a2 2 0 01-2 2z" />
                                </svg>
                                View Full Medical History
                            </a>

                            <a href="{{ route('doctor.medical-records.create', $patient) }}" class="flex items-center gap-2 w-full py-2 px-3 text-sm font-medium rounded-lg border border-blue-300 text-blue-700 hover:bg-blue-50 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                                </svg>
                                Add Medical Record
                            </a>

                            <a href="{{ route('doctor.cases.create', $patient) }}" class="flex items-center gap-2 w-full py-2 px-3 text-sm font-medium rounded-lg border border-orange-300 text-orange-700 hover:bg-orange-50 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                Post Case for Discussion
                            </a>

                        </div>


                        </div>


                {{-- Right: Medical history summary --}}
                <div class="lg:col-span-2 space-y-4">

                    {{-- Stats row --}}
                    <div class="grid grid-cols-3 gap-4">
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 text-center">
                            <p class="text-2xl font-bold text-gray-800 dark:text-white">
                                {{ $patient->medicalRecords->count() }}
                            </p>
                            <p class="text-xs text-gray-500 mt-1">Total Visits</p>
                        </div>
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 text-center">
                            <p class="text-2xl font-bold text-gray-800 dark:text-white">
                                {{ $patient->cases->count() }}
                            </p>
                            <p class="text-xs text-gray-500 mt-1">Cases Posted</p>
                        </div>
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 text-center">
                            <p class="text-2xl font-bold text-gray-800 dark:text-white">
                                {{ $patient->medicalRecords->where('status','resolved')->count() }}
                            </p>
                            <p class="text-xs text-gray-500 mt-1">Resolved</p>
                        </div>
                    </div>

                    {{-- Recent medical records --}}
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wide">
                                Recent Medical Records
                            </h3>
                            <a href="{{ route('doctor.patients.records', $patient) }}"
                               class="text-xs text-blue-600 hover:underline">
                                View all
                            </a>
                        </div>

                        @forelse($patient->medicalRecords->take(4) as $record)
                        <div class="px-6 py-4 border-b border-gray-50 hover:bg-gray-50 transition">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="text-sm font-semibold text-gray-800 dark:text-white">
                                            {{ $record->diagnosis }}
                                        </span>
                                        <span class="px-2 py-0.5 rounded-full text-xs font-medium
                                            @if($record->status === 'resolved') bg-green-100 text-green-800
                                            @elseif($record->status === 'active')  bg-blue-100 text-blue-800
                                            @else bg-yellow-100 text-yellow-800 @endif">
                                            {{ ucfirst($record->status) }}
                                        </span>
                                    </div>
                                    <p class="text-xs text-gray-500">
                                        {{ $record->visit_date->format('d M Y') }} •
                                        {{ $record->hospital?->name }} •
                                        Dr. {{ $record->doctor?->name }}
                                    </p>
                                    <p class="text-xs text-gray-400 mt-1 line-clamp-1">
                                        {{ $record->symptoms }}
                                    </p>
                                </div>
                                <span class="ml-4 px-2 py-0.5 rounded text-xs bg-gray-100 text-gray-600 capitalize flex-shrink-0">
                                    {{ str_replace('_', ' ', $record->visit_type) }}
                                </span>
                            </div>
                        </div>
                        @empty
                        <div class="px-6 py-8 text-center text-sm text-gray-400">
                            No medical records yet.
                            <a href="{{ route('doctor.medical-records.create', ['patient' => $patient->id]) }}"
                               class="text-blue-500 hover:underline ml-1">Add the first record</a>
                        </div>
                        @endforelse
                    </div>

                </div>

            </div>
        </div>
    </div>
</x-app-layout>
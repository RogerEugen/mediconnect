{{-- resources/views/doctor/patients/records.blade.php --}}

<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Medical History — {{ $patient->full_name }}
                </h2>
                <p class="text-sm text-gray-500 mt-0.5 font-mono">{{ $patient->patient_uid }}</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('doctor.medical-records.create', $patient) }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition">
                    + Add Record
                </a>

                <a href="{{ route('doctor.patients.show', $patient) }}"
                   class="text-sm text-gray-600 hover:text-gray-900 dark:text-gray-400 transition">
                    &larr; Back
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Patient summary strip --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 mb-6 flex flex-wrap gap-6 items-center">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full flex items-center justify-content-center text-white font-bold
                        {{ $patient->gender === 'male' ? 'bg-blue-500' : 'bg-pink-500' }}"
                         style="display:flex;align-items:center;justify-content:center">
                        {{ strtoupper(substr($patient->first_name, 0, 1)) }}
                    </div>
                    <div>
                        <p class="text-sm font-bold text-gray-800 dark:text-white">{{ $patient->full_name }}</p>
                        <p class="text-xs text-gray-400">{{ $patient->age }} yrs • {{ ucfirst($patient->gender) }}</p>
                    </div>
                </div>
                @if($patient->blood_group)
                    <span class="px-3 py-1 rounded-full text-sm font-bold bg-red-100 text-red-700">
                        {{ $patient->blood_group }}
                    </span>
                @endif
                <div class="text-sm text-gray-500">
                    <span class="font-semibold text-gray-800 dark:text-white">{{ $patient->medicalRecords->count() }}</span>
                    total visit(s)
                </div>
            </div>

            {{-- Timeline --}}
            @forelse($patient->medicalRecords as $record)
            <div class="relative pl-8 pb-6">
                {{-- Timeline line --}}
                <div class="absolute left-3 top-4 bottom-0 w-px bg-gray-200 dark:bg-gray-700
                    {{ $loop->last ? 'hidden' : '' }}"></div>
                {{-- Timeline dot --}}
                <div class="absolute left-0 top-4 w-6 h-6 rounded-full border-2 flex items-center justify-content-center
                    @if($record->status === 'resolved') border-green-500 bg-green-100
                    @elseif($record->status === 'active') border-blue-500 bg-blue-100
                    @else border-yellow-500 bg-yellow-100 @endif"
                     style="display:flex;align-items:center;justify-content:center">
                    <div class="w-2 h-2 rounded-full
                        @if($record->status === 'resolved') bg-green-500
                        @elseif($record->status === 'active') bg-blue-500
                        @else bg-yellow-500 @endif"></div>
                </div>

                {{-- Record card --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-100 flex justify-between items-start">
                        <div>
                            <div class="flex items-center gap-2 flex-wrap">
                                <h4 class="text-base font-semibold text-gray-800 dark:text-white">
                                    {{ $record->diagnosis }}
                                </h4>
                                <span class="px-2 py-0.5 rounded-full text-xs font-medium
                                    @if($record->status === 'resolved') bg-green-100 text-green-800
                                    @elseif($record->status === 'active')  bg-blue-100 text-blue-800
                                    @else bg-yellow-100 text-yellow-800 @endif">
                                    {{ ucfirst($record->status) }}
                                </span>
                                <span class="px-2 py-0.5 rounded text-xs bg-gray-100 text-gray-600 capitalize">
                                    {{ str_replace('_', ' ', $record->visit_type) }}
                                </span>
                            </div>
                            <p class="text-xs text-gray-400 mt-1">
                                {{ $record->visit_date->format('d M Y') }} •
                                {{ $record->hospital?->name }} •
                                Dr. {{ $record->doctor?->name }}
                            </p>
                        </div>
                    </div>

                    <div class="px-5 py-4 grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase mb-1">Symptoms</p>
                            <p class="text-gray-700 dark:text-gray-300">{{ $record->symptoms }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase mb-1">Treatment Plan</p>
                            <p class="text-gray-700 dark:text-gray-300">{{ $record->treatment_plan }}</p>
                        </div>
                        @if($record->prescription)
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase mb-1">Prescription</p>
                            <p class="text-gray-700 dark:text-gray-300">{{ $record->prescription }}</p>
                        </div>
                        @endif
                        @if($record->notes)
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase mb-1">Notes</p>
                            <p class="text-gray-700 dark:text-gray-300">{{ $record->notes }}</p>
                        </div>
                        @endif
                    </div>

                    @if($record->attachments->count())
                    <div class="px-5 pb-4">
                        <p class="text-xs font-semibold text-gray-500 uppercase mb-2">Attachments</p>
                        <div class="flex flex-wrap gap-2">
                            @foreach($record->attachments as $file)
                                <a href="{{ Storage::url($file->file_path) }}" target="_blank"
                                   class="flex items-center gap-1.5 px-3 py-1.5 text-xs bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                                    </svg>
                                    {{ $file->file_name }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @empty
            <div class="text-center py-16">
                <svg class="w-16 h-16 mx-auto text-gray-200 mb-4" fill="none" stroke="currentColor"
                     stroke-width="1" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5l2 2h3a2 2 0 012 2v13a2 2 0 01-2 2z"/>
                </svg>
                <p class="text-gray-500 font-medium">No medical records yet</p>
               <a href="{{ route('doctor.medical-records.create', $patient) }}" class="mt-3 inline-block text-sm text-blue-600 hover:underline">
                   Add the first medical record
               </a>
               </div>

            @endforelse

        </div>
    </div>
</x-app-layout>
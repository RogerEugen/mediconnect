{{-- resources/views/doctor/medical-records/show.blade.php --}}

<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Medical Record
                </h2>
                <p class="text-sm text-gray-500 mt-0.5">
                    {{ $record->patient->full_name }}
                    <span class="font-mono text-xs text-gray-400 ml-1">{{ $record->patient->patient_uid }}</span>
                </p>
            </div>
            <div class="flex items-center gap-2">
                @if(auth()->id() === $record->doctor_id)
                    <a href="{{ route('doctor.medical-records.edit', $record) }}"
                       class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition">
                        Edit
                    </a>
                @endif
                <a href="{{ route('doctor.patients.records', $record->patient) }}"
                   class="text-sm text-gray-600 hover:text-gray-900 dark:text-gray-400 transition">
                    &larr; Back
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-800 rounded-lg text-sm">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Header strip --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5 mb-5 flex flex-wrap gap-4 items-center justify-between">
                <div class="flex flex-wrap gap-3 items-center">
                    <span class="text-sm font-semibold text-gray-700 dark:text-white">
                        {{ $record->visit_date->format('d M Y') }}
                    </span>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700">
                        {{ $record->visit_type_label }}
                    </span>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-medium
                        @if($record->status === 'resolved') bg-green-100 text-green-800
                        @elseif($record->status === 'active') bg-blue-100 text-blue-800
                        @else bg-yellow-100 text-yellow-800 @endif">
                        {{ ucfirst($record->status) }}
                    </span>
                    <span class="text-xs text-gray-400">
                        {{ $record->hospital?->name }}
                    </span>
                </div>
                <div class="text-xs text-gray-400">
                    By Dr. {{ $record->doctor?->name }}
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

                {{-- Main clinical content --}}
                <div class="lg:col-span-2 space-y-5">

                    {{-- Symptoms --}}
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5">
                        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Symptoms</h3>
                        <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed">
                            {{ $record->symptoms }}
                        </p>
                    </div>

                    {{-- Diagnosis --}}
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5 border-l-4 border-blue-500">
                        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Diagnosis</h3>
                        <p class="text-sm font-semibold text-gray-800 dark:text-white leading-relaxed">
                            {{ $record->diagnosis }}
                        </p>
                    </div>

                    {{-- Treatment Plan --}}
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5">
                        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Treatment Plan</h3>
                        <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed">
                            {{ $record->treatment_plan }}
                        </p>
                    </div>

                    @if($record->prescription)
                    {{-- Prescription --}}
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5 border-l-4 border-green-500">
                        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Prescription</h3>
                        <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed whitespace-pre-line">
                            {{ $record->prescription }}
                        </p>
                    </div>
                    @endif

                    @if($record->notes)
                    {{-- Notes --}}
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5">
                        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Notes</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 leading-relaxed">
                            {{ $record->notes }}
                        </p>
                    </div>
                    @endif

                </div>

                {{-- Right sidebar --}}
                <div class="space-y-5">

                    {{-- Patient quick info --}}
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5">
                        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Patient</h3>
                        <div class="flex items-center gap-3 mb-3">
                            <div class="w-10 h-10 rounded-full flex items-center justify-content-center text-white font-bold text-sm flex-shrink-0
                                {{ $record->patient->gender === 'male' ? 'bg-blue-500' : 'bg-pink-500' }}"
                                 style="display:flex;align-items:center;justify-content:center">
                                {{ strtoupper(substr($record->patient->first_name, 0, 1)) }}
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-800 dark:text-white">
                                    {{ $record->patient->full_name }}
                                </p>
                                <p class="text-xs font-mono text-gray-400">{{ $record->patient->patient_uid }}</p>
                            </div>
                        </div>
                        <dl class="space-y-2 text-xs">
                            <div class="flex justify-between">
                                <dt class="text-gray-400">Age</dt>
                                <dd class="font-medium text-gray-700 dark:text-gray-300">{{ $record->patient->age }} yrs</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-gray-400">Blood group</dt>
                                <dd class="font-bold text-red-600">{{ $record->patient->blood_group ?? '—' }}</dd>
                            </div>
                        </dl>
                        <a href="{{ route('doctor.patients.show', $record->patient) }}"
                           class="mt-3 block text-center text-xs text-blue-600 hover:underline">
                            View full profile
                        </a>
                    </div>

                    {{-- Attachments --}}
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5">
                        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">
                            Attachments ({{ $record->attachments->count() }})
                        </h3>

                        @forelse($record->attachments as $attachment)
                        <div class="flex items-start justify-between gap-2 p-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition mb-1">
                            <div class="flex items-start gap-2 min-w-0">
                                {{-- Icon by type --}}
                                <div class="flex-shrink-0 mt-0.5">
                                    @if($attachment->is_image)
                                        <div class="w-6 h-6 bg-blue-100 rounded flex items-center justify-content-center"
                                             style="display:flex;align-items:center;justify-content:center">
                                            <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor"
                                                 stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                      d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                        </div>
                                    @elseif($attachment->is_pdf)
                                        <div class="w-6 h-6 bg-red-100 rounded flex items-center justify-content-center"
                                             style="display:flex;align-items:center;justify-content:center">
                                            <svg class="w-3.5 h-3.5 text-red-600" fill="none" stroke="currentColor"
                                                 stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                      d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                            </svg>
                                        </div>
                                    @else
                                        <div class="w-6 h-6 bg-gray-100 rounded flex items-center justify-content-center"
                                             style="display:flex;align-items:center;justify-content:center">
                                            <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor"
                                                 stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                      d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    <a href="{{ $attachment->url }}" target="_blank"
                                       class="text-xs font-medium text-blue-600 hover:underline block truncate">
                                        {{ $attachment->description ?: $attachment->file_name }}
                                    </a>
                                    <p class="text-xs text-gray-400">{{ $attachment->file_size_formatted }}</p>
                                </div>
                            </div>
                            @if(auth()->id() === $record->doctor_id)
                            {{-- we remove the {{ route('doctor.attachments.destroy', $attachment) }} for a specific time okay  --}}
                            <form action=""
                                  method="POST"
                                  onsubmit="return confirm('Remove this attachment?')">
                                @csrf @method('DELETE')
                                <button class="text-gray-300 hover:text-red-500 transition flex-shrink-0 text-sm">✕</button>
                            </form>
                            @endif
                        </div>
                        @empty
                        <p class="text-xs text-gray-400">No attachments.</p>
                        @endforelse
                    </div>

                    {{-- Danger zone --}}
                    @if(auth()->id() === $record->doctor_id)
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5 border border-red-100">
                        <h3 class="text-xs font-semibold text-red-400 uppercase tracking-wide mb-3">Danger Zone</h3>
                        <form action="{{ route('doctor.medical-records.destroy', $record) }}"
                              method="POST"
                              onsubmit="return confirm('Permanently delete this medical record and all its attachments?')">
                            @csrf @method('DELETE')
                            <button class="w-full py-2 text-xs font-medium rounded-lg border border-red-300 text-red-600 hover:bg-red-50 transition">
                                Delete This Record
                            </button>
                        </form>
                    </div>
                    @endif

                </div>

            </div>
        </div>
    </div>
</x-app-layout>
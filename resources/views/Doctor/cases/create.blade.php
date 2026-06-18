{{-- resources/views/doctor/cases/create.blade.php --}}

<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Post Case for Discussion
                </h2>
                <p class="text-sm text-gray-500 mt-0.5">
                    Patient:
                    <span class="font-semibold text-gray-700 dark:text-gray-300">{{ $patient->full_name }}</span>
                    <span class="font-mono text-xs ml-1 text-gray-400">{{ $patient->patient_uid }}</span>
                </p>
            </div>
            <a href="{{ route('doctor.patients.show', $patient) }}"
               class="text-sm text-gray-600 hover:text-gray-900 dark:text-gray-400 transition">
                &larr; Back
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Info banner --}}
            <div class="mb-5 p-4 bg-orange-50 border border-orange-200 rounded-xl flex gap-3">
                <svg class="w-5 h-5 text-orange-500 flex-shrink-0 mt-0.5" fill="none"
                     stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-sm text-orange-700">
                    Post this case when you need specialist input on a complex or unclear diagnosis.
                    An admin will review your case and assign the appropriate specialist.
                    Be as detailed as possible.
                </p>
            </div>

            <form action="{{ route('doctor.cases.store', $patient) }}"
                  method="POST" class="space-y-6">
                @csrf

                {{-- Case Overview --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
                    <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wide mb-5 pb-2 border-b border-gray-100">
                        Case Overview
                    </h3>
                    <div class="space-y-5">

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Case Title <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="title" value="{{ old('title') }}"
                                   placeholder="Brief title e.g. Recurring chest pain with unclear ECG findings"
                                   class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm @error('title') border-red-400 @enderror">
                            @error('title')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Urgency Level <span class="text-red-500">*</span>
                                </label>
                                <select name="urgency"
                                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm @error('urgency') border-red-400 @enderror">
                                    <option value="">— Select urgency —</option>
                                    <option value="low"      {{ old('urgency') === 'low'      ? 'selected' : '' }}>🟢 Low — not time-sensitive</option>
                                    <option value="medium"   {{ old('urgency') === 'medium'   ? 'selected' : '' }}>🟡 Medium — needs attention soon</option>
                                    <option value="high"     {{ old('urgency') === 'high'     ? 'selected' : '' }}>🟠 High — within 24 hours</option>
                                    <option value="critical" {{ old('urgency') === 'critical' ? 'selected' : '' }}>🔴 Critical — immediate action needed</option>
                                </select>
                                @error('urgency')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Specialization Needed <span class="text-red-500">*</span>
                                </label>
                                <select name="specialization_id"
                                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm @error('specialization_id') border-red-400 @enderror">
                                    <option value="">— Select specialization —</option>
                                    @foreach($specializations as $spec)
                                        <option value="{{ $spec->id }}"
                                            {{ old('specialization_id') == $spec->id ? 'selected' : '' }}>
                                            {{ $spec->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('specialization_id')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                        </div>

                        @if($medicalRecords->count())
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Link to Medical Record
                                <span class="text-gray-400 font-normal">(optional — helps specialist review the visit)</span>
                            </label>
                            <select name="medical_record_id"
                                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                                <option value="">— No specific record —</option>
                                @foreach($medicalRecords as $rec)
                                    <option value="{{ $rec->id }}"
                                        {{ old('medical_record_id') == $rec->id ? 'selected' : '' }}>
                                        {{ $rec->visit_date->format('d M Y') }} — {{ $rec->diagnosis }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @endif

                    </div>
                </div>

                {{-- Clinical Details --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
                    <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wide mb-5 pb-2 border-b border-gray-100">
                        Clinical Details
                    </h3>
                    <div class="space-y-5">

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Current Symptoms <span class="text-red-500">*</span>
                            </label>
                            <textarea name="symptoms" rows="3"
                                      placeholder="Describe all current symptoms in detail..."
                                      class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm @error('symptoms') border-red-400 @enderror">{{ old('symptoms') }}</textarea>
                            @error('symptoms')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Full Case Description <span class="text-red-500">*</span>
                                <span class="text-gray-400 font-normal">(min. 50 characters)</span>
                            </label>
                            <textarea name="description" rows="5" id="descriptionField"
                                      placeholder="Provide a complete clinical description — patient history, test results, what makes this case complex or unclear..."
                                      class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm @error('description') border-red-400 @enderror">{{ old('description') }}</textarea>
                            <p class="mt-1 text-xs text-gray-400">
                                <span id="charCount">0</span> characters
                                <span class="text-gray-300 mx-1">•</span>
                                minimum 50 required
                            </p>
                            @error('description')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Prior Treatments Tried
                                <span class="text-gray-400 font-normal">(optional but recommended)</span>
                            </label>
                            <textarea name="prior_treatments" rows="3"
                                      placeholder="What treatments have already been attempted? What worked or didn't work?"
                                      class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">{{ old('prior_treatments') }}</textarea>
                        </div>

                    </div>
                </div>

                {{-- Submit --}}
                <div class="flex items-center gap-3">
                    <button type="submit"
                            class="px-6 py-2.5 bg-orange-600 hover:bg-orange-700 text-white text-sm font-medium rounded-lg transition">
                        Post Case for Specialist Review
                    </button>
                    <a href="{{ route('doctor.patients.show', $patient) }}"
                       class="px-5 py-2.5 border border-gray-300 text-gray-600 hover:bg-gray-50 text-sm font-medium rounded-lg transition">
                        Cancel
                    </a>
                </div>

            </form>
        </div>
    </div>

    <script>
        const field = document.getElementById('descriptionField');
        const counter = document.getElementById('charCount');
        function updateCount() {
            const len = field.value.length;
            counter.textContent = len;
            counter.className = len >= 50 ? 'text-green-600 font-medium' : 'text-gray-400';
        }
        field.addEventListener('input', updateCount);
        updateCount();
    </script>
</x-app-layout>
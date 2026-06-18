{{-- resources/views/doctor/medical-records/edit.blade.php --}}

<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Edit Medical Record
                </h2>
                <p class="text-sm text-gray-500 mt-0.5">
                    {{ $record->patient->full_name }}
                    <span class="font-mono text-xs text-gray-400 ml-1">{{ $record->patient->patient_uid }}</span>
                </p>
            </div>
            <a href="{{ route('doctor.medical-records.show', $record) }}"
               class="text-sm text-gray-600 hover:text-gray-900 dark:text-gray-400 transition">
                &larr; Back
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

            @if(session('error'))
                <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-800 rounded-lg text-sm">
                    {{ session('error') }}
                </div>
            @endif

            <form action="{{ route('doctor.medical-records.update', $record) }}"
                  method="POST"
                  enctype="multipart/form-data"
                  class="space-y-6">
                @csrf @method('PUT')

                {{-- Visit Info --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
                    <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wide mb-5 pb-2 border-b border-gray-100">
                        Visit Information
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Visit Date <span class="text-red-500">*</span>
                            </label>
                            <input type="date" name="visit_date"
                                   value="{{ old('visit_date', $record->visit_date->toDateString()) }}"
                                   max="{{ now()->toDateString() }}"
                                   class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm @error('visit_date') border-red-400 @enderror">
                            @error('visit_date')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Visit Type <span class="text-red-500">*</span>
                            </label>
                            <select name="visit_type"
                                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm @error('visit_type') border-red-400 @enderror">
                                @foreach(['outpatient','inpatient','emergency','follow_up'] as $type)
                                    <option value="{{ $type }}"
                                        {{ old('visit_type', $record->visit_type) === $type ? 'selected' : '' }}>
                                        {{ ucfirst(str_replace('_', '-', $type)) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('visit_type')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Hospital <span class="text-red-500">*</span>
                            </label>
                            <select name="hospital_id"
                                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm @error('hospital_id') border-red-400 @enderror">
                                @foreach($hospitals as $hospital)
                                    <option value="{{ $hospital->id }}"
                                        {{ old('hospital_id', $record->hospital_id) == $hospital->id ? 'selected' : '' }}>
                                        {{ $hospital->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('hospital_id')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

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
                                Symptoms <span class="text-red-500">*</span>
                            </label>
                            <textarea name="symptoms" rows="3"
                                      class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm @error('symptoms') border-red-400 @enderror">{{ old('symptoms', $record->symptoms) }}</textarea>
                            @error('symptoms')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Diagnosis <span class="text-red-500">*</span>
                            </label>
                            <textarea name="diagnosis" rows="2"
                                      class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm @error('diagnosis') border-red-400 @enderror">{{ old('diagnosis', $record->diagnosis) }}</textarea>
                            @error('diagnosis')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Treatment Plan <span class="text-red-500">*</span>
                            </label>
                            <textarea name="treatment_plan" rows="3"
                                      class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm @error('treatment_plan') border-red-400 @enderror">{{ old('treatment_plan', $record->treatment_plan) }}</textarea>
                            @error('treatment_plan')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Prescription
                                </label>
                                <textarea name="prescription" rows="3"
                                          class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">{{ old('prescription', $record->prescription) }}</textarea>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Additional Notes
                                </label>
                                <textarea name="notes" rows="3"
                                          class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">{{ old('notes', $record->notes) }}</textarea>
                            </div>
                        </div>

                        <div class="w-48">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Status <span class="text-red-500">*</span>
                            </label>
                            <select name="status"
                                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                                <option value="active"   {{ old('status', $record->status) === 'active'   ? 'selected' : '' }}>Active</option>
                                <option value="pending"  {{ old('status', $record->status) === 'pending'  ? 'selected' : '' }}>Pending results</option>
                                <option value="resolved" {{ old('status', $record->status) === 'resolved' ? 'selected' : '' }}>Resolved</option>
                            </select>
                        </div>

                    </div>
                </div>

                {{-- Existing Attachments --}}
                @if($record->attachments->count())
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
                    <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wide mb-4 pb-2 border-b border-gray-100">
                        Existing Attachments
                    </h3>
                    <div class="space-y-2">
                        @foreach($record->attachments as $attachment)
                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div class="flex items-center gap-3 min-w-0">
                                <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor"
                                     stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                                </svg>
                                <div class="min-w-0">
                                    <a href="{{ $attachment->url }}" target="_blank"
                                       class="text-sm text-blue-600 hover:underline truncate block">
                                        {{ $attachment->description ?: $attachment->file_name }}
                                    </a>
                                    <p class="text-xs text-gray-400">{{ $attachment->file_size_formatted }}</p>
                                </div>
                            </div>
                            <form action="{{ route('doctor.attachments.destroy', $attachment) }}"
                                  method="POST"
                                  onsubmit="return confirm('Remove this file?')">
                                @csrf @method('DELETE')
                                <button class="text-xs text-red-500 hover:text-red-700 transition">Remove</button>
                            </form>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Add New Attachments --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
                    <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wide mb-2 pb-2 border-b border-gray-100">
                        Add More Attachments
                        <span class="text-gray-400 font-normal normal-case ml-1">(optional)</span>
                    </h3>
                    <p class="text-xs text-gray-400 mb-4">
                        JPG, PNG, GIF, PDF, DOC, DOCX — max 10MB each
                    </p>
                    <div id="attachmentsList" class="space-y-3">
                        <div class="attachment-row flex gap-3 items-start">
                            <div class="flex-1">
                                <input type="file" name="attachments[]"
                                       accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx"
                                       class="w-full text-sm text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 transition">
                            </div>
                            <div class="w-64">
                                <input type="text" name="attachment_desc[]"
                                       placeholder="Description e.g. Follow-up scan"
                                       class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                            </div>
                            <button type="button" class="remove-attachment mt-1 text-gray-300 hover:text-red-500 transition text-lg"
                                    style="display:none">✕</button>
                        </div>
                    </div>
                    <button type="button" id="addAttachment"
                            class="mt-3 flex items-center gap-1.5 text-xs text-blue-600 hover:text-blue-800 font-medium transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                        </svg>
                        Add another file
                    </button>
                </div>

                {{-- Submit --}}
                <div class="flex items-center gap-3">
                    <button type="submit"
                            class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition">
                        Update Medical Record
                    </button>
                    <a href="{{ route('doctor.medical-records.show', $record) }}"
                       class="px-5 py-2.5 border border-gray-300 text-gray-600 hover:bg-gray-50 text-sm font-medium rounded-lg transition">
                        Cancel
                    </a>
                </div>

            </form>
        </div>
    </div>

    <script>
        const list = document.getElementById('attachmentsList');
        const addBtn = document.getElementById('addAttachment');
        let count = 1;

        function updateRemoveButtons() {
            list.querySelectorAll('.attachment-row').forEach((row, i, rows) => {
                row.querySelector('.remove-attachment').style.display =
                    rows.length > 1 ? 'block' : 'none';
            });
        }

        addBtn.addEventListener('click', () => {
            if (count >= 10) return;
            count++;
            const row = document.createElement('div');
            row.className = 'attachment-row flex gap-3 items-start';
            row.innerHTML = `
                <div class="flex-1">
                    <input type="file" name="attachments[]"
                           accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx"
                           class="w-full text-sm text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 transition">
                </div>
                <div class="w-64">
                    <input type="text" name="attachment_desc[]" placeholder="Description"
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                </div>
                <button type="button" class="remove-attachment mt-1 text-gray-300 hover:text-red-500 transition text-lg">✕</button>
            `;
            list.appendChild(row);
            updateRemoveButtons();
        });

        list.addEventListener('click', (e) => {
            if (e.target.classList.contains('remove-attachment')) {
                e.target.closest('.attachment-row').remove();
                count--;
                updateRemoveButtons();
            }
        });
    </script>
</x-app-layout>
{{-- resources/views/admin/cases/assign.blade.php --}}

<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Assign Specialist — {{ $case->case_number }}
                </h2>
                <p class="text-sm text-gray-500 mt-0.5">{{ $case->title }}</p>
            </div>
            <a href="{{ route('admin.cases.show', $case) }}"
               class="text-sm text-gray-600 hover:text-gray-900 dark:text-gray-400 transition">
                &larr; Back
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Case summary --}}
            @php
                $urgencyClasses = ['low'=>'bg-green-100 text-green-800','medium'=>'bg-yellow-100 text-yellow-800','high'=>'bg-orange-100 text-orange-800','critical'=>'bg-red-100 text-red-800'];
            @endphp
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5 mb-6">
                <div class="flex flex-wrap gap-3 items-start justify-between">
                    <div>
                        <p class="font-mono text-sm font-bold text-gray-700 dark:text-white">{{ $case->case_number }}</p>
                        <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">{{ $case->title }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">
                            Patient: <span class="font-semibold">{{ $case->patient->full_name }}</span>
                            • Specialization needed: <span class="font-semibold text-purple-700">{{ $case->specialization?->name }}</span>
                        </p>
                    </div>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-medium {{ $urgencyClasses[$case->urgency] ?? '' }}">
                        {{ ucfirst($case->urgency) }} urgency
                    </span>
                </div>
            </div>

            <form action="{{ route('admin.cases.assign.store', $case) }}" method="POST">
                @csrf

                {{-- Specialists list --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden mb-5">
                    <div class="px-6 py-4 border-b border-gray-100">
                        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200">
                            Available Specialists — {{ $case->specialization?->name }}
                        </h3>
                        <p class="text-xs text-gray-400 mt-0.5">
                            Sorted by current workload (fewest active cases first)
                        </p>
                    </div>

                    @forelse($specialists as $specialist)
                    <label class="flex items-center gap-4 px-6 py-4 border-b border-gray-50 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer transition
                        @error('specialist_id') @enderror">
                        <input type="radio" name="specialist_id" value="{{ $specialist->id }}"
                               class="border-gray-300 text-purple-600 focus:ring-purple-500"
                               {{ old('specialist_id') == $specialist->id ? 'checked' : '' }}
                               required>
                        <div class="w-10 h-10 rounded-full bg-purple-600 flex items-center justify-content-center text-white font-bold text-sm flex-shrink-0"
                             style="display:flex;align-items:center;justify-content:center">
                            {{ strtoupper(substr($specialist->name, 0, 1)) }}
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-semibold text-gray-800 dark:text-white">
                                {{ $specialist->name }}
                            </p>
                            <p class="text-xs text-gray-400">
                                {{ $specialist->hospital?->name }}
                                @if($specialist->specializations->count())
                                    • {{ $specialist->specializations->pluck('name')->join(', ') }}
                                @endif
                            </p>
                        </div>
                        <div class="text-right">
                            <p class="text-lg font-bold {{ $specialist->active_cases_count > 3 ? 'text-orange-600' : 'text-green-600' }}">
                                {{ $specialist->active_cases_count }}
                            </p>
                            <p class="text-xs text-gray-400">active cases</p>
                        </div>
                    </label>
                    @empty
                    <div class="px-6 py-10 text-center text-sm text-gray-400">
                        No active specialists found for
                        <span class="font-semibold text-gray-600">{{ $case->specialization?->name }}</span>.
                        <br>
                        <a href="{{ route('admin.users.create') }}"
                           class="mt-2 inline-block text-blue-600 hover:underline text-xs">
                            Add a specialist with this specialization
                        </a>
                    </div>
                    @endforelse
                </div>

                @error('specialist_id')
                    <p class="mb-3 text-xs text-red-600">{{ $message }}</p>
                @enderror

                {{-- Assignment options --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 mb-5">
                    <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200 mb-4">
                        Assignment Options
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Response Due Date
                                <span class="text-gray-400 font-normal">(optional)</span>
                            </label>
                            <input type="date" name="due_date"
                                   value="{{ old('due_date') }}"
                                   min="{{ now()->addDay()->toDateString() }}"
                                   class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                            <p class="mt-1 text-xs text-gray-400">
                                Suggested:
                                @if($case->urgency === 'critical') today
                                @elseif($case->urgency === 'high') within 24 hours
                                @elseif($case->urgency === 'medium') within 3 days
                                @else within 1 week @endif
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Note to Specialist
                                <span class="text-gray-400 font-normal">(optional)</span>
                            </label>
                            <textarea name="notes" rows="3"
                                      placeholder="Any specific instructions for the specialist..."
                                      class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <button type="submit"
                            class="px-6 py-2.5 bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium rounded-lg transition">
                        Assign Specialist & Notify
                    </button>
                    <a href="{{ route('admin.cases.show', $case) }}"
                       class="px-5 py-2.5 border border-gray-300 text-gray-600 hover:bg-gray-50 text-sm font-medium rounded-lg transition">
                        Cancel
                    </a>
                </div>

            </form>

        </div>
    </div>
</x-app-layout>
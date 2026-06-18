{{-- resources/views/admin/cases/show.blade.php --}}

<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Case: {{ $case->case_number }}
                </h2>
                <p class="text-sm text-gray-500 mt-0.5">{{ $case->title }}</p>
            </div>
            <div class="flex items-center gap-2">
                @if(in_array($case->status, ['open', 'assigned']))
                <a href="{{ route('admin.cases.assign', $case) }}"
                   class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium rounded-lg transition">
                    {{ $case->status === 'open' ? 'Assign Specialist' : 'Reassign' }}
                </a>
                @endif
                <a href="{{ route('admin.cases.index') }}"
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
            @if(session('error'))
                <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-800 rounded-lg text-sm">
                    {{ session('error') }}
                </div>
            @endif

            @php
                $urgencyClasses = ['low'=>'bg-green-100 text-green-800','medium'=>'bg-yellow-100 text-yellow-800','high'=>'bg-orange-100 text-orange-800','critical'=>'bg-red-100 text-red-800'];
                $statusClasses  = ['open'=>'bg-blue-100 text-blue-800','assigned'=>'bg-purple-100 text-purple-800','in_discussion'=>'bg-orange-100 text-orange-800','resolved'=>'bg-green-100 text-green-800','closed'=>'bg-gray-100 text-gray-600'];
            @endphp

            {{-- Status strip --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5 mb-5 flex flex-wrap gap-3 items-center justify-between">
                <div class="flex flex-wrap gap-2 items-center">
                    <span class="font-mono text-sm font-bold text-gray-700 dark:text-white">{{ $case->case_number }}</span>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusClasses[$case->status] ?? '' }}">
                        {{ $case->status_label }}
                    </span>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-medium {{ $urgencyClasses[$case->urgency] ?? '' }}">
                        {{ ucfirst($case->urgency) }} urgency
                    </span>
                    <span class="text-xs text-gray-400">{{ $case->specialization?->name }}</span>
                </div>
                <div class="flex gap-2">
                    @if($case->status === 'assigned' || $case->status === 'in_discussion')
                    <button onclick="document.getElementById('resolveModal').classList.remove('hidden')"
                            class="px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white text-xs font-medium rounded-lg transition">
                        Mark Resolved
                    </button>
                    @endif
                    @if(!in_array($case->status, ['closed']))
                    <form action="{{ route('admin.cases.close', $case) }}" method="POST">
                        @csrf @method('PATCH')
                        <button class="px-3 py-1.5 border border-gray-300 text-gray-600 hover:bg-gray-50 text-xs font-medium rounded-lg transition"
                                onclick="return confirm('Close this case?')">
                            Close Case
                        </button>
                    </form>
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

                {{-- Main --}}
                <div class="lg:col-span-2 space-y-5">

                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5">
                        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Symptoms</h3>
                        <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed">{{ $case->symptoms }}</p>
                    </div>

                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5">
                        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Full Description</h3>
                        <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed whitespace-pre-line">{{ $case->description }}</p>
                    </div>

                    @if($case->prior_treatments)
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5">
                        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Prior Treatments</h3>
                        <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed">{{ $case->prior_treatments }}</p>
                    </div>
                    @endif

                    @if($case->resolution_notes)
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5 border-l-4 border-green-500">
                        <h3 class="text-xs font-semibold text-green-600 uppercase tracking-wide mb-3">Resolution Notes</h3>
                        <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed">{{ $case->resolution_notes }}</p>
                    </div>
                    @endif

                    {{-- Assignment history --}}
                    @if($case->assignments->count())
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
                        <div class="px-5 py-4 border-b border-gray-100">
                            <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide">
                                Assignment History
                            </h3>
                        </div>
                        <div class="divide-y divide-gray-50">
                            @foreach($case->assignments as $assignment)
                            <div class="px-5 py-3 text-sm flex justify-between items-start">
                                <div>
                                    <p class="font-medium text-gray-800 dark:text-white">
                                        {{ $assignment->specialist->name }}
                                    </p>
                                    <p class="text-xs text-gray-400">
                                        Assigned by {{ $assignment->assignedBy->name }} •
                                        {{ $assignment->created_at->format('d M Y') }}
                                        @if($assignment->due_date)
                                            • Due {{ $assignment->due_date->format('d M Y') }}
                                        @endif
                                    </p>
                                    @if($assignment->decline_reason)
                                    <p class="text-xs text-red-500 mt-1">
                                        Declined: {{ $assignment->decline_reason }}
                                    </p>
                                    @endif
                                </div>
                                <span class="px-2 py-0.5 rounded text-xs font-medium capitalize
                                    @if($assignment->status === 'completed') bg-green-100 text-green-800
                                    @elseif($assignment->status === 'declined') bg-red-100 text-red-800
                                    @elseif($assignment->status === 'in_progress') bg-orange-100 text-orange-800
                                    @else bg-gray-100 text-gray-600 @endif">
                                    {{ $assignment->status }}
                                </span>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    {{-- Discussion --}}
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
                        <div class="px-5 py-4 border-b border-gray-100">
                            <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide">
                                Discussion ({{ $case->discussions->count() }})
                            </h3>
                        </div>
                        @forelse($case->discussions as $msg)
                        <div class="px-5 py-4 border-b border-gray-50">
                            <div class="flex gap-3">
                                <div class="w-8 h-8 rounded-full flex-shrink-0 flex items-center justify-content-center text-white text-xs font-bold
                                    {{ $msg->is_expert_opinion ? 'bg-purple-600' : 'bg-blue-500' }}"
                                     style="display:flex;align-items:center;justify-content:center">
                                    {{ strtoupper(substr($msg->user->name, 0, 1)) }}
                                </div>
                                <div>
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="text-sm font-semibold text-gray-800 dark:text-white">{{ $msg->user->name }}</span>
                                        @if($msg->is_expert_opinion)
                                            <span class="px-2 py-0.5 rounded-full text-xs bg-purple-100 text-purple-800">★ Expert Opinion</span>
                                        @endif
                                        <span class="text-xs text-gray-400">{{ $msg->created_at->diffForHumans() }}</span>
                                    </div>
                                    <p class="text-sm text-gray-700 dark:text-gray-300">{{ $msg->message }}</p>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="px-5 py-6 text-center text-sm text-gray-400">No discussion yet.</div>
                        @endforelse
                    </div>

                </div>

                {{-- Sidebar --}}
                <div class="space-y-5">

                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5">
                        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Patient</h3>
                        <p class="text-sm font-semibold text-gray-800 dark:text-white">{{ $case->patient->full_name }}</p>
                        <p class="text-xs font-mono text-gray-400">{{ $case->patient->patient_uid }}</p>
                        <dl class="mt-2 space-y-1 text-xs">
                            <div class="flex justify-between">
                                <dt class="text-gray-400">Age</dt>
                                <dd class="text-gray-700 dark:text-gray-300">{{ $case->patient->age }} yrs</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-gray-400">Blood</dt>
                                <dd class="font-bold text-red-600">{{ $case->patient->blood_group ?? '—' }}</dd>
                            </div>
                        </dl>
                    </div>

                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5">
                        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Posted By</h3>
                        <p class="text-sm font-semibold text-gray-800 dark:text-white">{{ $case->postedBy?->name }}</p>
                        <p class="text-xs text-gray-400">{{ $case->hospital?->name }}</p>
                        <p class="text-xs text-gray-400 mt-1">{{ $case->created_at->format('d M Y H:i') }}</p>
                    </div>

                </div>

            </div>
        </div>
    </div>

    {{-- Resolve Modal --}}
    <div id="resolveModal"
         class="hidden fixed inset-0 bg-black bg-opacity-40 flex items-center justify-content-center z-50"
         style="display:none;align-items:center;justify-content:center">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl p-6 max-w-md w-full mx-4">
            <h3 class="text-base font-semibold text-gray-800 dark:text-white mb-4">
                Mark Case as Resolved
            </h3>
            <form action="{{ route('admin.cases.resolve', $case) }}" method="POST">
                @csrf @method('PATCH')
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Resolution Notes <span class="text-red-500">*</span>
                    </label>
                    <textarea name="resolution_notes" rows="4"
                              placeholder="Describe how the case was resolved, final diagnosis, and recommended treatment..."
                              class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-green-500 focus:border-green-500 text-sm"></textarea>
                </div>
                <div class="flex gap-3">
                    <button type="submit"
                            class="px-5 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition">
                        Mark Resolved
                    </button>
                    <button type="button"
                            onclick="document.getElementById('resolveModal').classList.add('hidden')"
                            class="px-5 py-2 border border-gray-300 text-gray-600 hover:bg-gray-50 text-sm font-medium rounded-lg transition">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Close modal on backdrop click
        document.getElementById('resolveModal').addEventListener('click', function(e) {
            if (e.target === this) this.classList.add('hidden');
        });
    </script>
</x-app-layout>
{{-- resources/views/doctor/cases/show.blade.php --}}

<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Case: {{ $case->case_number }}
                </h2>
                <p class="text-sm text-gray-500 mt-0.5">{{ $case->title }}</p>
            </div>
            <a href="{{ route('doctor.cases.index') }}" class="text-sm text-gray-600 hover:text-gray-900 dark:text-gray-400 transition">
                &larr; My Cases
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

            @if(session('success'))
            <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-800 rounded-lg text-sm">
                {{ session('success') }}
            </div>
            @endif

            {{-- Status bar --}}
            @php
            $urgencyClasses = ['low'=>'bg-green-100 text-green-800','medium'=>'bg-yellow-100 text-yellow-800','high'=>'bg-orange-100 text-orange-800','critical'=>'bg-red-100 text-red-800'];
            $statusClasses = ['open'=>'bg-blue-100 text-blue-800','assigned'=>'bg-purple-100 text-purple-800','in_discussion'=>'bg-orange-100 text-orange-800','resolved'=>'bg-green-100 text-green-800','closed'=>'bg-gray-100 text-gray-600'];
            @endphp
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5 mb-5 flex flex-wrap gap-3 items-center justify-between">
                <div class="flex flex-wrap gap-2 items-center">
                    <span class="font-mono text-sm font-bold text-gray-700 dark:text-white">{{ $case->case_number }}</span>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusClasses[$case->status] ?? '' }}">
                        {{ $case->status_label }}
                    </span>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-medium {{ $urgencyClasses[$case->urgency] ?? '' }}">
                        {{ ucfirst($case->urgency) }} urgency
                    </span>
                    <span class="text-xs text-gray-400">
                        {{ $case->specialization?->name }}
                    </span>
                </div>
                <span class="text-xs text-gray-400">
                    Posted {{ $case->created_at->format('d M Y') }}
                </span>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

                {{-- Main content --}}
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

                    {{-- Discussions --}}
                    {{-- At bottom of discussion thread in doctor/cases/show.blade.php --}}

                    {{-- DISCUSSION THREAD --}}
                    <div id="discussion" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
                        <div class="px-5 py-4 border-b border-gray-100">
                            <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide">
                                Discussion Thread ({{ $case->discussions->count() }} messages)
                            </h3>
                        </div>

                        <div class="divide-y divide-gray-50">
                            @forelse($case->discussions->whereNull('parent_id') as $message)
                            <div class="px-5 py-4
            {{ $message->is_expert_opinion ? 'bg-purple-50 dark:bg-purple-900/10 border-l-4 border-purple-500' : '' }}">
                                <div class="flex gap-3">
                                    <div class="w-9 h-9 rounded-full flex-shrink-0 flex items-center justify-content-center text-white text-sm font-bold
                    {{ $message->is_expert_opinion ? 'bg-purple-600' : 'bg-blue-500' }}" style="display:flex;align-items:center;justify-content:center">
                                        {{ strtoupper(substr($message->user->name, 0, 1)) }}
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2 flex-wrap mb-1">
                                            <span class="text-sm font-semibold text-gray-800 dark:text-white">{{ $message->user->name }}</span>
                                            <span class="text-xs text-gray-400 capitalize">{{ $message->user->role }}</span>
                                            @if($message->is_expert_opinion)
                                            <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                                ★ Expert Opinion
                                            </span>
                                            @endif
                                            <span class="text-xs text-gray-400 ml-auto">{{ $message->created_at->diffForHumans() }}</span>
                                        </div>
                                        <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed whitespace-pre-line">
                                            {{ $message->message }}
                                        </p>

                                        {{-- Reply button for doctor --}}
                                        @if(in_array($case->status, ['assigned', 'in_discussion']))
                                        <button onclick="toggleDoctorReply({{ $message->id }})" class="mt-2 text-xs text-blue-600 hover:underline">Reply</button>

                                        <div id="drReplyForm_{{ $message->id }}" class="hidden mt-3">
                                            <form action="{{ route('doctor.discussions.store', $case) }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="parent_id" value="{{ $message->id }}">
                                                <textarea name="message" rows="2" placeholder="Write a reply..." class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm"></textarea>
                                                <div class="mt-2 flex gap-2">
                                                    <button type="submit" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded-lg transition">
                                                        Post Reply
                                                    </button>
                                                    <button type="button" onclick="toggleDoctorReply({{ $message->id }})" class="px-3 py-1.5 border border-gray-300 text-gray-500 text-xs rounded-lg hover:bg-gray-50 transition">
                                                        Cancel
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                        @endif

                                        {{-- Replies --}}
                                        @forelse($message->replies as $reply)
                                        <div class="ml-12 mt-3 pl-4 border-l-2 border-gray-200">
                                            <div class="flex gap-3">
                                                <div class="w-7 h-7 rounded-full flex-shrink-0 flex items-center justify-content-center text-white text-xs font-bold
                                {{ $reply->is_expert_opinion ? 'bg-purple-500' : 'bg-blue-400' }}" style="display:flex;align-items:center;justify-content:center">
                                                    {{ strtoupper(substr($reply->user->name, 0, 1)) }}
                                                </div>
                                                <div>
                                                    <div class="flex items-center gap-2 mb-0.5">
                                                        <span class="text-xs font-semibold text-gray-700">{{ $reply->user->name }}</span>
                                                        <span class="text-xs text-gray-400 capitalize">{{ $reply->user->role }}</span>
                                                        <span class="text-xs text-gray-400 ml-auto">{{ $reply->created_at->diffForHumans() }}</span>
                                                    </div>
                                                    <p class="text-sm text-gray-700 dark:text-gray-300">{{ $reply->message }}</p>
                                                </div>
                                            </div>
                                        </div>
                                        @empty @endforelse
                                    </div>
                                </div>
                            </div>
                            @empty
                            <div class="px-5 py-8 text-center text-sm text-gray-400">
                                No discussion messages yet. Awaiting specialist assignment.
                            </div>
                            @endforelse
                        </div>

                        {{-- Doctor reply form --}}
                        @if(in_array($case->status, ['assigned', 'in_discussion']))
                        <div class="px-5 py-4 border-t border-gray-100 bg-gray-50 dark:bg-gray-700/30">
                            <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-200 mb-3">Post a Message</h4>
                            <form action="{{ route('doctor.discussions.store', $case) }}" method="POST">
                                @csrf
                                <textarea name="message" rows="3" placeholder="Ask the specialist a follow-up question or provide additional information..." class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm @error('message') border-red-400 @enderror"></textarea>
                                @error('message')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                                <div class="mt-2 text-right">
                                    <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition">
                                        Post Message
                                    </button>
                                </div>
                            </form>
                        </div>
                        @endif
                    </div>

                    <script>
                        function toggleDoctorReply(id) {
                            const form = document.getElementById('drReplyForm_' + id);
                            form.classList.toggle('hidden');
                            if (!form.classList.contains('hidden')) form.querySelector('textarea').focus();
                        }

                    </script>

                </div>


                {{-- Sidebar --}}
                <div class="space-y-5">

                    {{-- Patient --}}
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
                        <a href="{{ route('doctor.patients.show', $case->patient) }}" class="mt-2 block text-xs text-blue-600 hover:underline">
                            View patient profile
                        </a>
                    </div>

                    {{-- Assignment status --}}
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5">
                        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Assignment</h3>
                        @if($case->activeAssignment)
                        <div class="space-y-2 text-xs">
                            <div class="flex justify-between">
                                <span class="text-gray-400">Specialist</span>
                                <span class="font-semibold text-gray-700 dark:text-gray-300">
                                    {{ $case->activeAssignment->specialist->name }}
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-400">Status</span>
                                <span class="capitalize font-medium text-purple-700">
                                    {{ $case->activeAssignment->status }}
                                </span>
                            </div>
                            @if($case->activeAssignment->due_date)
                            <div class="flex justify-between">
                                <span class="text-gray-400">Due by</span>
                                <span class="font-medium {{ $case->activeAssignment->due_date->isPast() ? 'text-red-600' : 'text-gray-700' }}">
                                    {{ $case->activeAssignment->due_date->format('d M Y') }}
                                </span>
                            </div>
                            @endif
                        </div>
                        @else
                        <p class="text-xs text-gray-400">
                            No specialist assigned yet. Admin will review and assign shortly.
                        </p>
                        @endif
                    </div>

                    {{-- Hospital --}}
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5">
                        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Posted from</h3>
                        <p class="text-sm text-gray-700 dark:text-gray-300">{{ $case->hospital?->name ?? '—' }}</p>
                    </div>

                </div>

            </div>
        </div>
    </div>
</x-app-layout>

{{-- resources/views/specialist/cases/show.blade.php --}}

<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-700 leading-tight">
                    {{ $case->case_number }}
                </h2>
                <p class="text-sm text-gray-500 mt-0.5">{{ $case->title }}</p>
            </div>
            <a href="{{ route('specialist.cases.index') }}"
               class="text-sm text-gray-600 hover:text-gray-900 dark:text-gray-600 transition">
                &larr; My Cases
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

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
                $urgencyClasses    = ['low'=>'bg-green-100 text-green-800','medium'=>'bg-yellow-100 text-yellow-800','high'=>'bg-orange-100 text-orange-800','critical'=>'bg-red-100 text-red-800'];
                $statusClasses     = ['open'=>'bg-blue-100 text-blue-800','assigned'=>'bg-purple-100 text-purple-800','in_discussion'=>'bg-orange-100 text-orange-800','resolved'=>'bg-green-100 text-green-800','closed'=>'bg-gray-100 text-gray-600'];
                $assignmentClasses = ['pending'=>'bg-yellow-100 text-yellow-800','accepted'=>'bg-blue-100 text-blue-800','in_progress'=>'bg-orange-100 text-orange-800','completed'=>'bg-green-100 text-green-800','declined'=>'bg-red-100 text-red-800'];
            @endphp

            {{-- Assignment action bar --}}
            @if(in_array($assignment->status, ['pending', 'in_progress']))
            <div class="mb-5 p-4 rounded-xl border flex flex-wrap gap-3 items-center justify-between
                {{ $assignment->status === 'pending' ? 'bg-yellow-50 border-yellow-200' : 'bg-orange-50 border-orange-200' }}">
                <div>
                    @if($assignment->status === 'pending')
                    <p class="text-sm font-semibold text-yellow-800">
                        You have been assigned to this case. Please review and accept or decline.
                    </p>
                    <p class="text-xs text-yellow-600 mt-0.5">
                        Urgency: <strong>{{ ucfirst($case->urgency) }}</strong>
                        @if($assignment->due_date) • Due by {{ $assignment->due_date->format('d M Y') }} @endif
                    </p>
                    @else
                    <p class="text-sm font-semibold text-orange-800">Case in progress — post your expert opinion below.</p>
                    @endif
                </div>
                <div class="flex gap-2">
                    @if($assignment->status === 'pending')
                        <form action="{{ route('specialist.cases.accept', $assignment) }}" method="POST">
                            @csrf @method('PATCH')
                            <button class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition">
                                Accept Case
                            </button>
                        </form>
                        <button onclick="document.getElementById('declineModal').classList.remove('hidden')"
                                class="px-4 py-2 border border-red-300 text-red-600 hover:bg-red-50 text-sm font-medium rounded-lg transition">
                            Decline
                        </button>
                    @elseif($assignment->status === 'in_progress')
                        <form action="{{ route('specialist.cases.complete', $assignment) }}" method="POST"
                              onsubmit="return confirm('Mark this case as completed? Make sure you have posted your expert opinion.')">
                            @csrf @method('PATCH')
                            <button class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition">
                                Mark Complete
                            </button>
                        </form>
                    @endif
                </div>
            </div>
            @endif

            {{-- Resolved/completed banner --}}
            @if(in_array($assignment->status, ['completed']))
            <div class="mb-5 p-4 bg-green-50 border border-green-200 rounded-xl flex items-center gap-3">
                <svg class="w-5 h-5 text-green-600 flex-shrink-0" fill="none" stroke="currentColor"
                     stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-sm font-semibold text-green-800">
                    You have completed this case. Completed on {{ $assignment->completed_at?->format('d M Y') }}.
                </p>
            </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

                {{-- Left: Case + Patient info --}}
                <div class="space-y-5">

                    {{-- Case info --}}
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5">
                        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Case Info</h3>
                        <dl class="space-y-2 text-sm">
                            <div class="flex justify-between items-start">
                                <dt class="text-gray-400">Case #</dt>
                                <dd class="font-mono font-bold text-gray-700 dark:text-gray-200">{{ $case->case_number }}</dd>
                            </div>
                            <div class="flex justify-between items-center">
                                <dt class="text-gray-400">Status</dt>
                                <dd><span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $statusClasses[$case->status] ?? '' }}">{{ $case->status_label }}</span></dd>
                            </div>
                            <div class="flex justify-between items-center">
                                <dt class="text-gray-400">Urgency</dt>
                                <dd><span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $urgencyClasses[$case->urgency] ?? '' }}">{{ ucfirst($case->urgency) }}</span></dd>
                            </div>
                            <div class="flex justify-between items-center">
                                <dt class="text-gray-400">Assignment</dt>
                                <dd><span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $assignmentClasses[$assignment->status] ?? '' }}">{{ ucfirst(str_replace('_',' ',$assignment->status)) }}</span></dd>
                            </div>
                            @if($assignment->due_date)
                            <div class="flex justify-between">
                                <dt class="text-gray-400">Due by</dt>
                                <dd class="font-medium {{ $assignment->due_date->isPast() ? 'text-red-600' : 'text-gray-700 dark:text-gray-300' }}">
                                    {{ $assignment->due_date->format('d M Y') }}
                                </dd>
                            </div>
                            @endif
                            <div class="flex justify-between">
                                <dt class="text-gray-400">Specialization</dt>
                                <dd class="font-medium text-gray-700 dark:text-gray-300">{{ $case->specialization?->name }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-gray-400">Posted by</dt>
                                <dd class="font-medium text-gray-700 dark:text-gray-300">{{ $case->postedBy?->name }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-gray-400">Posted on</dt>
                                <dd class="text-gray-500">{{ $case->created_at->format('d M Y') }}</dd>
                            </div>
                        </dl>
                    </div>

                    {{-- Patient info --}}
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5">
                        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Patient</h3>
                        <div class="flex items-center gap-3 mb-3">
                            <div class="w-10 h-10 rounded-full flex items-center justify-content-center text-white font-bold text-sm flex-shrink-0
                                {{ $case->patient->gender === 'male' ? 'bg-blue-500' : 'bg-pink-500' }}"
                                 style="display:flex;align-items:center;justify-content:center">
                                {{ strtoupper(substr($case->patient->first_name, 0, 1)) }}
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-800 dark:text-white">{{ $case->patient->full_name }}</p>
                                <p class="text-xs font-mono text-gray-400">{{ $case->patient->patient_uid }}</p>
                            </div>
                        </div>
                        <dl class="space-y-2 text-xs">
                            <div class="flex justify-between">
                                <dt class="text-gray-400">Age</dt>
                                <dd class="font-medium text-gray-700 dark:text-gray-300">{{ $case->patient->age }} years</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-gray-400">Gender</dt>
                                <dd class="font-medium text-gray-700 dark:text-gray-300 capitalize">{{ $case->patient->gender }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-gray-400">Blood Group</dt>
                                <dd class="font-bold text-red-600">{{ $case->patient->blood_group ?? '—' }}</dd>
                            </div>
                        </dl>
                    </div>

                </div>

                {{-- Right: Case details + medical history + discussion --}}
                <div class="lg:col-span-2 space-y-5">

                    {{-- Case description --}}
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5">
                        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Symptoms Reported</h3>
                        <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed">{{ $case->symptoms }}</p>
                    </div>

                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5">
                        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Full Case Description</h3>
                        <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed whitespace-pre-line">{{ $case->description }}</p>
                    </div>

                    @if($case->prior_treatments)
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5">
                        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Prior Treatments</h3>
                        <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed">{{ $case->prior_treatments }}</p>
                    </div>
                    @endif

                    {{-- Linked medical record --}}
                    @if($case->medicalRecord)
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5 border-l-4 border-blue-400">
                        <h3 class="text-xs font-semibold text-blue-500 uppercase tracking-wide mb-3">Linked Medical Record</h3>
                        <div class="grid grid-cols-2 gap-3 text-sm">
                            <div>
                                <p class="text-xs text-gray-400">Visit Date</p>
                                <p class="font-medium text-gray-700 dark:text-gray-300">
                                    {{ $case->medicalRecord->visit_date->format('d M Y') }}
                                </p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400">Visit Type</p>
                                <p class="font-medium text-gray-700 dark:text-gray-300">
                                    {{ $case->medicalRecord->visit_type_label }}
                                </p>
                            </div>
                            <div class="col-span-2">
                                <p class="text-xs text-gray-400">Diagnosis</p>
                                <p class="font-semibold text-gray-800 dark:text-white">{{ $case->medicalRecord->diagnosis }}</p>
                            </div>
                            <div class="col-span-2">
                                <p class="text-xs text-gray-400">Symptoms</p>
                                <p class="text-gray-700 dark:text-gray-300">{{ $case->medicalRecord->symptoms }}</p>
                            </div>
                            @if($case->medicalRecord->attachments->count())
                            <div class="col-span-2">
                                <p class="text-xs text-gray-400 mb-1">Attachments</p>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($case->medicalRecord->attachments as $file)
                                    <a href="{{ Storage::url($file->file_path) }}" target="_blank"
                                       class="text-xs px-2 py-1 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded transition">
                                        {{ $file->description ?: $file->file_name }}
                                    </a>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif

                    {{-- Patient medical history --}}
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
                        <div class="px-5 py-4 border-b border-gray-100 flex justify-between items-center">
                            <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide">
                                Patient Medical History ({{ $case->patient->medicalRecords->count() }} records)
                            </h3>
                            <button onclick="document.getElementById('historySection').classList.toggle('hidden')"
                                    class="text-xs text-blue-600 hover:underline">
                                Toggle
                            </button>
                        </div>
                        <div id="historySection" class="divide-y divide-gray-50">
                            @forelse($case->patient->medicalRecords->take(5) as $rec)
                            <div class="px-5 py-3 hover:bg-gray-50 transition">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <p class="text-sm font-semibold text-gray-800 dark:text-white">{{ $rec->diagnosis }}</p>
                                        <p class="text-xs text-gray-400">
                                            {{ $rec->visit_date->format('d M Y') }} •
                                            {{ $rec->hospital?->name }} •
                                            Dr. {{ $rec->doctor?->name }}
                                        </p>
                                    </div>
                                    <span class="text-xs px-2 py-0.5 rounded bg-gray-100 text-gray-600 capitalize ml-2 flex-shrink-0">
                                        {{ str_replace('_', ' ', $rec->visit_type) }}
                                    </span>
                                </div>
                                @if($rec->prescription)
                                <p class="text-xs text-gray-500 mt-1">
                                    <span class="font-medium">Rx:</span> {{ Str::limit($rec->prescription, 80) }}
                                </p>
                                @endif
                            </div>
                            @empty
                            <div class="px-5 py-4 text-xs text-gray-400 text-center">No medical history available.</div>
                            @endforelse
                        </div>
                    </div>

                    {{-- ═══════════════════════════════════════════
                         DISCUSSION THREAD
                    ═══════════════════════════════════════════ --}}
                    <div id="discussion" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
                        <div class="px-5 py-4 border-b border-gray-100">
                            <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide">
                                Discussion Thread ({{ $case->discussions->count() }} messages)
                            </h3>
                        </div>

                        {{-- Messages --}}
                        <div class="divide-y divide-gray-50">
                            @forelse($case->discussions as $message)

                            {{-- Top-level message --}}
                            <div class="px-5 py-4
                                {{ $message->is_expert_opinion ? 'bg-purple-50 dark:bg-purple-900/10 border-l-4 border-purple-500' : '' }}">
                                <div class="flex gap-3">
                                    <div class="w-9 h-9 rounded-full flex-shrink-0 flex items-center justify-content-center text-white text-sm font-bold
                                        {{ $message->is_expert_opinion ? 'bg-purple-600' : ($message->user->role === 'doctor' ? 'bg-blue-500' : 'bg-gray-500') }}"
                                         style="display:flex;align-items:center;justify-content:center">
                                        {{ strtoupper(substr($message->user->name, 0, 1)) }}
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2 flex-wrap mb-1">
                                            <span class="text-sm font-semibold text-gray-800 dark:text-white">
                                                {{ $message->user->name }}
                                            </span>
                                            <span class="text-xs text-gray-400 capitalize">
                                                {{ $message->user->role }}
                                            </span>
                                            @if($message->is_expert_opinion)
                                                <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                                    ★ Expert Opinion
                                                </span>
                                            @endif
                                            <span class="text-xs text-gray-400 ml-auto">
                                                {{ $message->created_at->diffForHumans() }}
                                            </span>
                                        </div>
                                        <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed whitespace-pre-line">
                                            {{ $message->message }}
                                        </p>

                                        {{-- Reply button --}}
                                        @if(in_array($assignment->status, ['in_progress', 'completed']))
                                        <button onclick="toggleReplyForm({{ $message->id }})"
                                                class="mt-2 text-xs text-blue-600 hover:underline">
                                            Reply
                                        </button>
                                        @endif

                                        {{-- Delete button --}}
                                        @if($message->user_id === auth()->id() && $message->replies->count() === 0)
                                        <form action="{{ route('specialist.discussions.destroy', $message) }}"
                                              method="POST" class="inline ml-3"
                                              onsubmit="return confirm('Delete this message?')">
                                            @csrf @method('DELETE')
                                            <button class="text-xs text-red-400 hover:text-red-600">Delete</button>
                                        </form>
                                        @endif

                                        {{-- Inline reply form --}}
                                        @if(in_array($assignment->status, ['in_progress', 'completed']))
                                        <div id="replyForm_{{ $message->id }}" class="hidden mt-3">
                                            <form action="{{ route('specialist.discussions.store', $case) }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="parent_id" value="{{ $message->id }}">
                                                <textarea name="message" rows="2"
                                                          placeholder="Write your reply..."
                                                          class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-purple-500 focus:border-purple-500 text-sm"></textarea>
                                                <div class="mt-2 flex gap-2">
                                                    <button type="submit"
                                                            class="px-3 py-1.5 bg-purple-600 hover:bg-purple-700 text-white text-xs font-medium rounded-lg transition">
                                                        Post Reply
                                                    </button>
                                                    <button type="button"
                                                            onclick="toggleReplyForm({{ $message->id }})"
                                                            class="px-3 py-1.5 border border-gray-300 text-gray-500 text-xs rounded-lg hover:bg-gray-50 transition">
                                                        Cancel
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                        @endif
                                    </div>
                                </div>

                                {{-- Replies --}}
                                @forelse($message->replies as $reply)
                                <div class="ml-12 mt-3 pl-4 border-l-2 border-gray-200 dark:border-gray-600">
                                    <div class="flex gap-3">
                                        <div class="w-7 h-7 rounded-full flex-shrink-0 flex items-center justify-content-center text-white text-xs font-bold
                                            {{ $reply->user->role === 'specialist' ? 'bg-purple-500' : 'bg-blue-400' }}"
                                             style="display:flex;align-items:center;justify-content:center">
                                            {{ strtoupper(substr($reply->user->name, 0, 1)) }}
                                        </div>
                                        <div class="flex-1">
                                            <div class="flex items-center gap-2 mb-0.5">
                                                <span class="text-xs font-semibold text-gray-700 dark:text-gray-200">
                                                    {{ $reply->user->name }}
                                                </span>
                                                <span class="text-xs text-gray-400 capitalize">{{ $reply->user->role }}</span>
                                                <span class="text-xs text-gray-400 ml-auto">{{ $reply->created_at->diffForHumans() }}</span>
                                            </div>
                                            <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed">
                                                {{ $reply->message }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                @empty @endforelse

                            </div>
                            @empty
                            <div class="px-5 py-8 text-center text-sm text-gray-400">
                                No messages yet. Be the first to post your expert opinion below.
                            </div>
                            @endforelse
                        </div>

                        {{-- ═══════ POST EXPERT OPINION / MESSAGE FORM ═══════ --}}
                        @if(in_array($assignment->status, ['in_progress']))
                        <div class="px-5 py-5 border-t border-gray-100 bg-gray-50 dark:bg-gray-700/30">
                            <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-200 mb-3">
                                Post Message
                            </h4>

                            {{-- Expert Opinion Form --}}
                            <form action="{{ route('specialist.discussions.store', $case) }}"
                                  method="POST" class="mb-4" id="expertForm">
                                @csrf
                                <input type="hidden" name="is_expert_opinion" value="1">
                                <textarea name="message" rows="4"
                                          placeholder="Write your formal expert opinion here — include your analysis, diagnosis recommendation, and suggested treatment plan..."
                                          class="w-full rounded-lg border-purple-300 dark:border-purple-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-purple-500 focus:border-purple-500 text-sm @error('message') border-red-400 @enderror"></textarea>
                                @error('message')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                                <div class="mt-2 flex items-center justify-between">
                                    <p class="text-xs text-purple-600 font-medium">
                                        ★ This will be marked as your formal expert opinion
                                    </p>
                                    <button type="submit"
                                            class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium rounded-lg transition">
                                        Post Expert Opinion
                                    </button>
                                </div>
                            </form>

                            {{-- Regular message --}}
                            <details class="mt-2">
                                <summary class="text-xs text-gray-500 cursor-pointer hover:text-gray-700 select-none">
                                    Or post a regular message (question / clarification)
                                </summary>
                                <form action="{{ route('specialist.discussions.store', $case) }}"
                                      method="POST" class="mt-3">
                                    @csrf
                                    <input type="hidden" name="is_expert_opinion" value="0">
                                    <textarea name="message" rows="3"
                                              placeholder="Ask the doctor a clarifying question..."
                                              class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm"></textarea>
                                    <div class="mt-2 text-right">
                                        <button type="submit"
                                                class="px-4 py-2 bg-gray-700 hover:bg-gray-800 text-white text-sm font-medium rounded-lg transition">
                                            Post Message
                                        </button>
                                    </div>
                                </form>
                            </details>
                        </div>
                        @elseif($assignment->status === 'pending')
                        <div class="px-5 py-4 bg-yellow-50 border-t border-yellow-100 text-center">
                            <p class="text-sm text-yellow-700">
                                Accept the case above before posting your opinion.
                            </p>
                        </div>
                        @endif

                    </div>
                    {{-- END DISCUSSION --}}

                </div>

            </div>
        </div>
    </div>

    {{-- Decline Modal --}}
    <div id="declineModal"
         class="hidden fixed inset-0 bg-black bg-opacity-40 z-50"
         style="display:none;align-items:center;justify-content:center"
         onclick="if(event.target===this)this.classList.add('hidden')">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl p-6 max-w-md w-full mx-4 mt-20">
            <h3 class="text-base font-semibold text-gray-800 dark:text-white mb-4">
                Decline Case Assignment
            </h3>
            <form action="{{ route('specialist.cases.decline', $assignment) }}" method="POST">
                @csrf @method('PATCH')
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Reason for declining <span class="text-red-500">*</span>
                    </label>
                    <textarea name="decline_reason" rows="3" required
                              placeholder="Please give a clear reason e.g. not in my area of specialization, currently on leave..."
                              class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-red-500 focus:border-red-500 text-sm"></textarea>
                </div>
                <div class="flex gap-3">
                    <button type="submit"
                            class="px-5 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition">
                        Confirm Decline
                    </button>
                    <button type="button"
                            onclick="document.getElementById('declineModal').classList.add('hidden')"
                            class="px-5 py-2 border border-gray-300 text-gray-600 hover:bg-gray-50 text-sm font-medium rounded-lg transition">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function toggleReplyForm(id) {
            const form = document.getElementById('replyForm_' + id);
            form.classList.toggle('hidden');
            if (!form.classList.contains('hidden')) {
                form.querySelector('textarea').focus();
            }
        }

        // Show decline modal properly
        document.querySelector('[onclick*="declineModal"]')?.addEventListener('click', function() {
            const modal = document.getElementById('declineModal');
            modal.style.display = 'flex';
            modal.classList.remove('hidden');
        });
    </script>
</x-app-layout>
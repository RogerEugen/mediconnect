<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
            <div class="min-w-0">
                <a href="{{ route('clinical-cases.index') }}" class="text-sm font-semibold text-teal-700 hover:underline">← Case discussions</a>
                <div class="mt-2 flex flex-wrap items-center gap-2">
                    <span class="font-mono text-xs font-bold text-teal-700">{{ $clinicalCase->case_number }}</span>
                    <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-600">{{ $clinicalCase->status_label }}</span>
                    <span class="rounded-full bg-rose-50 px-2.5 py-1 text-xs font-semibold text-rose-700">{{ ucfirst($clinicalCase->urgency) }} priority</span>
                </div>
                <h1 class="mt-2 text-2xl font-bold text-slate-900 dark:text-white">{{ $clinicalCase->title }}</h1>
            </div>
            <form method="POST" action="{{ route('clinical-cases.follow', $clinicalCase) }}">
                @csrf
                <button class="rounded-xl border px-4 py-2.5 text-sm font-semibold transition {{ $isFollowing ? 'border-teal-200 bg-teal-50 text-teal-800 hover:bg-teal-100' : 'border-slate-300 bg-white text-slate-700 hover:border-teal-300 hover:text-teal-700' }}">
                    {{ $isFollowing ? '✓ Following discussion' : '+ Follow discussion' }}
                </button>
            </form>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="mb-5 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-medium text-rose-800">{{ session('error') }}</div>
            @endif

            <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_320px]">
                <div class="space-y-6">
                    <article class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800">
                        <div class="border-b border-slate-100 bg-gradient-to-r from-teal-50 to-cyan-50 px-6 py-5 dark:border-slate-700 dark:from-slate-800 dark:to-slate-800">
                            <div class="flex flex-wrap items-center justify-between gap-3">
                                <div>
                                    <p class="text-sm font-bold text-slate-900 dark:text-white">{{ $clinicalCase->author_display_name }}</p>
                                    <p class="text-xs text-slate-500">{{ $clinicalCase->author_anonymous ? 'Identity verified by MediConnect' : ($clinicalCase->hospital?->name ?? 'Medical professional') }} · {{ $clinicalCase->created_at->format('d M Y, H:i') }}</p>
                                </div>
                                @if($clinicalCase->specialization)
                                    <span class="rounded-full bg-white px-3 py-1 text-xs font-semibold text-teal-700 shadow-sm dark:bg-slate-700">{{ $clinicalCase->specialization->name }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="space-y-6 p-6">
                            <section>
                                <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400">Case overview</h2>
                                <p class="mt-2 whitespace-pre-line text-sm leading-7 text-slate-700 dark:text-slate-200">{{ $clinicalCase->description }}</p>
                            </section>
                            <div class="grid gap-4 sm:grid-cols-2">
                                <div class="rounded-xl bg-slate-50 p-4 dark:bg-slate-900">
                                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Age group</p>
                                    <p class="mt-1 text-sm font-semibold text-slate-800 dark:text-white">{{ $clinicalCase->patient_age_group_label }}</p>
                                </div>
                                <div class="rounded-xl bg-slate-50 p-4 dark:bg-slate-900">
                                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Sex</p>
                                    <p class="mt-1 text-sm font-semibold text-slate-800 dark:text-white">{{ ucfirst(str_replace('_', ' ', $clinicalCase->patient_sex ?? 'not specified')) }}</p>
                                </div>
                            </div>
                            @foreach([
                                ['Clinical history', $clinicalCase->clinical_history],
                                ['Signs and symptoms', $clinicalCase->symptoms],
                                ['Investigations and results', $clinicalCase->investigation_results],
                                ['Management attempted', $clinicalCase->prior_treatments],
                            ] as [$label, $content])
                                @if($content)
                                    <section class="border-t border-slate-100 pt-5 dark:border-slate-700">
                                        <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400">{{ $label }}</h2>
                                        <p class="mt-2 whitespace-pre-line text-sm leading-7 text-slate-700 dark:text-slate-200">{{ $content }}</p>
                                    </section>
                                @endif
                            @endforeach
                            <section class="rounded-2xl border border-teal-200 bg-teal-50 p-5 dark:border-teal-900 dark:bg-teal-950/30">
                                <h2 class="text-xs font-bold uppercase tracking-wider text-teal-700 dark:text-teal-400">Question for the clinical team</h2>
                                <p class="mt-2 whitespace-pre-line text-base font-semibold leading-7 text-teal-950 dark:text-teal-100">{{ $clinicalCase->discussion_question }}</p>
                            </section>
                            @if($clinicalCase->resolution_notes)
                                <section class="rounded-2xl border border-emerald-200 bg-emerald-50 p-5 dark:border-emerald-900 dark:bg-emerald-950/30">
                                    <h2 class="text-xs font-bold uppercase tracking-wider text-emerald-700">Clinical resolution summary</h2>
                                    <p class="mt-2 whitespace-pre-line text-sm leading-7 text-emerald-950 dark:text-emerald-100">{{ $clinicalCase->resolution_notes }}</p>
                                </section>
                            @endif
                        </div>
                    </article>

                    <section id="discussion" class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800">
                        <div class="border-b border-slate-100 px-6 py-5 dark:border-slate-700">
                            <h2 class="text-lg font-bold text-slate-900 dark:text-white">Clinical discussion</h2>
                            <p class="mt-1 text-sm text-slate-500">{{ $clinicalCase->discussions_count }} contributions from the medical team.</p>
                        </div>
                        <div class="space-y-5 p-6">
                            @forelse($clinicalCase->discussions as $discussion)
                                <div class="rounded-2xl border {{ $discussion->is_expert_opinion ? 'border-purple-200 bg-purple-50/50 dark:border-purple-900 dark:bg-purple-950/20' : 'border-slate-200 bg-white dark:border-slate-700 dark:bg-slate-900' }} p-5">
                                    <div class="flex items-start justify-between gap-3">
                                        <div>
                                            <div class="flex flex-wrap items-center gap-2">
                                                <p class="text-sm font-bold text-slate-900 dark:text-white">{{ $discussion->user->name }}</p>
                                                <span class="rounded-full px-2 py-0.5 text-[11px] font-bold {{ $discussion->user->isSpecialist() ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700' }}">{{ ucfirst($discussion->user->role) }}</span>
                                                @if($discussion->is_expert_opinion)
                                                    <span class="rounded-full bg-purple-600 px-2 py-0.5 text-[11px] font-bold text-white">Specialist insight</span>
                                                @endif
                                            </div>
                                            <p class="mt-1 text-xs text-slate-400">{{ $discussion->created_at->diffForHumans() }}</p>
                                        </div>
                                        @if(auth()->id() === $discussion->user_id || auth()->user()->isAdmin())
                                            <form method="POST" action="{{ route('clinical-discussions.destroy', $discussion) }}" onsubmit="return confirm('Remove this contribution?')">
                                                @csrf @method('DELETE')
                                                <button class="text-xs font-medium text-rose-500 hover:text-rose-700">Remove</button>
                                            </form>
                                        @endif
                                    </div>
                                    <p class="mt-4 whitespace-pre-line text-sm leading-7 text-slate-700 dark:text-slate-200">{{ $discussion->message }}</p>

                                    @foreach($discussion->replies as $reply)
                                        <div class="mt-4 border-l-2 border-teal-200 pl-4">
                                            <div class="flex items-center gap-2">
                                                <p class="text-xs font-bold text-slate-800 dark:text-white">{{ $reply->user->name }}</p>
                                                <span class="text-[11px] text-slate-400">{{ ucfirst($reply->user->role) }} · {{ $reply->created_at->diffForHumans() }}</span>
                                            </div>
                                            <p class="mt-1 whitespace-pre-line text-sm leading-6 text-slate-600 dark:text-slate-300">{{ $reply->message }}</p>
                                        </div>
                                    @endforeach

                                    @if(in_array(auth()->user()->role, ['doctor', 'specialist']) && $clinicalCase->status !== 'closed')
                                        <details class="mt-4">
                                            <summary class="cursor-pointer text-xs font-bold text-teal-700">Reply to this contribution</summary>
                                            <form method="POST" action="{{ route('clinical-discussions.store', $clinicalCase) }}" class="mt-3">
                                                @csrf
                                                <input type="hidden" name="parent_id" value="{{ $discussion->id }}">
                                                <textarea name="message" rows="3" required placeholder="Add a focused response..."
                                                          class="w-full rounded-xl border-slate-300 text-sm focus:border-teal-500 focus:ring-teal-500 dark:border-slate-600 dark:bg-slate-800 dark:text-white"></textarea>
                                                <button class="mt-2 rounded-lg bg-teal-600 px-4 py-2 text-xs font-bold text-white hover:bg-teal-700">Post reply</button>
                                            </form>
                                        </details>
                                    @endif
                                </div>
                            @empty
                                <div class="rounded-2xl border border-dashed border-slate-300 px-6 py-10 text-center dark:border-slate-700">
                                    <p class="font-semibold text-slate-700 dark:text-slate-200">No contributions yet</p>
                                    <p class="mt-1 text-sm text-slate-500">Be the first clinician to share a perspective or ask a clarifying question.</p>
                                </div>
                            @endforelse

                            @if(in_array(auth()->user()->role, ['doctor', 'specialist']) && $clinicalCase->status !== 'closed')
                                <form method="POST" action="{{ route('clinical-discussions.store', $clinicalCase) }}" class="rounded-2xl bg-slate-50 p-5 dark:bg-slate-900">
                                    @csrf
                                    <label for="message" class="block text-sm font-bold text-slate-800 dark:text-white">Add your clinical perspective</label>
                                    <p class="mt-1 text-xs text-slate-500">Keep recommendations professional, evidence-informed and free of patient identifiers.</p>
                                    <textarea id="message" name="message" rows="5" required placeholder="Share differential diagnoses, interpretation, management options, evidence or questions..."
                                              class="mt-3 w-full rounded-xl border-slate-300 text-sm focus:border-teal-500 focus:ring-teal-500 dark:border-slate-600 dark:bg-slate-800 dark:text-white">{{ old('message') }}</textarea>
                                    @error('message')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                                    <div class="mt-3 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                        @if(auth()->user()->isSpecialist())
                                            <label class="inline-flex items-center gap-2 text-sm text-slate-600 dark:text-slate-300">
                                                <input type="checkbox" name="is_expert_opinion" value="1" class="rounded border-slate-300 text-purple-600 focus:ring-purple-500">
                                                Mark as specialist insight
                                            </label>
                                        @else
                                            <span></span>
                                        @endif
                                        <button class="rounded-xl bg-teal-600 px-5 py-2.5 text-sm font-bold text-white shadow-sm hover:bg-teal-700">Post contribution</button>
                                    </div>
                                </form>
                            @endif
                        </div>
                    </section>
                </div>

                <aside class="space-y-5">
                    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
                        <h2 class="text-sm font-bold text-slate-900 dark:text-white">Discussion snapshot</h2>
                        <dl class="mt-4 space-y-3 text-sm">
                            <div class="flex justify-between gap-3"><dt class="text-slate-500">Contributions</dt><dd class="font-bold text-slate-800 dark:text-white">{{ $clinicalCase->discussions_count }}</dd></div>
                            <div class="flex justify-between gap-3"><dt class="text-slate-500">Followers</dt><dd class="font-bold text-slate-800 dark:text-white">{{ $clinicalCase->followers_count }}</dd></div>
                            <div class="flex justify-between gap-3"><dt class="text-slate-500">Last activity</dt><dd class="text-right font-medium text-slate-800 dark:text-white">{{ $clinicalCase->updated_at->diffForHumans() }}</dd></div>
                        </dl>
                    </div>

                    <div class="rounded-2xl border border-blue-200 bg-blue-50 p-5 dark:border-blue-900 dark:bg-blue-950/30">
                        <h2 class="text-sm font-bold text-blue-900 dark:text-blue-200">Clinical safety reminder</h2>
                        <p class="mt-2 text-xs leading-6 text-blue-800 dark:text-blue-300">MediConnect supports professional peer discussion. The treating clinician remains responsible for verifying recommendations, local protocols and patient-specific decisions.</p>
                    </div>

                    @if(auth()->user()->isAdmin() || auth()->id() === $clinicalCase->posted_by)
                        @if($clinicalCase->private_reference)
                            <div class="rounded-2xl border border-amber-200 bg-amber-50 p-5 dark:border-amber-900 dark:bg-amber-950/30">
                                <p class="text-xs font-bold uppercase tracking-wider text-amber-700">Private local reference</p>
                                <p class="mt-2 break-all font-mono text-sm font-bold text-amber-950 dark:text-amber-200">{{ $clinicalCase->private_reference }}</p>
                                <p class="mt-2 text-xs leading-5 text-amber-700">Only the case author and administrators can see this encrypted reference.</p>
                            </div>
                        @endif
                        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
                            <h2 class="text-sm font-bold text-slate-900 dark:text-white">Discussion management</h2>
                            @if($clinicalCase->status === 'resolved')
                                <form method="POST" action="{{ route('clinical-cases.reopen', $clinicalCase) }}" class="mt-4">
                                    @csrf @method('PATCH')
                                    <button class="w-full rounded-xl border border-amber-300 bg-amber-50 px-4 py-2.5 text-sm font-bold text-amber-800 hover:bg-amber-100">Reopen discussion</button>
                                </form>
                            @else
                                <form method="POST" action="{{ route('clinical-cases.resolve', $clinicalCase) }}" class="mt-4">
                                    @csrf @method('PATCH')
                                    <label for="resolution_notes" class="text-xs font-semibold text-slate-600 dark:text-slate-300">Clinical outcome / learning summary</label>
                                    <textarea id="resolution_notes" name="resolution_notes" rows="4" required placeholder="Summarize the final decision, outcome or key learning..."
                                              class="mt-2 w-full rounded-xl border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500 dark:border-slate-600 dark:bg-slate-900 dark:text-white"></textarea>
                                    <button class="mt-2 w-full rounded-xl bg-emerald-600 px-4 py-2.5 text-sm font-bold text-white hover:bg-emerald-700">Mark resolved</button>
                                </form>
                            @endif
                            <form method="POST" action="{{ route('clinical-cases.destroy', $clinicalCase) }}" class="mt-3" onsubmit="return confirm('Permanently remove this clinical case?')">
                                @csrf @method('DELETE')
                                <button class="w-full rounded-xl px-4 py-2 text-xs font-semibold text-rose-600 hover:bg-rose-50">Delete discussion</button>
                            </form>
                        </div>
                    @endif
                </aside>
            </div>
        </div>
    </div>
</x-app-layout>

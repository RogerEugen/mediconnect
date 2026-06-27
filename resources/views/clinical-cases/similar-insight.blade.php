<x-app-layout>
    <x-slot name="header">
        <div>
            <a href="{{ route('clinical-cases.show', $clinicalCase) }}" class="text-sm font-bold text-indigo-700 hover:underline">← Back to {{ $clinicalCase->case_number }}</a>
            <div class="mt-3 flex flex-wrap items-center gap-2">
                <span class="rounded-full bg-indigo-600 px-3 py-1 text-xs font-extrabold text-white">{{ $score }}% similar</span>
                @if($similarCase->specialization)<span class="rounded-full bg-cyan-50 px-3 py-1 text-xs font-bold text-cyan-700">{{ $similarCase->specialization->name }}</span>@endif
                <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-600">Read-only insight</span>
            </div>
            <h1 class="mt-3 text-2xl font-bold text-slate-900 dark:text-white">{{ $similarCase->title }}</h1>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-5xl space-y-6 px-4 sm:px-6 lg:px-8">
            <div class="rounded-2xl border border-indigo-200 bg-indigo-50 p-5 text-sm leading-6 text-indigo-900 dark:border-indigo-900 dark:bg-indigo-950/30 dark:text-indigo-200">
                MediConnect matched this de-identified case to your case using specialty and shared clinical terms. It is shown only as a knowledge reference; verify all recommendations against the current patient and local protocols.
            </div>

            <article class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-800">
                <div class="space-y-5">
                    @foreach([
                        ['Case overview', $similarCase->description],
                        ['Clinical history', $similarCase->clinical_history],
                        ['Signs and symptoms', $similarCase->symptoms],
                        ['Investigations and results', $similarCase->investigation_results],
                        ['Management attempted', $similarCase->prior_treatments],
                        ['Clinical question', $similarCase->discussion_question],
                    ] as [$label, $content])
                        @if($content)
                            <section>
                                <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400">{{ $label }}</h2>
                                <p class="mt-2 whitespace-pre-line text-sm leading-7 text-slate-700 dark:text-slate-200">{{ $content }}</p>
                            </section>
                        @endif
                    @endforeach

                    @if($similarCase->resolution_notes)
                        <section class="rounded-2xl border border-emerald-200 bg-emerald-50 p-5 dark:border-emerald-900 dark:bg-emerald-950/30">
                            <h2 class="text-xs font-bold uppercase tracking-wider text-emerald-700">Recorded solution</h2>
                            <p class="mt-2 whitespace-pre-line text-sm leading-7 text-emerald-950 dark:text-emerald-100">{{ $similarCase->resolution_notes }}</p>
                        </section>
                    @endif

                    @if($similarCase->attachments->isNotEmpty())
                        <section>
                            <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400">Supporting images and reports</h2>
                            <div class="mt-3 grid gap-3 sm:grid-cols-2">
                                @foreach($similarCase->attachments as $attachment)
                                    <a href="{{ route('clinical-cases.attachments.show', [$similarCase, $attachment]) }}" target="_blank" class="flex items-center gap-3 rounded-xl border border-slate-200 p-3 hover:border-indigo-300 hover:bg-indigo-50 dark:border-slate-700 dark:hover:bg-slate-900">
                                        <span class="text-xl">{{ $attachment->is_image ? '🖼️' : '📄' }}</span>
                                        <span class="min-w-0"><span class="block truncate text-sm font-bold text-slate-800 dark:text-white">{{ $attachment->file_name }}</span><span class="text-xs text-slate-500">Open securely</span></span>
                                    </a>
                                @endforeach
                            </div>
                        </section>
                    @endif
                </div>
            </article>

            <section class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800">
                <div class="border-b border-slate-100 px-6 py-5 dark:border-slate-700">
                    <h2 class="text-lg font-bold text-slate-900 dark:text-white">Discussion and specialist insights</h2>
                    <p class="mt-1 text-sm text-slate-500">{{ $similarCase->discussions_count }} contributions. This view is read-only.</p>
                </div>
                <div class="space-y-4 p-6">
                    @forelse($similarCase->discussions as $discussion)
                        <div class="rounded-xl border {{ $discussion->is_expert_opinion ? 'border-purple-200 bg-purple-50' : 'border-slate-200 bg-slate-50' }} p-4 dark:border-slate-700 dark:bg-slate-900">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="text-sm font-bold text-slate-900 dark:text-white">{{ $discussion->user->name }}</span>
                                <span class="rounded-full bg-white px-2 py-0.5 text-[11px] font-bold text-slate-600 dark:bg-slate-700 dark:text-slate-200">{{ ucfirst($discussion->user->role) }}</span>
                                @if($discussion->is_expert_opinion)<span class="rounded-full bg-purple-600 px-2 py-0.5 text-[11px] font-bold text-white">Specialist insight</span>@endif
                            </div>
                            <p class="mt-3 whitespace-pre-line text-sm leading-7 text-slate-700 dark:text-slate-200">{{ $discussion->message }}</p>
                            @foreach($discussion->replies as $reply)
                                <div class="mt-3 border-l-2 border-indigo-200 pl-4">
                                    <p class="text-xs font-bold text-slate-700 dark:text-slate-200">{{ $reply->user->name }} · {{ ucfirst($reply->user->role) }}</p>
                                    <p class="mt-1 text-sm leading-6 text-slate-600 dark:text-slate-300">{{ $reply->message }}</p>
                                </div>
                            @endforeach
                        </div>
                    @empty
                        <p class="py-8 text-center text-sm text-slate-500">No discussion is available.</p>
                    @endforelse
                </div>
            </section>
        </div>
    </div>
</x-app-layout>

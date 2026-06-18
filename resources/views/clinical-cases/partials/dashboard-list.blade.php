<section class="mt-6 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800">
    <div class="flex items-center justify-between border-b border-slate-100 px-6 py-5 dark:border-slate-700">
        <div>
            <h2 class="font-bold text-slate-900 dark:text-white">{{ $title }}</h2>
            <p class="mt-0.5 text-xs text-slate-500">Cases are anonymized and open for voluntary professional contribution.</p>
        </div>
        <a href="{{ route('clinical-cases.index') }}" class="text-sm font-bold text-teal-700 hover:underline">View all</a>
    </div>
    <div class="divide-y divide-slate-100 dark:divide-slate-700">
        @forelse($recentCases as $case)
            <a href="{{ route('clinical-cases.show', $case) }}" class="flex flex-col gap-3 px-6 py-4 transition hover:bg-slate-50 dark:hover:bg-slate-900 sm:flex-row sm:items-center sm:justify-between">
                <div class="min-w-0">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="font-mono text-xs font-bold text-teal-700">{{ $case->case_number }}</span>
                        <span class="rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-600">{{ ucfirst($case->urgency) }}</span>
                        @if($case->specialization)<span class="text-xs text-slate-400">{{ $case->specialization->name }}</span>@endif
                    </div>
                    <p class="mt-1 truncate text-sm font-bold text-slate-900 dark:text-white">{{ $case->title }}</p>
                    <p class="mt-0.5 text-xs text-slate-500">By {{ $case->author_display_name }} · active {{ $case->updated_at->diffForHumans() }}</p>
                </div>
                <div class="flex shrink-0 items-center gap-2 text-xs font-semibold text-slate-500">
                    <span>{{ $case->discussions_count }} contributions</span>
                    <span class="text-teal-700">Open →</span>
                </div>
            </a>
        @empty
            <div class="px-6 py-12 text-center text-sm text-slate-500">{{ $emptyMessage }}</div>
        @endforelse
    </div>
</section>

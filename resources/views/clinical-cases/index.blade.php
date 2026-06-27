<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.2em] text-teal-600">Clinical knowledge exchange</p>
                <h1 class="mt-1 text-2xl font-bold text-slate-900 dark:text-white">Case Discussions</h1>
                <p class="mt-1 text-sm text-slate-500">Review difficult anonymized cases and share evidence-informed clinical perspectives.</p>
            </div>
            @if(auth()->user()->isDoctor())
                <a href="{{ route('clinical-cases.create') }}"
                   class="inline-flex items-center justify-center gap-2 rounded-xl bg-teal-600 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-teal-600/20 transition hover:bg-teal-700">
                    <span class="text-lg leading-none">+</span> Start a discussion
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">{{ session('success') }}</div>
            @endif

            <div class="mb-6 rounded-2xl border border-teal-200 bg-teal-50 p-5 dark:border-teal-900 dark:bg-teal-950/30">
                <p class="text-xs font-bold uppercase tracking-[0.18em] text-teal-700">{{ auth()->user()->isDoctor() ? 'Your clinical cases' : 'Matched specialist cases' }}</p>
                <p class="mt-1 text-sm leading-6 text-teal-900 dark:text-teal-200">{{ auth()->user()->isDoctor() ? 'Only cases you posted appear here. Similar solved cases are suggested privately inside your own case.' : 'Only cases matching your verified specialties appear here.' }}</p>
            </div>

            <div class="mb-6 grid gap-4 sm:grid-cols-3">
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
                    <p class="text-sm text-slate-500">Active discussions</p>
                    <p class="mt-2 text-3xl font-bold text-slate-900 dark:text-white">{{ $stats['open'] }}</p>
                </div>
                <div class="rounded-2xl border border-amber-200 bg-amber-50 p-5 shadow-sm dark:border-amber-900 dark:bg-amber-950/30">
                    <p class="text-sm text-amber-700 dark:text-amber-400">Awaiting first insight</p>
                    <p class="mt-2 text-3xl font-bold text-amber-900 dark:text-amber-300">{{ $stats['unanswered'] }}</p>
                </div>
                <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-5 shadow-sm dark:border-emerald-900 dark:bg-emerald-950/30">
                    <p class="text-sm text-emerald-700 dark:text-emerald-400">Resolved cases</p>
                    <p class="mt-2 text-3xl font-bold text-emerald-900 dark:text-emerald-300">{{ $stats['resolved'] }}</p>
                </div>
            </div>

            <form method="GET" class="mb-6 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-700 dark:bg-slate-800">
                <div class="grid gap-3 lg:grid-cols-6">
                    <div class="lg:col-span-2">
                        <label class="sr-only" for="search">Search cases</label>
                        <input id="search" name="search" value="{{ request('search') }}" placeholder="Search title, symptoms or case number..."
                               class="w-full rounded-xl border-slate-300 text-sm focus:border-teal-500 focus:ring-teal-500 dark:border-slate-600 dark:bg-slate-900 dark:text-white">
                    </div>
                    <select name="specialization" class="rounded-xl border-slate-300 text-sm focus:border-teal-500 focus:ring-teal-500 dark:border-slate-600 dark:bg-slate-900 dark:text-white">
                        <option value="">All specialties</option>
                        @foreach($specializations as $specialization)
                            <option value="{{ $specialization->id }}" @selected(request('specialization') == $specialization->id)>{{ $specialization->name }}</option>
                        @endforeach
                    </select>
                    <select name="status" class="rounded-xl border-slate-300 text-sm focus:border-teal-500 focus:ring-teal-500 dark:border-slate-600 dark:bg-slate-900 dark:text-white">
                        <option value="">All statuses</option>
                        <option value="open" @selected(request('status') === 'open')>Open</option>
                        <option value="in_discussion" @selected(request('status') === 'in_discussion')>In discussion</option>
                        <option value="resolved" @selected(request('status') === 'resolved')>Resolved</option>
                    </select>
                    <select name="sort" class="rounded-xl border-slate-300 text-sm focus:border-teal-500 focus:ring-teal-500 dark:border-slate-600 dark:bg-slate-900 dark:text-white">
                        <option value="">Newest first</option>
                        <option value="active" @selected(request('sort') === 'active')>Recently active</option>
                        <option value="unanswered" @selected(request('sort') === 'unanswered')>Unanswered</option>
                        <option value="urgent" @selected(request('sort') === 'urgent')>Urgent first</option>
                    </select>
                    <button class="rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-700 dark:bg-teal-600 dark:hover:bg-teal-700">Apply filters</button>
                </div>
                <div class="mt-3 flex flex-wrap gap-4 text-sm">
                    @if(auth()->user()->isSpecialist())
                    <label class="inline-flex items-center gap-2 text-slate-600 dark:text-slate-300">
                        <input type="checkbox" name="following" value="1" @checked(request()->boolean('following')) class="rounded border-slate-300 text-teal-600 focus:ring-teal-500">
                        Following
                    </label>
                    @endif
                    @if(request()->hasAny(['search', 'specialization', 'status', 'sort', 'mine', 'following']))
                        <a href="{{ route('clinical-cases.index') }}" class="font-medium text-teal-700 hover:underline">Clear filters</a>
                    @endif
                </div>
            </form>

            <div class="space-y-4">
                @forelse($cases as $case)
                    @php
                        $urgency = [
                            'low' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                            'medium' => 'bg-amber-50 text-amber-700 border-amber-200',
                            'high' => 'bg-orange-50 text-orange-700 border-orange-200',
                            'critical' => 'bg-rose-50 text-rose-700 border-rose-200',
                        ][$case->urgency] ?? 'bg-slate-50 text-slate-700 border-slate-200';
                    @endphp
                    <article class="group rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-teal-300 hover:shadow-md dark:border-slate-700 dark:bg-slate-800">
                        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="font-mono text-xs font-bold text-teal-700">{{ $case->case_number }}</span>
                                    <span class="rounded-full border px-2.5 py-1 text-xs font-semibold {{ $urgency }}">{{ ucfirst($case->urgency) }}</span>
                                    <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-600 dark:bg-slate-700 dark:text-slate-200">{{ $case->status_label }}</span>
                                    @if($case->specialization)
                                        <span class="rounded-full bg-cyan-50 px-2.5 py-1 text-xs font-medium text-cyan-700">{{ $case->specialization->name }}</span>
                                    @endif
                                </div>
                                <a href="{{ route('clinical-cases.show', $case) }}" class="mt-3 block text-lg font-bold text-slate-900 transition group-hover:text-teal-700 dark:text-white">
                                    {{ $case->title }}
                                </a>
                                <p class="mt-2 line-clamp-2 text-sm leading-6 text-slate-600 dark:text-slate-300">{{ $case->description }}</p>
                                <div class="mt-4 flex flex-wrap items-center gap-x-5 gap-y-2 text-xs text-slate-500">
                                    <span>{{ $case->patient_age_group_label }} · {{ ucfirst(str_replace('_', ' ', $case->patient_sex ?? 'not specified')) }}</span>
                                    <span>Posted by {{ $case->author_display_name }}</span>
                                    <span>{{ $case->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                            <div class="flex items-center gap-3 lg:flex-col lg:items-end">
                                <div class="rounded-xl bg-slate-50 px-4 py-3 text-center dark:bg-slate-900">
                                    <p class="text-xl font-bold text-slate-900 dark:text-white">{{ $case->discussions_count }}</p>
                                    <p class="text-xs text-slate-500">contributions</p>
                                </div>
                                <a href="{{ route('clinical-cases.show', $case) }}" class="text-sm font-semibold text-teal-700 hover:text-teal-900">Open discussion →</a>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="rounded-2xl border border-dashed border-slate-300 bg-white px-6 py-16 text-center dark:border-slate-700 dark:bg-slate-800">
                        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-teal-50 text-2xl">🩺</div>
                        <h2 class="mt-4 text-lg font-bold text-slate-900 dark:text-white">No matching clinical cases</h2>
                        <p class="mt-1 text-sm text-slate-500">Change your filters or start a new anonymized discussion.</p>
                    </div>
                @endforelse
            </div>

            <div class="mt-6">{{ $cases->links() }}</div>
        </div>
    </div>
</x-app-layout>

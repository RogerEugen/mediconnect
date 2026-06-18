<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-xs font-bold uppercase tracking-[0.2em] text-slate-500">Platform oversight</p>
            <h1 class="mt-1 text-2xl font-bold text-slate-900 dark:text-white">Administration Dashboard</h1>
            <p class="mt-1 text-sm text-slate-500">Manage the clinical community, knowledge categories, privacy and platform activity.</p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                @foreach([
                    ['Active clinicians', $stats['clinicians'], 'bg-blue-50 text-blue-700'],
                    ['Active discussions', $stats['active_cases'], 'bg-teal-50 text-teal-700'],
                    ['Awaiting insight', $stats['unanswered'], 'bg-amber-50 text-amber-700'],
                    ['Resolved cases', $stats['resolved'], 'bg-emerald-50 text-emerald-700'],
                ] as [$label, $value, $style])
                    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
                        <span class="inline-flex rounded-lg px-2.5 py-1 text-xs font-bold {{ $style }}">{{ $label }}</span>
                        <p class="mt-3 text-3xl font-bold text-slate-900 dark:text-white">{{ $value }}</p>
                    </div>
                @endforeach
            </div>

            <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <a href="{{ route('admin.users.index') }}" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:border-blue-300 hover:shadow-md dark:border-slate-700 dark:bg-slate-800">
                    <p class="font-bold text-slate-900 dark:text-white">Manage users</p><p class="mt-1 text-sm text-slate-500">Doctors, specialists and access status</p>
                </a>
                <a href="{{ route('admin.hospitals.index') }}" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:border-blue-300 hover:shadow-md dark:border-slate-700 dark:bg-slate-800">
                    <p class="font-bold text-slate-900 dark:text-white">Hospitals</p><p class="mt-1 text-sm text-slate-500">Manage participating facilities</p>
                </a>
                <a href="{{ route('admin.specializations.index') }}" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:border-blue-300 hover:shadow-md dark:border-slate-700 dark:bg-slate-800">
                    <p class="font-bold text-slate-900 dark:text-white">Specialties</p><p class="mt-1 text-sm text-slate-500">Maintain discussion categories</p>
                </a>
                <a href="{{ route('admin.audit-logs.index') }}" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:border-blue-300 hover:shadow-md dark:border-slate-700 dark:bg-slate-800">
                    <p class="font-bold text-slate-900 dark:text-white">Audit trail</p><p class="mt-1 text-sm text-slate-500">Review privacy and platform activity</p>
                </a>
            </div>

            @include('clinical-cases.partials.dashboard-list', [
                'title' => 'Latest clinical discussions',
                'emptyMessage' => 'No clinical discussions have been posted yet.',
            ])
        </div>
    </div>
</x-app-layout>

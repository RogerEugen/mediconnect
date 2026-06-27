<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.2em] text-teal-600">Doctor workspace</p>
                <h1 class="mt-1 text-2xl font-bold text-slate-900 dark:text-white">Welcome, Dr. {{ auth()->user()->name }}</h1>
                <p class="mt-1 text-sm text-slate-500">Post difficult cases, receive specialist insight and review matched solutions.</p>
            </div>
            <a href="{{ route('clinical-cases.create') }}" class="rounded-xl bg-teal-600 px-5 py-3 text-center text-sm font-bold text-white shadow-lg shadow-teal-600/20 hover:bg-teal-700">+ Start discussion</a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                @foreach([
                    ['My active cases', $stats['community_cases'], 'bg-teal-50 text-teal-700'],
                    ['My discussions', $stats['my_cases'], 'bg-blue-50 text-blue-700'],
                    ['My contributions', $stats['my_contributions'], 'bg-purple-50 text-purple-700'],
                    ['Awaiting insight', $stats['unanswered'], 'bg-amber-50 text-amber-700'],
                ] as [$label, $value, $style])
                    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
                        <span class="inline-flex rounded-lg px-2.5 py-1 text-xs font-bold {{ $style }}">{{ $label }}</span>
                        <p class="mt-3 text-3xl font-bold text-slate-900 dark:text-white">{{ $value }}</p>
                    </div>
                @endforeach
            </div>

            @include('clinical-cases.partials.dashboard-list', [
                'title' => 'My recently active cases',
                'emptyMessage' => 'You have not posted a clinical case yet.',
            ])
        </div>
    </div>
</x-app-layout>

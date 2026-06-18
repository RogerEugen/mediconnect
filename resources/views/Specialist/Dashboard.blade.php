{{-- resources/views/specialist/dashboard.blade.php --}}

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Specialist Dashboard
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Welcome card --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 mb-6">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-full bg-purple-600 flex items-center justify-content-center text-white text-2xl font-bold flex-shrink-0"
                         style="display:flex;align-items:center;justify-content:center">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-white">
                            Welcome, {{ auth()->user()->name }}
                        </h3>
                        <p class="text-sm text-gray-500">
                            {{ auth()->user()->hospital?->name ?? 'No hospital assigned' }}
                            @if(auth()->user()->specializations->count())
                                • {{ auth()->user()->specializations->pluck('name')->join(', ') }}
                            @endif
                        </p>
                    </div>
                    @if($unreadCount > 0)
                    <div class="ml-auto">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                            {{ $unreadCount }} unread notification{{ $unreadCount > 1 ? 's' : '' }}
                        </span>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Stats --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5 flex items-center gap-4">
                    <div class="bg-yellow-100 text-yellow-600 rounded-lg p-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Pending Review</p>
                        <p class="text-2xl font-bold text-gray-800 dark:text-white">{{ $pendingCount }}</p>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5 flex items-center gap-4">
                    <div class="bg-orange-100 text-orange-600 rounded-lg p-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">In Progress</p>
                        <p class="text-2xl font-bold text-gray-800 dark:text-white">{{ $inProgressCount }}</p>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5 flex items-center gap-4">
                    <div class="bg-green-100 text-green-600 rounded-lg p-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Completed</p>
                        <p class="text-2xl font-bold text-gray-800 dark:text-white">{{ $completedCount }}</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- Quick actions --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
                    <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wide mb-4">
                        Quick Actions
                    </h3>
                    <div class="space-y-2">
                        <a href="{{ route('specialist.cases.index') }}"
                           class="flex items-center gap-3 p-3 rounded-lg border border-gray-200 hover:bg-purple-50 hover:border-purple-300 transition group">
                            <div class="bg-purple-100 text-purple-600 rounded-lg p-2 group-hover:bg-purple-200 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-700">My Assigned Cases</p>
                                @if($pendingCount > 0)
                                <p class="text-xs text-yellow-600">{{ $pendingCount }} awaiting your action</p>
                                @endif
                            </div>
                        </a>
                        <a href="{{ route('specialist.cases.index', ['status' => 'in_progress']) }}"
                           class="flex items-center gap-3 p-3 rounded-lg border border-gray-200 hover:bg-orange-50 hover:border-orange-300 transition group">
                            <div class="bg-orange-100 text-orange-600 rounded-lg p-2 group-hover:bg-orange-200 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                </svg>
                            </div>
                            <span class="text-sm font-medium text-gray-700">Cases In Progress</span>
                        </a>
                    </div>
                </div>

                {{-- Recent assigned cases --}}
                <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wide">
                            Active Cases
                        </h3>
                        <a href="{{ route('specialist.cases.index') }}"
                           class="text-xs text-blue-600 hover:underline">View all</a>
                    </div>
                    @php
                        $urgencyClasses = ['low'=>'bg-green-100 text-green-800','medium'=>'bg-yellow-100 text-yellow-800','high'=>'bg-orange-100 text-orange-800','critical'=>'bg-red-100 text-red-800'];
                    @endphp
                    <div class="divide-y divide-gray-100">
                        @forelse($recentCases as $assignment)
                        <div class="flex items-center justify-between px-6 py-4 hover:bg-gray-50 transition">
                            <div class="flex-1 min-w-0 pr-4">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="text-xs font-mono font-bold text-gray-600">
                                        {{ $assignment->case->case_number }}
                                    </span>
                                    <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $urgencyClasses[$assignment->case->urgency] ?? '' }}">
                                        {{ ucfirst($assignment->case->urgency) }}
                                    </span>
                                    @if($assignment->status === 'pending')
                                    <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        Awaiting action
                                    </span>
                                    @endif
                                </div>
                                <p class="text-sm font-medium text-gray-800 dark:text-white mt-0.5 truncate">
                                    {{ $assignment->case->title }}
                                </p>
                                <p class="text-xs text-gray-400">
                                    {{ $assignment->case->patient->full_name }} •
                                    {{ $assignment->case->specialization?->name }}
                                </p>
                            </div>
                            <a href="{{ route('specialist.cases.show', $assignment->case) }}"
                               class="text-xs px-3 py-1.5 rounded border border-purple-300 text-purple-600 hover:bg-purple-50 transition flex-shrink-0">
                                Review
                            </a>
                        </div>
                        @empty
                        <div class="px-6 py-8 text-center text-sm text-gray-400">
                            No active cases assigned to you yet.
                        </div>
                        @endforelse
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
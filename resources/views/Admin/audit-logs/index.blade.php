{{-- resources/views/admin/audit-logs/index.blade.php --}}

<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Audit Logs
            </h2>
            <a href="{{ route('admin.audit-logs.export', request()->query()) }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-gray-800 hover:bg-gray-900 text-white text-sm font-medium rounded-lg transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                Export CSV
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Stats --}}
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5">
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Today</p>
                    <p class="text-3xl font-bold text-gray-800 dark:text-white mt-1">{{ number_format($stats['today']) }}</p>
                    <p class="text-xs text-gray-400 mt-0.5">actions logged</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5">
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">This Week</p>
                    <p class="text-3xl font-bold text-gray-800 dark:text-white mt-1">{{ number_format($stats['this_week']) }}</p>
                    <p class="text-xs text-gray-400 mt-0.5">actions logged</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5">
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Total Logs</p>
                    <p class="text-3xl font-bold text-gray-800 dark:text-white mt-1">{{ number_format($stats['total']) }}</p>
                    <p class="text-xs text-gray-400 mt-0.5">all time</p>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5">
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Unique Users</p>
                    <p class="text-3xl font-bold text-gray-800 dark:text-white mt-1">{{ number_format($stats['unique_users']) }}</p>
                    <p class="text-xs text-gray-400 mt-0.5">recorded</p>
                </div>
            </div>

            {{-- Filters --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5 mb-5">
                <form method="GET" action="{{ route('admin.audit-logs.index') }}"
                      class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Search description</label>
                        <input type="text" name="search" value="{{ request('search') }}"
                               placeholder="Search..."
                               class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">User</label>
                        <select name="user_id"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                            <option value="">All users</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}"
                                    {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }} ({{ ucfirst($user->role) }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Action</label>
                        <select name="action"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                            <option value="">All actions</option>
                            @foreach($actions as $action)
                                <option value="{{ $action }}"
                                    {{ request('action') === $action ? 'selected' : '' }}>
                                    {{ ucwords(str_replace('_', ' ', $action)) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Model type</label>
                        <select name="model_type"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                            <option value="">All models</option>
                            @foreach($modelTypes as $type)
                                <option value="{{ $type }}"
                                    {{ request('model_type') === $type ? 'selected' : '' }}>
                                    {{ $type }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Date from</label>
                        <input type="date" name="date_from" value="{{ request('date_from') }}"
                               class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Date to</label>
                        <input type="date" name="date_to" value="{{ request('date_to') }}"
                               class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                    </div>

                    <div class="flex items-end gap-2 sm:col-span-2">
                        <button type="submit"
                                class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition">
                            Apply Filters
                        </button>
                        @if(request()->hasAny(['search','user_id','action','model_type','date_from','date_to']))
                        <a href="{{ route('admin.audit-logs.index') }}"
                           class="px-5 py-2 border border-gray-300 text-gray-600 hover:bg-gray-50 text-sm font-medium rounded-lg transition">
                            Clear
                        </a>
                        <span class="text-xs text-gray-400 self-center">
                            {{ $logs->total() }} result(s)
                        </span>
                        @endif
                    </div>

                </form>
            </div>

            {{-- Logs Table --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                            <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                            <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                            <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Record</th>
                            <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">IP Address</th>
                            <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date / Time</th>
                            <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @php
                            $colorMap = [
                                'blue'   => 'bg-blue-100 text-blue-800',
                                'green'  => 'bg-green-100 text-green-800',
                                'yellow' => 'bg-yellow-100 text-yellow-800',
                                'red'    => 'bg-red-100 text-red-800',
                                'purple' => 'bg-purple-100 text-purple-800',
                                'gray'   => 'bg-gray-100 text-gray-600',
                            ];
                        @endphp

                        @forelse($logs as $log)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">

                            {{-- User --}}
                            <td class="px-5 py-3">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 rounded-full flex items-center justify-content-center text-white text-xs font-bold flex-shrink-0
                                        {{ $log->user?->role === 'admin' ? 'bg-gray-800' : ($log->user?->role === 'specialist' ? 'bg-purple-600' : 'bg-blue-600') }}"
                                         style="display:flex;align-items:center;justify-content:center">
                                        {{ strtoupper(substr($log->user?->name ?? 'S', 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-800 dark:text-white leading-tight">
                                            {{ $log->user?->name ?? 'System' }}
                                        </p>
                                        <p class="text-xs text-gray-400 capitalize">
                                            {{ $log->user?->role ?? '—' }}
                                        </p>
                                    </div>
                                </div>
                            </td>

                            {{-- Action badge --}}
                            <td class="px-5 py-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $colorMap[$log->action_color] ?? $colorMap['gray'] }}">
                                    {{ $log->action_label }}
                                </span>
                            </td>

                            {{-- Description --}}
                            <td class="px-5 py-3 max-w-xs">
                                <p class="text-sm text-gray-600 dark:text-gray-300 truncate">
                                    {{ $log->description ?? '—' }}
                                </p>
                            </td>

                            {{-- Model --}}
                            <td class="px-5 py-3">
                                @if($log->model_type)
                                <div>
                                    <p class="text-xs font-medium text-gray-700 dark:text-gray-300">{{ $log->model_type }}</p>
                                    <p class="text-xs text-gray-400">#{{ $log->model_id }}</p>
                                </div>
                                @else
                                <span class="text-gray-400 text-sm">—</span>
                                @endif
                            </td>

                            {{-- IP --}}
                            <td class="px-5 py-3 text-xs text-gray-500 font-mono">
                                {{ $log->ip_address ?? '—' }}
                            </td>

                            {{-- Date --}}
                            <td class="px-5 py-3">
                                <p class="text-xs text-gray-600 dark:text-gray-300">
                                    {{ $log->created_at->format('d M Y') }}
                                </p>
                                <p class="text-xs text-gray-400">
                                    {{ $log->created_at->format('H:i:s') }}
                                </p>
                            </td>

                            {{-- View --}}
                            <td class="px-5 py-3">
                                <a href="{{ route('admin.audit-logs.show', $log) }}"
                                   class="text-xs px-2.5 py-1 rounded border border-gray-300 text-gray-600 hover:bg-gray-50 transition">
                                    Detail
                                </a>
                            </td>

                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-5 py-12 text-center text-sm text-gray-400">
                                No audit logs found.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="px-5 py-4 border-t border-gray-100 flex items-center justify-between">
                    <p class="text-xs text-gray-500">
                        Showing {{ $logs->firstItem() }}–{{ $logs->lastItem() }} of {{ number_format($logs->total()) }} entries
                    </p>
                    {{ $logs->links() }}
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
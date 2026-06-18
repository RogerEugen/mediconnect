{{-- resources/views/admin/audit-logs/show.blade.php --}}

<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Audit Log Detail
            </h2>
            <a href="{{ route('admin.audit-logs.index') }}"
               class="text-sm text-gray-600 hover:text-gray-900 dark:text-gray-400 transition">
                &larr; Back to logs
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
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

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">

                {{-- Header strip --}}
                <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <span class="px-3 py-1 rounded-full text-sm font-medium {{ $colorMap[$auditLog->action_color] ?? $colorMap['gray'] }}">
                            {{ $auditLog->action_label }}
                        </span>
                        <span class="text-xs text-gray-400 font-mono">Log #{{ $auditLog->id }}</span>
                    </div>
                    <span class="text-sm text-gray-500">
                        {{ $auditLog->created_at->format('d M Y H:i:s') }}
                    </span>
                </div>

                <div class="px-6 py-5 space-y-5">

                    {{-- User --}}
                    <div>
                        <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-2">User</h3>
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full flex items-center justify-content-center text-white font-bold
                                {{ $auditLog->user?->role === 'admin' ? 'bg-gray-800' : ($auditLog->user?->role === 'specialist' ? 'bg-purple-600' : 'bg-blue-600') }}"
                                 style="display:flex;align-items:center;justify-content:center">
                                {{ strtoupper(substr($auditLog->user?->name ?? 'S', 0, 1)) }}
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-800 dark:text-white">
                                    {{ $auditLog->user?->name ?? 'System' }}
                                </p>
                                <p class="text-xs text-gray-400">
                                    {{ $auditLog->user?->email ?? '—' }}
                                    <span class="mx-1">•</span>
                                    <span class="capitalize">{{ $auditLog->user?->role ?? '—' }}</span>
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Action --}}
                    <div>
                        <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-2">Action</h3>
                        <p class="text-sm font-mono text-gray-800 dark:text-white bg-gray-50 dark:bg-gray-700 px-3 py-2 rounded-lg">
                            {{ $auditLog->action }}
                        </p>
                    </div>

                    {{-- Description --}}
                    <div>
                        <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-2">Description</h3>
                        <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed bg-gray-50 dark:bg-gray-700 px-3 py-2 rounded-lg">
                            {{ $auditLog->description ?? 'No description recorded.' }}
                        </p>
                    </div>

                    {{-- Affected Record --}}
                    @if($auditLog->model_type)
                    <div>
                        <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-2">Affected Record</h3>
                        <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 rounded-lg flex gap-6 text-sm">
                            <div>
                                <p class="text-xs text-gray-400 mb-0.5">Model</p>
                                <p class="font-semibold text-gray-800 dark:text-white">{{ $auditLog->model_type }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 mb-0.5">ID</p>
                                <p class="font-semibold text-gray-800 dark:text-white">#{{ $auditLog->model_id }}</p>
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- Technical info --}}
                    <div>
                        <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-2">Technical Details</h3>
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg overflow-hidden">
                            <dl class="divide-y divide-gray-100 dark:divide-gray-600">
                                <div class="flex justify-between px-4 py-2.5">
                                    <dt class="text-xs text-gray-500">IP Address</dt>
                                    <dd class="text-xs font-mono text-gray-700 dark:text-gray-300">
                                        {{ $auditLog->ip_address ?? '—' }}
                                    </dd>
                                </div>
                                <div class="flex justify-between px-4 py-2.5">
                                    <dt class="text-xs text-gray-500">Timestamp</dt>
                                    <dd class="text-xs text-gray-700 dark:text-gray-300">
                                        {{ $auditLog->created_at->format('d M Y H:i:s') }}
                                        <span class="text-gray-400 ml-1">({{ $auditLog->created_at->diffForHumans() }})</span>
                                    </dd>
                                </div>
                                <div class="px-4 py-2.5">
                                    <dt class="text-xs text-gray-500 mb-1">User Agent</dt>
                                    <dd class="text-xs text-gray-600 dark:text-gray-400 break-all leading-relaxed">
                                        {{ $auditLog->user_agent ?? '—' }}
                                    </dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                </div>

                {{-- Navigation between logs --}}
                <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700 flex justify-between">
                    @php
                        $prev = \App\Models\AuditLog::where('id', '<', $auditLog->id)->latest('id')->first();
                        $next = \App\Models\AuditLog::where('id', '>', $auditLog->id)->oldest('id')->first();
                    @endphp

                    @if($prev)
                        <a href="{{ route('admin.audit-logs.show', $prev) }}"
                           class="text-xs text-blue-600 hover:underline">
                            &larr; Previous log
                        </a>
                    @else
                        <span></span>
                    @endif

                    @if($next)
                        <a href="{{ route('admin.audit-logs.show', $next) }}"
                           class="text-xs text-blue-600 hover:underline">
                            Next log &rarr;
                        </a>
                    @endif
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
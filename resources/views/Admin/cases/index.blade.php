{{-- resources/views/admin/cases/index.blade.php --}}

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Case Management
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-800 rounded-lg text-sm">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Stat tabs --}}
            <div class="grid grid-cols-2 sm:grid-cols-5 gap-3 mb-5">
                @php
                    $tabs = [
                        ''              => ['label' => 'All Cases',     'count' => $counts['all'],           'color' => 'gray'],
                        'open'          => ['label' => 'Open',          'count' => $counts['open'],          'color' => 'blue'],
                        'assigned'      => ['label' => 'Assigned',      'count' => $counts['assigned'],      'color' => 'purple'],
                        'in_discussion' => ['label' => 'In Discussion', 'count' => $counts['in_discussion'], 'color' => 'orange'],
                        'resolved'      => ['label' => 'Resolved',      'count' => $counts['resolved'],      'color' => 'green'],
                    ];
                    $colorMap = [
                        'gray'   => 'border-gray-300 bg-white text-gray-700',
                        'blue'   => 'border-blue-400 bg-blue-50 text-blue-800',
                        'purple' => 'border-purple-400 bg-purple-50 text-purple-800',
                        'orange' => 'border-orange-400 bg-orange-50 text-orange-800',
                        'green'  => 'border-green-400 bg-green-50 text-green-800',
                    ];
                @endphp
                @foreach($tabs as $status => $tab)
                <a href="{{ route('admin.cases.index', $status ? ['status' => $status] : []) }}"
                   class="rounded-xl border p-3 text-center transition hover:shadow-sm
                       {{ request('status') === $status ? $colorMap[$tab['color']] . ' shadow-sm' : 'border-gray-200 bg-white text-gray-500 hover:bg-gray-50' }}">
                    <p class="text-2xl font-bold">{{ $tab['count'] }}</p>
                    <p class="text-xs mt-0.5 font-medium">{{ $tab['label'] }}</p>
                </a>
                @endforeach
            </div>

            {{-- Filter bar --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 mb-4">
                <form method="GET" action="{{ route('admin.cases.index') }}"
                      class="flex flex-wrap gap-3 items-end">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Urgency</label>
                        <select name="urgency"
                                class="rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                            <option value="">All urgencies</option>
                            @foreach(['low','medium','high','critical'] as $u)
                                <option value="{{ $u }}" {{ request('urgency') === $u ? 'selected' : '' }}>
                                    {{ ucfirst($u) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit"
                            class="px-4 py-2 bg-gray-800 hover:bg-gray-900 text-white text-sm font-medium rounded-lg transition">
                        Filter
                    </button>
                    @if(request()->hasAny(['status','urgency']))
                    <a href="{{ route('admin.cases.index') }}"
                       class="px-4 py-2 border border-gray-300 text-gray-600 hover:bg-gray-50 text-sm font-medium rounded-lg transition">
                        Clear
                    </a>
                    @endif
                </form>
            </div>

            {{-- Table --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Case</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Patient</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Doctor</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Specialization</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Urgency</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Assigned To</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @php
                            $urgencyClasses = ['low'=>'bg-green-100 text-green-800','medium'=>'bg-yellow-100 text-yellow-800','high'=>'bg-orange-100 text-orange-800','critical'=>'bg-red-100 text-red-800'];
                            $statusClasses  = ['open'=>'bg-blue-100 text-blue-800','assigned'=>'bg-purple-100 text-purple-800','in_discussion'=>'bg-orange-100 text-orange-800','resolved'=>'bg-green-100 text-green-800','closed'=>'bg-gray-100 text-gray-600'];
                        @endphp
                        @forelse($cases as $case)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition
                            {{ $case->urgency === 'critical' ? 'border-l-4 border-red-500' : '' }}
                            {{ $case->urgency === 'high' ? 'border-l-4 border-orange-400' : '' }}">
                            <td class="px-6 py-4">
                                <p class="text-xs font-mono font-bold text-gray-700 dark:text-gray-200">
                                    {{ $case->case_number }}
                                </p>
                                <p class="text-xs text-gray-400 mt-0.5 max-w-[140px] truncate">
                                    {{ $case->title }}
                                </p>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">
                                <p class="font-medium">{{ $case->patient->full_name }}</p>
                                <p class="text-xs text-gray-400 font-mono">{{ $case->patient->patient_uid }}</p>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">
                                {{ $case->postedBy?->name ?? '—' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">
                                {{ $case->specialization?->name ?? '—' }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-medium {{ $urgencyClasses[$case->urgency] ?? '' }}">
                                    {{ ucfirst($case->urgency) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusClasses[$case->status] ?? '' }}">
                                    {{ $case->status_label }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">
                                {{ $case->activeAssignment?->specialist?->name ?? '—' }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex gap-1">
                                    <a href="{{ route('admin.cases.show', $case) }}"
                                       class="text-xs px-2.5 py-1 rounded border border-blue-300 text-blue-600 hover:bg-blue-50 transition">
                                        View
                                    </a>
                                    @if(in_array($case->status, ['open', 'assigned']))
                                    <a href="{{ route('admin.cases.assign', $case) }}"
                                       class="text-xs px-2.5 py-1 rounded border border-purple-300 text-purple-600 hover:bg-purple-50 transition">
                                        {{ $case->status === 'open' ? 'Assign' : 'Reassign' }}
                                    </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-sm text-gray-400">
                                No cases found.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="px-6 py-4 border-t border-gray-100">
                    {{ $cases->links() }}
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
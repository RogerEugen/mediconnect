{{-- resources/views/specialist/cases/index.blade.php --}}

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            My Assigned Cases
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
            @php
                $tabs = [
                    ''            => ['label' => 'All',         'count' => $counts['all']],
                    'pending'     => ['label' => 'Pending',     'count' => $counts['pending']],
                    'in_progress' => ['label' => 'In Progress', 'count' => $counts['in_progress']],
                    'completed'   => ['label' => 'Completed',   'count' => $counts['completed']],
                    'declined'    => ['label' => 'Declined',    'count' => $counts['declined']],
                ];
            @endphp
            <div class="flex flex-wrap gap-2 mb-5">
                @foreach($tabs as $status => $tab)
                <a href="{{ route('specialist.cases.index', $status ? ['status' => $status] : []) }}"
                   class="px-4 py-2 text-sm font-medium rounded-lg transition
                       {{ request('status') === $status ? 'bg-purple-600 text-white' : 'bg-white border border-gray-300 text-gray-600 hover:bg-gray-50' }}">
                    {{ $tab['label'] }}
                    <span class="ml-1.5 px-1.5 py-0.5 text-xs rounded-full
                        {{ request('status') === $status ? 'bg-purple-500 text-white' : 'bg-gray-100 text-gray-500' }}">
                        {{ $tab['count'] }}
                    </span>
                </a>
                @endforeach
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Case</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Patient</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Doctor</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Urgency</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Assignment</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Due</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @php
                            $urgencyClasses    = ['low'=>'bg-green-100 text-green-800','medium'=>'bg-yellow-100 text-yellow-800','high'=>'bg-orange-100 text-orange-800','critical'=>'bg-red-100 text-red-800'];
                            $assignmentClasses = ['pending'=>'bg-yellow-100 text-yellow-800','accepted'=>'bg-blue-100 text-blue-800','in_progress'=>'bg-orange-100 text-orange-800','completed'=>'bg-green-100 text-green-800','declined'=>'bg-red-100 text-red-800'];
                        @endphp
                        @forelse($assignments as $assignment)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition
                            {{ $assignment->case->urgency === 'critical' ? 'border-l-4 border-red-500' : '' }}
                            {{ $assignment->case->urgency === 'high' ? 'border-l-4 border-orange-400' : '' }}">
                            <td class="px-6 py-4">
                                <p class="text-xs font-mono font-bold text-gray-700 dark:text-gray-200">
                                    {{ $assignment->case->case_number }}
                                </p>
                                <p class="text-xs text-gray-400 mt-0.5 max-w-[140px] truncate">
                                    {{ $assignment->case->title }}
                                </p>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm font-medium text-gray-800 dark:text-white">
                                    {{ $assignment->case->patient->full_name }}
                                </p>
                                <p class="text-xs text-gray-400 font-mono">{{ $assignment->case->patient->patient_uid }}</p>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $assignment->case->postedBy?->name ?? '—' }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-medium {{ $urgencyClasses[$assignment->case->urgency] ?? '' }}">
                                    {{ ucfirst($assignment->case->urgency) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-medium {{ $assignmentClasses[$assignment->status] ?? '' }}">
                                    {{ ucfirst(str_replace('_', ' ', $assignment->status)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-xs
                                {{ $assignment->due_date && $assignment->due_date->isPast() ? 'text-red-600 font-semibold' : 'text-gray-500' }}">
                                {{ $assignment->due_date ? $assignment->due_date->format('d M Y') : '—' }}
                            </td>
                            <td class="px-6 py-4">
                                <a href="{{ route('specialist.cases.show', $assignment->case) }}"
                                   class="text-xs px-2.5 py-1 rounded border border-purple-300 text-purple-600 hover:bg-purple-50 transition">
                                    {{ in_array($assignment->status, ['pending']) ? 'Review' : 'Open' }}
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-sm text-gray-400">
                                No cases found.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="px-6 py-4 border-t border-gray-100">
                    {{ $assignments->links() }}
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
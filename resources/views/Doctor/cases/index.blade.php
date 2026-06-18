{{-- resources/views/doctor/cases/index.blade.php --}}

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            My Posted Cases
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-800 rounded-lg text-sm">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Case</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Patient</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Specialization</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Urgency</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Assigned To</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Posted</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse($cases as $case)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                            <td class="px-6 py-4">
                                <p class="text-xs font-mono font-bold text-gray-700 dark:text-gray-200">
                                    {{ $case->case_number }}
                                </p>
                                <p class="text-xs text-gray-400 mt-0.5 truncate max-w-[160px]">
                                    {{ $case->title }}
                                </p>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">
                                <p class="font-medium">{{ $case->patient->full_name }}</p>
                                <p class="text-xs text-gray-400 font-mono">{{ $case->patient->patient_uid }}</p>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">
                                {{ $case->specialization?->name ?? '—' }}
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $urgencyClasses = [
                                        'low'      => 'bg-green-100 text-green-800',
                                        'medium'   => 'bg-yellow-100 text-yellow-800',
                                        'high'     => 'bg-orange-100 text-orange-800',
                                        'critical' => 'bg-red-100 text-red-800',
                                    ];
                                @endphp
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-medium {{ $urgencyClasses[$case->urgency] ?? 'bg-gray-100 text-gray-600' }}">
                                    {{ ucfirst($case->urgency) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $statusClasses = [
                                        'open'          => 'bg-blue-100 text-blue-800',
                                        'assigned'      => 'bg-purple-100 text-purple-800',
                                        'in_discussion' => 'bg-orange-100 text-orange-800',
                                        'resolved'      => 'bg-green-100 text-green-800',
                                        'closed'        => 'bg-gray-100 text-gray-600',
                                    ];
                                @endphp
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusClasses[$case->status] ?? 'bg-gray-100 text-gray-600' }}">
                                    {{ $case->status_label }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">
                                {{ $case->activeAssignment?->specialist?->name ?? '—' }}
                            </td>
                            <td class="px-6 py-4 text-xs text-gray-400">
                                {{ $case->created_at->diffForHumans() }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex gap-1">
                                    <a href="{{ route('doctor.cases.show', $case) }}"
                                       class="text-xs px-2.5 py-1 rounded border border-blue-300 text-blue-600 hover:bg-blue-50 transition">
                                        View
                                    </a>
                                    @if($case->status === 'open')
                                    <form action="{{ route('doctor.cases.destroy', $case) }}"
                                          method="POST"
                                          onsubmit="return confirm('Delete this case?')">
                                        @csrf @method('DELETE')
                                        <button class="text-xs px-2.5 py-1 rounded border border-red-300 text-red-500 hover:bg-red-50 transition">
                                            Delete
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-sm text-gray-400">
                                You have not posted any cases yet.
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
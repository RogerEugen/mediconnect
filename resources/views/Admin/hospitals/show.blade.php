{{-- resources/views/admin/hospitals/show.blade.php --}}

<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-700 leading-tight">
                {{ $hospital->name }}
            </h2>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.hospitals.edit', $hospital) }}"
                   class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition">
                    Edit
                </a>
                <a href="{{ route('admin.hospitals.index') }}"
                   class="text-sm text-gray-600 hover:text-gray-900 dark:text-gray-400 transition">
                    &larr; Back
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">

                {{-- Details --}}
                <div class="lg:col-span-2">
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
                        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200 mb-4 uppercase tracking-wide">
                            Hospital Details
                        </h3>
                        <dl class="space-y-3 text-sm">
                            <div class="flex justify-between">
                                <dt class="text-gray-500">Name</dt>
                                <dd class="font-medium text-gray-800 dark:text-white text-right">{{ $hospital->name }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-gray-500">Address</dt>
                                <dd class="font-medium text-gray-800 dark:text-white text-right">{{ $hospital->address ?? '—' }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-gray-500">Phone</dt>
                                <dd class="font-medium text-gray-800 dark:text-white">{{ $hospital->phone ?? '—' }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-gray-500">Email</dt>
                                <dd class="font-medium text-gray-800 dark:text-white">{{ $hospital->email ?? '—' }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-gray-500">License</dt>
                                <dd class="font-medium text-gray-800 dark:text-white">{{ $hospital->license_number ?? '—' }}</dd>
                            </div>
                            <div class="flex justify-between items-center">
                                <dt class="text-gray-500">Status</dt>
                                <dd>
                                    @if($hospital->is_active)
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Active</span>
                                    @else
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">Inactive</span>
                                    @endif
                                </dd>
                            </div>
                        </dl>

                        <div class="mt-6 pt-4 border-t border-gray-100 space-y-2">
                            <form action="{{ route('admin.hospitals.toggle', $hospital) }}" method="POST">
                                @csrf @method('PATCH')
                                <button class="w-full py-2 text-sm font-medium rounded-lg border
                                    {{ $hospital->is_active
                                        ? 'border-yellow-300 text-yellow-700 hover:bg-yellow-50'
                                        : 'border-green-300 text-green-700 hover:bg-green-50' }} transition">
                                    {{ $hospital->is_active ? 'Deactivate Hospital' : 'Activate Hospital' }}
                                </button>
                            </form>
                            <form action="{{ route('admin.hospitals.destroy', $hospital) }}" method="POST"
                                  onsubmit="return confirm('Delete this hospital?')">
                                @csrf @method('DELETE')
                                <button class="w-full py-2 text-sm font-medium rounded-lg border border-red-300 text-red-600 hover:bg-red-50 transition">
                                    Delete Hospital
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- Staff --}}
                <div class="lg:col-span-3">
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wide">
                                Staff Assigned ({{ $hospital->users->count() }})
                            </h3>
                        </div>
                        <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Role</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                @forelse($hospital->users as $user)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                    <td class="px-6 py-3 text-sm font-medium text-gray-800 dark:text-white">
                                        {{ $user->name }}
                                    </td>
                                    <td class="px-6 py-3">
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-medium
                                            {{ $user->role === 'doctor' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                                            {{ ucfirst($user->role) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-3 text-sm text-gray-600 dark:text-gray-300">{{ $user->email }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-8 text-center text-sm text-gray-400">
                                        No staff assigned to this hospital yet.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
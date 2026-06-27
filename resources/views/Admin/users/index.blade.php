{{-- resources/views/admin/users/index.blade.php --}}

<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-700 leading-tight">
                User Management
            </h2>
            <a href="{{ route('admin.users.create') }}"
               class="inline-flex items-center px-4 py-2 bg-gray-800 hover:bg-gray-900 dark:bg-gray-700 dark:hover:bg-gray-600 text-white text-sm font-medium rounded-lg transition">
                + Add User
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-800 rounded-lg text-sm">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-800 rounded-lg text-sm">
                    {{ session('error') }}
                </div>
            @endif

            {{-- Role shortcuts --}}
            <div class="mb-4 flex flex-wrap gap-2">
                <a href="{{ route('admin.users.index', [...request()->except(['status', 'page']), 'status' => 'pending']) }}"
                   class="inline-flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-bold transition
                       {{ request('status') === 'pending' ? 'bg-amber-500 text-white shadow-sm' : 'border border-amber-300 bg-amber-50 text-amber-800 hover:bg-amber-100' }}">
                    <span class="h-2 w-2 rounded-full bg-current"></span>
                    Pending verification ({{ $roleCounts['pending'] }})
                </a>
                <a href="{{ route('admin.users.index', request()->except(['role', 'page'])) }}"
                   class="px-4 py-2 text-sm font-medium rounded-lg transition
                       {{ !request('role') ? 'bg-gray-800 text-white' : 'bg-white border border-gray-300 text-gray-600 hover:bg-gray-50' }}">
                    All ({{ $roleCounts['all'] }})
                </a>
                <a href="{{ route('admin.users.index', [...request()->except(['role', 'page']), 'role' => 'doctor']) }}"
                   class="px-4 py-2 text-sm font-medium rounded-lg transition
                       {{ request('role') === 'doctor' ? 'bg-blue-600 text-white' : 'bg-white border border-gray-300 text-gray-600 hover:bg-gray-50' }}">
                    Doctors ({{ $roleCounts['doctor'] }})
                </a>
                <a href="{{ route('admin.users.index', [...request()->except(['role', 'page']), 'role' => 'specialist']) }}"
                   class="px-4 py-2 text-sm font-medium rounded-lg transition
                       {{ request('role') === 'specialist' ? 'bg-purple-600 text-white' : 'bg-white border border-gray-300 text-gray-600 hover:bg-gray-50' }}">
                    Specialists ({{ $roleCounts['specialist'] }})
                </a>
            </div>

            <form method="GET" class="mb-5 rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <div class="grid gap-3 md:grid-cols-2 lg:grid-cols-5">
                    <input name="search" value="{{ request('search') }}" placeholder="Search name or email..."
                           class="rounded-lg border-gray-300 text-sm dark:border-gray-600 dark:bg-gray-900 dark:text-white">
                    <select name="role" class="rounded-lg border-gray-300 text-sm dark:border-gray-600 dark:bg-gray-900 dark:text-white">
                        <option value="">All professions</option>
                        <option value="doctor" @selected(request('role') === 'doctor')>Doctors</option>
                        <option value="specialist" @selected(request('role') === 'specialist')>Specialists</option>
                    </select>
                    <select name="specialization" class="rounded-lg border-gray-300 text-sm dark:border-gray-600 dark:bg-gray-900 dark:text-white">
                        <option value="">All specialties</option>
                        @foreach($specializations as $specialization)
                            <option value="{{ $specialization->id }}" @selected(request('specialization') == $specialization->id)>{{ $specialization->name }}</option>
                        @endforeach
                    </select>
                    <select name="hospital" class="rounded-lg border-gray-300 text-sm dark:border-gray-600 dark:bg-gray-900 dark:text-white">
                        <option value="">All hospitals</option>
                        @foreach($hospitals as $hospital)
                            <option value="{{ $hospital->id }}" @selected(request('hospital') == $hospital->id)>{{ $hospital->name }}</option>
                        @endforeach
                    </select>
                    <select name="status" class="rounded-lg border-gray-300 text-sm dark:border-gray-600 dark:bg-gray-900 dark:text-white">
                        <option value="">Any account status</option>
                        <option value="active" @selected(request('status') === 'active')>Active</option>
                        <option value="pending" @selected(request('status') === 'pending')>Pending verification</option>
                        <option value="inactive" @selected(request('status') === 'inactive')>Inactive / no ID</option>
                    </select>
                </div>
                <div class="mt-3 flex flex-wrap items-center gap-3">
                    <button class="rounded-lg bg-gray-900 px-5 py-2 text-sm font-semibold text-white hover:bg-gray-700 dark:bg-blue-600 dark:hover:bg-blue-700">Apply filters</button>
                    @if(request()->hasAny(['search', 'role', 'specialization', 'hospital', 'status']))
                        <a href="{{ route('admin.users.index') }}" class="text-sm font-semibold text-blue-600 hover:underline">Clear filters</a>
                    @endif
                </div>
            </form>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hospital</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Specialization</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse($users as $user)
                        <tr class="transition {{ !$user->is_active && $user->profile?->staff_card_path ? 'bg-amber-50/70 hover:bg-amber-50 dark:bg-amber-950/10' : 'hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $loop->iteration }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-content-center text-white text-sm font-bold flex-shrink-0"
                                         style="display:flex;align-items:center;justify-content:center">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <span class="text-sm font-semibold text-gray-800 dark:text-white">
                                        {{ $user->name }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">{{ $user->email }}</td>
                            <td class="px-6 py-4">
                                @if($user->role === 'doctor')
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Doctor</span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">Specialist</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">
                                {{ $user->hospital?->name ?? '—' }}
                            </td>
                            <td class="px-6 py-4">
                                @if($user->specializations->count())
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($user->specializations as $spec)
                                            <span class="px-2 py-0.5 text-xs rounded-full border border-gray-300 text-gray-600 bg-gray-50">
                                                {{ $spec->name }}
                                                @if($spec->pivot->is_primary)
                                                    <span class="text-green-600">★</span>
                                                @endif
                                            </span>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-gray-400 text-sm">—</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($user->is_active)
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Active</span>
                                @elseif($user->profile?->staff_card_path)
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-100 px-2.5 py-1 text-xs font-bold text-amber-800">
                                        <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span> Pending approval
                                    </span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">Inactive</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap gap-1">
                                    <a href="{{ route('admin.users.show', $user) }}"
                                       class="text-xs px-2.5 py-1 rounded border border-blue-300 text-blue-600 hover:bg-blue-50 transition">
                                        {{ !$user->is_active && $user->profile?->staff_card_path ? 'Review ID' : 'View' }}
                                    </a>
                                    <a href="{{ route('admin.users.edit', $user) }}"
                                       class="text-xs px-2.5 py-1 rounded border border-gray-300 text-gray-600 hover:bg-gray-50 transition">Edit</a>
                                    @if(!$user->is_active && $user->profile?->staff_card_path)
                                    <form action="{{ route('admin.users.approve', $user) }}" method="POST" onsubmit="return confirm('Approve this clinician after reviewing the staff ID?')">
                                        @csrf @method('PATCH')
                                        <button class="rounded bg-emerald-600 px-2.5 py-1 text-xs font-bold text-white transition hover:bg-emerald-700">
                                            Approve
                                        </button>
                                    </form>
                                    @else
                                        <form action="{{ route('admin.users.toggle', $user) }}" method="POST">
                                            @csrf @method('PATCH')
                                            <button class="text-xs px-2.5 py-1 rounded border border-yellow-300 text-yellow-700 hover:bg-yellow-50 transition">
                                                {{ $user->is_active ? 'Deactivate' : 'Activate' }}
                                            </button>
                                        </form>
                                    @endif
                                    <form action="{{ route('admin.users.reset-password', $user) }}" method="POST"
                                          onsubmit="return confirm('Reset password to Password@123?')">
                                        @csrf @method('PATCH')
                                        <button class="text-xs px-2.5 py-1 rounded border border-gray-300 text-gray-500 hover:bg-gray-50 transition">
                                            Reset Pass
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST"
                                          onsubmit="return confirm('Deactivate this user and preserve their clinical history?')">
                                        @csrf @method('DELETE')
                                        <button class="text-xs px-2.5 py-1 rounded border border-red-300 text-red-600 hover:bg-red-50 transition">
                                            Deactivate
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="px-6 py-10 text-center text-sm text-gray-400">
                                No users found.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="px-6 py-4 border-t border-gray-100">
                    {{ $users->links() }}
                </div>
            </div>

        </div>
    </div>
</x-app-layout>

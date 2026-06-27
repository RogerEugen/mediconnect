{{-- resources/views/admin/users/show.blade.php --}}

<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-700 leading-tight">
                User Profile
            </h2>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.users.edit', $user) }}"
                   class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition">
                    Edit
                </a>
                <a href="{{ route('admin.users.index') }}"
                   class="text-sm text-gray-600 hover:text-gray-900 dark:text-gray-400 transition">
                    &larr; Back
                </a>
            </div>
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
                <div class="mb-5 rounded-xl border border-red-200 bg-red-50 p-4 text-sm font-medium text-red-800">
                    {{ session('error') }}
                </div>
            @endif

            @if(!$user->is_active && $user->profile?->staff_card_path)
                <div class="mb-6 flex flex-col gap-4 rounded-2xl border border-amber-200 bg-gradient-to-r from-amber-50 to-orange-50 p-5 shadow-sm sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-[0.18em] text-amber-700">Approval required</p>
                        <h3 class="mt-1 text-lg font-bold text-slate-900">Review this clinician's staff ID</h3>
                        <p class="mt-1 text-sm text-slate-600">The account cannot sign in until you approve it.</p>
                    </div>
                    <form action="{{ route('admin.users.approve', $user) }}" method="POST" onsubmit="return confirm('I have reviewed the staff ID. Approve this clinician?')">
                        @csrf @method('PATCH')
                        <button class="w-full rounded-xl bg-emerald-600 px-6 py-3 text-sm font-bold text-white shadow-lg shadow-emerald-600/20 transition hover:bg-emerald-700 sm:w-auto">
                            Approve account
                        </button>
                    </form>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- Profile card --}}
                <div class="lg:col-span-1">
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">

                        <div class="text-center mb-6">
                            <div class="w-20 h-20 rounded-full bg-blue-600 flex items-center justify-content-center text-white text-3xl font-bold mx-auto mb-3"
                                 style="display:flex;align-items:center;justify-content:center">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-white">
                                {{ $user->name }}
                            </h3>
                            <div class="mt-1 flex justify-center gap-2 flex-wrap">
                                @if($user->role === 'doctor')
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Doctor</span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">Specialist</span>
                                @endif
                                @if($user->is_active)
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Active</span>
                                @elseif($user->profile?->staff_card_path)
                                    <span class="rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-bold text-amber-800">Pending approval</span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">Inactive</span>
                                @endif
                            </div>
                        </div>

                        <dl class="space-y-3 text-sm border-t border-gray-100 pt-4">
                            <div class="flex justify-between">
                                <dt class="text-gray-500">Email</dt>
                                <dd class="font-medium text-gray-800 dark:text-white text-right">{{ $user->email }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-gray-500">Hospital</dt>
                                <dd class="font-medium text-gray-800 dark:text-white">{{ $user->hospital?->name ?? '—' }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-gray-500">Joined</dt>
                                <dd class="font-medium text-gray-800 dark:text-white">{{ $user->created_at->format('d M Y') }}</dd>
                            </div>
                            @if($user->approved_at)
                                <div class="flex justify-between gap-4">
                                    <dt class="text-gray-500">Approved</dt>
                                    <dd class="text-right font-medium text-gray-800 dark:text-white">
                                        {{ $user->approved_at->format('d M Y, H:i') }}<br>
                                        <span class="text-xs text-gray-500">by {{ $user->approvedBy?->name ?? 'Administrator' }}</span>
                                    </dd>
                                </div>
                            @endif
                        </dl>

                        <div class="mt-5 rounded-lg border border-gray-200 bg-gray-50 p-4">
                            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Staff verification</p>
                            @if($user->profile?->staff_card_path)
                                <a href="{{ route('admin.users.staff-card', $user) }}"
                                   class="mt-2 inline-flex text-sm font-semibold text-blue-600 hover:underline">
                                    View staff card ({{ $user->profile->staff_card_original_name ?? 'uploaded file' }})
                                </a>
                            @else
                                <p class="mt-2 text-sm text-amber-700">No staff card uploaded (account created by admin).</p>
                            @endif
                        </div>

                        <div class="mt-5 pt-4 border-t border-gray-100 space-y-2">
                            @if(!(!$user->is_active && $user->profile?->staff_card_path))
                            <form action="{{ route('admin.users.toggle', $user) }}" method="POST">
                                @csrf @method('PATCH')
                                <button class="w-full py-2 text-sm font-medium rounded-lg border transition
                                    {{ $user->is_active ? 'border-yellow-300 text-yellow-700 hover:bg-yellow-50' : 'border-green-300 text-green-700 hover:bg-green-50' }}">
                                    {{ $user->is_active ? 'Deactivate Account' : 'Activate Account' }}
                                </button>
                            </form>
                            @endif
                            <form action="{{ route('admin.users.reset-password', $user) }}" method="POST"
                                  onsubmit="return confirm('Reset password to Password@123?')">
                                @csrf @method('PATCH')
                                <button class="w-full py-2 text-sm font-medium rounded-lg border border-gray-300 text-gray-600 hover:bg-gray-50 transition">
                                    Reset Password
                                </button>
                            </form>
                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST"
                                  onsubmit="return confirm('Deactivate this user and preserve their clinical history?')">
                                @csrf @method('DELETE')
                                <button class="w-full py-2 text-sm font-medium rounded-lg border border-red-300 text-red-600 hover:bg-red-50 transition">
                                    Deactivate & Preserve History
                                </button>
                            </form>
                        </div>

                    </div>
                </div>

                {{-- Right cards --}}
                <div class="lg:col-span-2 space-y-6">

                    @if($user->profile?->staff_card_path)
                    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-800">
                        <div class="flex flex-col gap-3 border-b border-slate-200 px-6 py-5 sm:flex-row sm:items-center sm:justify-between dark:border-slate-700">
                            <div>
                                <p class="text-xs font-bold uppercase tracking-[0.18em] text-blue-600">Identity verification</p>
                                <h3 class="mt-1 font-bold text-slate-900 dark:text-white">Hospital staff ID</h3>
                                <p class="mt-1 text-xs text-slate-500">{{ $user->profile->staff_card_original_name }}</p>
                            </div>
                            <a href="{{ route('admin.users.staff-card', $user) }}" target="_blank"
                               class="rounded-lg border border-blue-200 bg-blue-50 px-4 py-2 text-center text-sm font-bold text-blue-700 hover:bg-blue-100">
                                Open full size
                            </a>
                        </div>
                        <div class="bg-slate-100 p-3 dark:bg-slate-900">
                            @if(str_ends_with(strtolower($user->profile->staff_card_original_name ?? ''), '.pdf'))
                                <iframe src="{{ route('admin.users.staff-card', $user) }}" title="Staff ID for {{ $user->name }}" class="h-[560px] w-full rounded-lg bg-white"></iframe>
                            @else
                                <div class="flex min-h-[360px] items-center justify-center overflow-hidden rounded-lg bg-white p-4">
                                    <img src="{{ route('admin.users.staff-card', $user) }}" alt="Staff ID for {{ $user->name }}" class="max-h-[560px] w-auto rounded-lg object-contain shadow-sm">
                                </div>
                            @endif
                        </div>
                    </div>
                    @endif

                    @if($user->role === 'specialist')
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
                        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wide mb-4">
                            Specializations
                        </h3>
                        @forelse($user->specializations as $spec)
                            <div class="flex items-center justify-between p-3 mb-2 border border-gray-100 rounded-lg hover:bg-gray-50 transition">
                                <span class="text-sm font-medium text-gray-800 dark:text-white">{{ $spec->name }}</span>
                                @if($spec->pivot->is_primary)
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">★ Primary</span>
                                @endif
                            </div>
                        @empty
                            <p class="text-sm text-gray-400">No specializations assigned.</p>
                        @endforelse
                    </div>
                    @endif

                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
                        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wide mb-4">
                            Hospital Affiliations
                        </h3>
                        @forelse($user->hospitals as $hospital)
                            <div class="flex items-center justify-between p-3 mb-2 border border-gray-100 rounded-lg hover:bg-gray-50 transition">
                                <span class="text-sm font-medium text-gray-800 dark:text-white">{{ $hospital->name }}</span>
                                @if($hospital->pivot->is_primary)
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Primary</span>
                                @endif
                            </div>
                        @empty
                            <p class="text-sm text-gray-400">No hospital affiliations found.</p>
                        @endforelse
                    </div>

                </div>

            </div>
        </div>
    </div>
</x-app-layout>

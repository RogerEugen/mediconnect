{{-- resources/views/notifications/index.blade.php --}}

<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Notifications
            </h2>
            <div class="flex gap-2">
                <form action="{{ route('notifications.mark-all-read') }}" method="POST">
                    @csrf
                    <button class="px-4 py-2 border border-gray-300 text-gray-600 hover:bg-gray-50 text-sm font-medium rounded-lg transition">
                        Mark all read
                    </button>
                </form>
                <form action="{{ route('notifications.clear') }}" method="POST"
                      onsubmit="return confirm('Clear all notifications?')">
                    @csrf @method('DELETE')
                    <button class="px-4 py-2 border border-red-300 text-red-600 hover:bg-red-50 text-sm font-medium rounded-lg transition">
                        Clear all
                    </button>
                </form>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-800 rounded-lg text-sm">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">

                @forelse($notifications as $notification)
                <div class="flex gap-4 px-5 py-4 border-b border-gray-50 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition
                    {{ !$notification->is_read ? 'bg-blue-50 dark:bg-blue-900/10' : '' }}">

                    {{-- Icon --}}
                    <div class="flex-shrink-0 mt-0.5">
                        @php
                            $colorMap = [
                                'blue'   => 'bg-blue-500',
                                'purple' => 'bg-purple-500',
                                'green'  => 'bg-green-500',
                                'red'    => 'bg-red-500',
                                'orange' => 'bg-orange-500',
                                'gray'   => 'bg-gray-400',
                            ];
                            $bgClass = $colorMap[$notification->color] ?? 'bg-gray-400';
                        @endphp
                        <div class="w-10 h-10 rounded-full {{ $bgClass }} flex items-center justify-content-center text-white"
                             style="display:flex;align-items:center;justify-content:center">
                            @if($notification->type === 'new_case' || $notification->type === 'case_assigned')
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            @elseif($notification->type === 'new_discussion')
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                            </svg>
                            @elseif(in_array($notification->type, ['case_completed','case_resolved','case_accepted']))
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            @elseif($notification->type === 'case_declined')
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            @else
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                            @endif
                        </div>
                    </div>

                    {{-- Content --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex justify-between items-start gap-2">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-gray-800 dark:text-white
                                    {{ !$notification->is_read ? 'text-blue-900 dark:text-blue-100' : '' }}">
                                    {{ $notification->title }}
                                </p>
                                <p class="text-sm text-gray-600 dark:text-gray-300 mt-0.5 leading-relaxed">
                                    {{ $notification->message }}
                                </p>
                                <p class="text-xs text-gray-400 mt-1.5">
                                    {{ $notification->created_at->timezone(config('app.timezone'))->format('d M Y H:i') }}
                                    <span class="mx-1">•</span>
                                    {{ $notification->created_at->diffForHumans() }}
                                </p>
                            </div>
                            <div class="flex items-center gap-2 flex-shrink-0">
                                @if(!$notification->is_read)
                                    <span class="w-2 h-2 bg-blue-500 rounded-full"></span>
                                @endif
                                <form action="{{ route('notifications.destroy', $notification) }}"
                                      method="POST">
                                    @csrf @method('DELETE')
                                    <button class="text-gray-300 hover:text-red-500 transition text-sm">✕</button>
                                </form>
                            </div>
                        </div>
                    </div>

                </div>
                @empty
                <div class="px-5 py-16 text-center">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-content-center mx-auto mb-4"
                         style="display:flex;align-items:center;justify-content:center">
                        <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor"
                             stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                    </div>
                    <p class="text-gray-500 font-medium">No notifications yet</p>
                    <p class="text-gray-400 text-sm mt-1">You'll see notifications here when there's activity on your cases.</p>
                </div>
                @endforelse

            </div>

            <div class="mt-4">
                {{ $notifications->links() }}
            </div>

        </div>
    </div>
</x-app-layout>

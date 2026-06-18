{{-- resources/views/layouts/doctor.blade.php --}}

<nav x-data="{ open: false }" class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">

            {{-- Logo + Nav links --}}
            <div class="flex">
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('doctor.dashboard') }}" class="text-lg font-bold text-blue-600 dark:text-blue-400">
                        Medi<span class="text-gray-800 dark:text-white">Connect</span>
                    </a>
                </div>

                <div class="hidden space-x-1 sm:-my-px sm:ms-8 sm:flex items-center">
                    <x-nav-link :href="route('doctor.dashboard')" :active="request()->routeIs('doctor.dashboard')">
                        Dashboard
                    </x-nav-link>
                    <x-nav-link :href="route('clinical-cases.index')" :active="request()->routeIs('clinical-cases.*')">
                        Case Discussions
                    </x-nav-link>
                    <x-nav-link :href="route('clinical-cases.index', ['following' => 1])" :active="request()->boolean('following')">
                        Following
                    </x-nav-link>
                </div>
            </div>

            {{-- Right side: bell + user dropdown --}}
            <div class="hidden sm:flex sm:items-center sm:ms-6 gap-2">

                {{-- 🔔 Notification Bell --}}
                <div class="relative" x-data="notificationBell()" x-init="init()">

                    <button @click="toggleDropdown()"
                            class="relative p-2 rounded-lg text-gray-500 hover:text-gray-700 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-700 transition focus:outline-none">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        <span x-show="unreadCount > 0"
                              x-text="unreadCount > 99 ? '99+' : unreadCount"
                              class="absolute -top-1 -right-1 min-w-[18px] h-[18px] px-1 bg-red-500 text-white text-xs font-bold rounded-full flex items-center justify-center"
                              style="display:none">
                        </span>
                    </button>

                    {{-- Dropdown --}}
                    <div x-show="open"
                         @click.outside="open = false"
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="absolute right-0 top-full mt-2 w-80 bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-200 dark:border-gray-700 z-50 overflow-hidden"
                         style="display:none">

                        {{-- Header --}}
                        <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center bg-gray-50 dark:bg-gray-700">
                            <div class="flex items-center gap-2">
                                <h3 class="text-sm font-semibold text-gray-800 dark:text-white">Notifications</h3>
                                <span x-show="unreadCount > 0"
                                      x-text="unreadCount + ' new'"
                                      class="px-1.5 py-0.5 text-xs bg-red-100 text-red-700 font-medium rounded-full">
                                </span>
                            </div>
                            <div class="flex items-center gap-3">
                                <button @click="markAllRead()"
                                        x-show="unreadCount > 0"
                                        class="text-xs text-blue-600 hover:text-blue-800 font-medium">
                                    Mark all read
                                </button>
                                <a href="{{ route('notifications.index') }}"
                                   class="text-xs text-gray-500 hover:text-gray-700">
                                    View all
                                </a>
                            </div>
                        </div>

                        {{-- List --}}
                        <div class="max-h-80 overflow-y-auto divide-y divide-gray-50 dark:divide-gray-700">

                            <template x-if="notifications.length === 0">
                                <div class="px-4 py-8 text-center">
                                    <p class="text-sm text-gray-400">No notifications yet</p>
                                </div>
                            </template>

                            <template x-for="n in notifications" :key="n.id">
                                <div @click="markRead(n)"
                                     class="flex gap-3 px-4 py-3 cursor-pointer transition hover:bg-gray-50 dark:hover:bg-gray-700"
                                     :class="{ 'bg-blue-50 dark:bg-blue-900/20': !n.is_read }">

                                    {{-- Color dot --}}
                                    <div class="flex-shrink-0 mt-1">
                                        <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-bold"
                                             :class="{
                                                'bg-blue-500':   n.color === 'blue',
                                                'bg-purple-500': n.color === 'purple',
                                                'bg-green-500':  n.color === 'green',
                                                'bg-red-500':    n.color === 'red',
                                                'bg-orange-500': n.color === 'orange',
                                                'bg-gray-400':   n.color === 'gray' || !n.color,
                                             }">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                            </svg>
                                        </div>
                                    </div>

                                    {{-- Content --}}
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-semibold text-gray-800 dark:text-white leading-tight truncate"
                                           :class="{ 'text-blue-900': !n.is_read }"
                                           x-text="n.title">
                                        </p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 line-clamp-2"
                                           x-text="n.message">
                                        </p>
                                        <p class="text-xs text-gray-400 mt-1" x-text="n.time"></p>
                                    </div>

                                    {{-- Unread dot --}}
                                    <div x-show="!n.is_read"
                                         class="w-2 h-2 bg-blue-500 rounded-full flex-shrink-0 mt-2">
                                    </div>
                                </div>
                            </template>
                        </div>

                        {{-- Footer --}}
                        <div class="px-4 py-2.5 border-t border-gray-100 dark:border-gray-700 text-center bg-gray-50 dark:bg-gray-700">
                            <a href="{{ route('notifications.index') }}"
                               class="text-xs text-blue-600 hover:text-blue-800 font-medium">
                                See all notifications →
                            </a>
                        </div>
                    </div>
                </div>
                {{-- END Bell --}}

                {{-- User dropdown --}}
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center gap-2 px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition">
                            <div class="w-7 h-7 rounded-full bg-blue-600 flex items-center justify-center text-white text-xs font-bold"
                                 style="display:flex;align-items:center;justify-content:center">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </div>
                            <span>{{ Auth::user()->name }}</span>
                            <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </x-slot>
                    <x-slot name="content">
                        <div class="px-4 py-2 border-b border-gray-100">
                            <p class="text-xs text-gray-400">Signed in as</p>
                            <p class="text-sm font-semibold text-gray-700 truncate">{{ Auth::user()->email }}</p>
                            <span class="inline-block mt-1 px-2 py-0.5 text-xs bg-blue-100 text-blue-800 rounded-full font-medium">Doctor</span>
                        </div>
                        <x-dropdown-link :href="route('profile.edit')">Profile</x-dropdown-link>
                        <x-dropdown-link :href="route('notifications.index')">
                            Notifications
                            @php $unread = Auth::user()->notifications()->where('is_read', false)->count(); @endphp
                            @if($unread > 0)
                                <span class="ml-1 px-1.5 py-0.5 text-xs bg-red-100 text-red-700 rounded-full">{{ $unread }}</span>
                            @endif
                        </x-dropdown-link>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault(); this.closest('form').submit();">
                                Log Out
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            {{-- Mobile hamburger --}}
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = !open"
                        class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none transition">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': !open}" class="inline-flex"
                              stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 6h16M4 12h16M4 18h16"/>
                        <path :class="{'hidden': !open, 'inline-flex': open}" class="hidden"
                              stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- Mobile menu --}}
    <div :class="{'block': open, 'hidden': !open}" class="hidden sm:hidden border-t border-gray-100">
        <div class="pt-2 pb-3 space-y-1 px-4">
            <x-responsive-nav-link :href="route('doctor.dashboard')" :active="request()->routeIs('doctor.dashboard')">Dashboard</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('clinical-cases.index')" :active="request()->routeIs('clinical-cases.*')">Case Discussions</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('clinical-cases.index', ['following' => 1])" :active="request()->boolean('following')">Following</x-responsive-nav-link>
        </div>
        <div class="pt-4 pb-1 border-t border-gray-200 px-4">
            <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
            <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">Profile</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('notifications.index')">Notifications</x-responsive-nav-link>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault(); this.closest('form').submit();">
                        Log Out
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>

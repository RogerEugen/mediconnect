<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'MediConnect') }}</title>
        <script>
            (() => {
                const savedTheme = localStorage.getItem('mediconnect-theme');
                const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                document.documentElement.classList.toggle('dark', savedTheme ? savedTheme === 'dark' : prefersDark);
            })();
        </script>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts & Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100 dark:bg-gray-900">

            {{-- Role-based navigation --}}
            @auth
                @if(Auth::user()->role === 'admin')
                    @include('layouts.admin')
                @elseif(Auth::user()->role === 'doctor')
                    @include('layouts.doctor')
                @elseif(Auth::user()->role === 'specialist')
                    @include('layouts.specialist')
                @endif
            @endauth

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white dark:bg-gray-800 shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>

        {{-- Notification sound --}}
        <audio id="notifSound" src="/sounds/notification.mp3" preload="auto"></audio>

        @auth
        <script>
        function themeSwitcher() {
            return {
                dark: document.documentElement.classList.contains('dark'),

                toggle() {
                    this.dark = !this.dark;
                    document.documentElement.classList.toggle('dark', this.dark);
                    localStorage.setItem('mediconnect-theme', this.dark ? 'dark' : 'light');
                }
            }
        }

        function notificationBell() {
            return {
                open: false,
                unreadCount: 0,
                notifications: [],
                soundEnabled: true,

                init() {
                    this.loadNotifications();
                    this.listenForRealtime();
                    // Fallback poll every 60s
                    setInterval(() => this.loadNotifications(), 60000);
                },

                async loadNotifications() {
                    try {
                        const res = await fetch('/notifications/recent', {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });
                        if (!res.ok) return;
                        this.notifications = await res.json();
                        this.unreadCount = this.notifications.filter(n => !n.is_read).length;
                    } catch (e) {
                        console.error('Notifications load failed:', e);
                    }
                },

                toggleDropdown() {
                    this.open = !this.open;
                    if (this.open) this.loadNotifications();
                },

                async markRead(notification) {
                    if (!notification.is_read) {
                        notification.is_read = true;
                        this.unreadCount = Math.max(0, this.unreadCount - 1);
                        try {
                            await fetch('/notifications/' + notification.id + '/read', {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                    'Accept': 'application/json',
                                }
                            });
                        } catch(e) {}
                    }
                    if (notification.url) {
                        window.location.href = notification.url;
                    }
                },

                async markAllRead() {
                    this.notifications.forEach(n => n.is_read = true);
                    this.unreadCount = 0;
                    try {
                        await fetch('/notifications/mark-all-read', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json',
                            }
                        });
                    } catch(e) {}
                },

                playSound() {
                    if (!this.soundEnabled) return;
                    try {
                        const audio = document.getElementById('notifSound');
                        if (audio) {
                            audio.currentTime = 0;
                            audio.volume = 0.6;
                            audio.play().catch(() => {});
                        }
                    } catch(e) {}
                },

                listenForRealtime() {
                    if (typeof window.Echo === 'undefined') {
                        console.warn('Echo not loaded, using polling only');
                        return;
                    }

                    window.Echo.private('user.{{ auth()->id() }}')
                        .listen('.notification.sent', (data) => {

                            // Add to top of list
                            this.notifications.unshift({
                                id:      data.id,
                                title:   data.title,
                                message: data.message,
                                type:    data.type,
                                color:   data.color ?? 'blue',
                                url:     data.url ?? null,
                                is_read: false,
                                time:    data.created_at,
                            });

                            // Keep max 8 in dropdown
                            if (this.notifications.length > 8) {
                                this.notifications = this.notifications.slice(0, 8);
                            }

                            this.unreadCount++;

                            // Play sound
                            this.playSound();

                            // Show toast
                            this.showToast(data.title, data.message, data.color ?? 'blue');

                            window.dispatchEvent(new CustomEvent('mediconnect:notification', {
                                detail: data
                            }));
                        });
                },

                showToast(title, message, color) {
                    const colorMap = {
                        blue:   'bg-blue-600',
                        purple: 'bg-purple-600',
                        green:  'bg-green-600',
                        red:    'bg-red-600',
                        orange: 'bg-orange-500',
                        gray:   'bg-gray-600',
                    };
                    const bgClass = colorMap[color] || 'bg-blue-600';

                    const toast = document.createElement('div');
                    toast.className = `fixed top-4 right-4 z-[9999] max-w-sm w-full ${bgClass} text-white rounded-xl shadow-2xl p-4 flex gap-3 items-start`;
                    toast.style.cssText = 'transform: translateX(120%); transition: transform 0.3s ease;';
                    toast.innerHTML = `
                        <div class="flex-shrink-0 mt-0.5">
                            <svg class="w-5 h-5 opacity-90" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold leading-tight">${escapeHtml(title)}</p>
                            <p class="text-xs opacity-90 mt-0.5 line-clamp-2">${escapeHtml(message)}</p>
                        </div>
                        <button onclick="this.closest('[data-toast]').remove()"
                                class="flex-shrink-0 opacity-75 hover:opacity-100 text-xl leading-none font-light">&times;</button>
                    `;
                    toast.setAttribute('data-toast', '');

                    document.body.appendChild(toast);

                    // Slide in
                    requestAnimationFrame(() => {
                        requestAnimationFrame(() => {
                            toast.style.transform = 'translateX(0)';
                        });
                    });

                    // Auto dismiss after 5s
                    setTimeout(() => {
                        toast.style.transform = 'translateX(120%)';
                        setTimeout(() => toast.remove(), 300);
                    }, 5000);
                }
            }
        }

        function escapeHtml(str) {
            const d = document.createElement('div');
            d.textContent = str;
            return d.innerHTML;
        }
        </script>
        @endauth

    </body>
</html>

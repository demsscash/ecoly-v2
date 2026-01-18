<!DOCTYPE html>
<html
    lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}"
    data-theme="{{ session('theme', 'light') }}"
>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Ecoly' }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Noto+Kufi+Arabic:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <style>
        /* Custom scrollbar for sidebar */
        .scrollbar-thin::-webkit-scrollbar {
            width: 4px;
        }
        .scrollbar-thin::-webkit-scrollbar-track {
            background: transparent;
        }
        .scrollbar-thin::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 2px;
        }
        .scrollbar-thin::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.3);
        }
    </style>
</head>
<body class="min-h-screen font-sans antialiased bg-base-200">

    {{-- Theme change listener --}}
    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('theme-changed', (data) => {
                document.documentElement.setAttribute('data-theme', data.theme);
            });
        });
    </script>

    <div class="flex min-h-screen">
        {{-- Sidebar --}}
        <livewire:sidebar />

        {{-- Main Content --}}
        <div class="flex-1 flex flex-col min-h-screen">
            {{-- Navbar --}}
            <header class="bg-base-100 shadow-sm sticky top-0 z-30 px-6 py-4">
                <div class="flex items-center justify-between">
                    {{-- Mobile menu button --}}
                    <button
                        x-data="{ open: false }"
                        @click="open = !open"
                        class="lg:hidden p-2 hover:bg-base-200 rounded-lg"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                        </svg>
                    </button>

                    {{-- Page Title (mobile) --}}
                    <h1 class="text-lg font-bold lg:hidden">{{ $title ?? 'Ecoly' }}</h1>

                    <div class="flex-1"></div>

                    {{-- Right side actions --}}
                    <div class="flex items-center gap-2">
                        <livewire:language-switcher />
                        <livewire:theme-toggle />

                        {{-- User dropdown --}}
                        <div class="dropdown dropdown-end">
                            <label tabindex="0" class="btn btn-ghost btn-circle">
                                <div class="w-9 h-9 rounded-full bg-base-300 flex items-center justify-center">
                                    @if(auth()->user()->photo_url)
                                        <img src="{{ auth()->user()->photo_url }}" alt="" class="w-full h-full rounded-full object-cover" />
                                    @else
                                        <span class="text-sm font-medium">{{ auth()->user()->first_name[0] }}</span>
                                    @endif
                                </div>
                            </label>
                            <ul tabindex="0" class="dropdown-content menu p-2 shadow-lg bg-base-100 rounded-xl w-56 mt-4 border border-base-200">
                                <li class="menu-title">
                                    <div class="flex items-center gap-2">
                                        <div>
                                            <div class="font-medium">{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</div>
                                            <div class="text-xs opacity-60">{{ auth()->user()->email }}</div>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <a href="{{ route('profile') }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                                        </svg>
                                        {{ __('My Profile') }}
                                    </a>
                                </li>
                                <li>
                                    <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="text-error">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 9l-3 3m0 0 3 3m-3-3h12.75" />
                                        </svg>
                                        {{ __('Logout') }}
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </header>

            {{-- Page Content --}}
            <main class="flex-1 p-4 lg:p-8">
                {{ $slot }}
            </main>

            {{-- Footer --}}
            <footer class="bg-base-100 border-t border-base-200 py-4 px-6 text-center text-sm text-base-content/60">
                <p>&copy; {{ date('Y') }} Ecoly - {{ __('School Management System') }}</p>
            </footer>
        </div>
    </div>

    {{-- Toast Notifications --}}
    <livewire:toast />

    {{-- Logout form (hidden) --}}
    <form id="logout-form" method="POST" action="{{ route('logout') }}" class="hidden">
        @csrf
    </form>

    @livewireScripts
</body>
</html>

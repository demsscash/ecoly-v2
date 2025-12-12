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
    
    <div class="drawer lg:drawer-open">
        <input id="main-drawer" type="checkbox" class="drawer-toggle" />
        
        {{-- Main Content --}}
        <div class="drawer-content flex flex-col">
            {{-- Navbar --}}
            <div class="navbar bg-base-100 shadow-sm sticky top-0 z-30">
                <div class="flex-none lg:hidden">
                    <label for="main-drawer" class="btn btn-square btn-ghost">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="inline-block w-5 h-5 stroke-current">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </label>
                </div>
                <div class="flex-1">
                    <span class="text-lg font-bold lg:hidden">Ecoly</span>
                </div>
                <div class="flex items-center gap-1">
                    <livewire:language-switcher />
                    <livewire:theme-toggle />
                    
                    <div class="dropdown dropdown-end">
                        <label tabindex="0" class="btn btn-ghost gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                            </svg>
                            <span class="hidden md:inline text-sm">{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</span>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3 h-3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                            </svg>
                        </label>
                        <ul tabindex="0" class="dropdown-content menu p-2 shadow bg-base-100 rounded-box w-52 mt-4">
                            <li><a href="#">{{ __('My Profile') }}</a></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full text-left">{{ __('Logout') }}</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            
            {{-- Page Content --}}
            <main class="flex-1 p-6">
                {{ $slot }}
            </main>
        </div>
        
        {{-- Sidebar --}}
        <div class="drawer-side z-40">
            <label for="main-drawer" class="drawer-overlay"></label>
            <aside class="bg-base-100 w-64 min-h-screen flex flex-col">
                <div class="flex items-center gap-3 px-4 py-6 border-b border-base-200">
                    <div class="w-10 h-10 rounded-lg bg-primary flex items-center justify-center">
                        <span class="text-white font-bold text-lg">E</span>
                    </div>
                    <span class="font-bold text-xl">Ecoly</span>
                </div>
                
                <ul class="menu p-4 flex-1">
                    <li>
                        <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                            </svg>
                            {{ __('Dashboard') }}
                        </a>
                    </li>
                    
                    {{-- Admin Configuration --}}
                    @if(auth()->user()->isAdmin())
                        <li class="menu-title mt-4">
                            <span>{{ __('Configuration') }}</span>
                        </li>
                        <li>
                            <a href="{{ route('admin.school-settings') }}" class="{{ request()->routeIs('admin.school-settings') ? 'active' : '' }}">
                                {{ __('School') }}
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.school-years') }}" class="{{ request()->routeIs('admin.school-years') ? 'active' : '' }}">
                                {{ __('School Years') }}
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.trimesters') }}" class="{{ request()->routeIs('admin.trimesters') ? 'active' : '' }}">
                                {{ __('Trimesters') }}
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.classes') }}" class="{{ request()->routeIs('admin.classes') ? 'active' : '' }}">
                                {{ __('Classes') }}
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.subjects') }}" class="{{ request()->routeIs('admin.subjects') ? 'active' : '' }}">
                                {{ __('Subjects') }}
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.class-subjects') }}" class="{{ request()->routeIs('admin.class-subjects') ? 'active' : '' }}">
                                {{ __('Class Subjects') }}
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.users') }}" class="{{ request()->routeIs('admin.users') ? 'active' : '' }}">
                                {{ __('Users') }}
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.teacher-assignments') }}" class="{{ request()->routeIs('admin.teacher-assignments') ? 'active' : '' }}">
                                {{ __('Assignments') }}
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.grading-config') }}" class="{{ request()->routeIs('admin.grading-config') ? 'active' : '' }}">
                                {{ __('Grading Config') }}
                            </a>
                        </li>
                    @endif
                    
                    {{-- Admin + Secretary Management --}}
                    @if(auth()->user()->isAdmin() || auth()->user()->isSecretary())
                        <li class="menu-title mt-4">
                            <span>{{ __('Management') }}</span>
                        </li>
                        <li>
                            <a href="{{ route('students') }}" class="{{ request()->routeIs('students') ? 'active' : '' }}">
                                {{ __('Students') }}
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('payments') }}" class="{{ request()->routeIs('payments') ? 'active' : '' }}">
                                {{ __('Payments') }}
                            </a>
                        </li>
                    @endif
                    
                    {{-- Teacher Section --}}
                    @if(auth()->user()->isTeacher())
                        <li class="menu-title mt-4">
                            <span>{{ __('Teaching') }}</span>
                        </li>
                        <li>
                            <a href="{{ route('teacher.my-classes') }}" class="{{ request()->routeIs('teacher.my-classes') ? 'active' : '' }}">
                                {{ __('My Classes') }}
                            </a>
                        </li>
                    @endif
                    
                    {{-- Academic Section (All users) --}}
                    <li class="menu-title mt-4">
                        <span>{{ __('Academic') }}</span>
                    </li>
                    <li>
                        <a href="{{ route('grades') }}" class="{{ request()->routeIs('grades') ? 'active' : '' }}">
                            {{ __('Grades') }}
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('class-grades') }}" class="{{ request()->routeIs('class-grades') ? 'active' : '' }}">
                            {{ __('Class Grades') }}
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('bulletins') }}" class="{{ request()->routeIs('bulletins') ? 'active' : '' }}">
                            {{ __('Bulletins') }}
                        </a>
                    </li>
                </ul>
            </aside>
        </div>
    </div>
    
    {{-- Toast Notifications --}}
    <livewire:toast />
    
    @livewireScripts
</body>
</html>

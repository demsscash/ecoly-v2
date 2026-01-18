<aside
    class="bg-neutral text-neutral-content h-screen sticky top-0 flex flex-col transition-all duration-300 {{ $collapsed ? 'w-20' : 'w-72' }} z-40 overflow-hidden"
    x-data="{ collapsed: @js($collapsed) }"
>
    {{-- Header --}}
    <div class="flex items-center justify-between px-4 py-5 border-b border-neutral-700/50">
        @if(!$collapsed)
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-primary flex items-center justify-center shadow-lg">
                    <span class="text-white font-bold text-lg">E</span>
                </div>
                <span class="font-bold text-xl tracking-tight">Ecoly</span>
            </div>
        @else
            <div class="w-9 h-9 rounded-lg bg-primary flex items-center justify-center shadow-lg mx-auto">
                <span class="text-white font-bold text-lg">E</span>
            </div>
        @endif

        <button
            wire:click="toggleCollapse"
            class="p-2 hover:bg-neutral-700/50 rounded-lg transition-colors"
            title="{{ $collapsed ? __('Expand') : __('Collapse') }}"
        >
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                @if($collapsed)
                    <path stroke-linecap="round" stroke-linejoin="round" d="m18.75 4.5 7.5 7.5-7.5 7.5m-6-15L5.25 12l7.5 7.5" />
                @else
                    <path stroke-linecap="round" stroke-linejoin="round" d="m18.75 4.5 7.5 7.5-7.5 7.5m-6-15L5.25 12l7.5 7.5" style="transform: scaleX(-1);" />
                @endif
            </svg>
        </button>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 overflow-y-auto overflow-x-hidden px-3 py-4 space-y-1 scrollbar-thin scrollbar-thumb-neutral-700 scrollbar-track-transparent min-h-0">

        {{-- Dashboard --}}
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all duration-200 {{ request()->routeIs('dashboard') ? 'bg-primary text-white shadow-lg shadow-primary/30' : 'hover:bg-neutral-700/50' }}">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 flex-shrink-0">
                <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
            </svg>
            @if(!$collapsed)
                <span class="font-medium">{{ __('Dashboard') }}</span>
            @endif
        </a>

        {{-- Admin Configuration --}}
        @if(auth()->user()->isAdmin())
            <div class="mt-6">
                <button
                    wire:click="toggleSection('configuration')"
                    class="flex items-center justify-between w-full px-3 py-2 text-sm font-medium text-neutral-400 hover:text-neutral-200 transition-colors"
                >
                    @if(!$collapsed)
                        <span class="uppercase tracking-wider text-xs">{{ __('Configuration') }}</span>
                    @else
                        <span class="sr-only">{{ __('Configuration') }}</span>
                    @endif
                    @if(!$collapsed)
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 transition-transform {{ $this->isSectionCollapsed('configuration') ? '-rotate-90' : '' }}">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                        </svg>
                    @endif
                </button>

                @if($collapsed || !$this->isSectionCollapsed('configuration'))
                    <div class="{{ $collapsed ? 'mt-2 space-y-1' : 'mt-2 space-y-1 pl-2 border-l border-neutral-700/50' }}">
                        <a href="{{ route('admin.school-settings') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition-all duration-200 {{ request()->routeIs('admin.school-settings') ? 'bg-primary text-white shadow-lg shadow-primary/30' : 'hover:bg-neutral-700/50 text-neutral-300' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 flex-shrink-0">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5" />
                            </svg>
                            @if(!$collapsed)<span class="text-sm">{{ __('School') }}</span>@endif
                        </a>

                        <a href="{{ route('admin.school-years') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition-all duration-200 {{ request()->routeIs('admin.school-years') ? 'bg-primary text-white shadow-lg shadow-primary/30' : 'hover:bg-neutral-700/50 text-neutral-300' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 flex-shrink-0">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                            </svg>
                            @if(!$collapsed)<span class="text-sm">{{ __('School Years') }}</span>@endif
                        </a>

                        <a href="{{ route('admin.trimesters') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition-all duration-200 {{ request()->routeIs('admin.trimesters') ? 'bg-primary text-white shadow-lg shadow-primary/30' : 'hover:bg-neutral-700/50 text-neutral-300' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 flex-shrink-0">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                            @if(!$collapsed)<span class="text-sm">{{ __('Trimesters') }}</span>@endif
                        </a>

                        <a href="{{ route('admin.classes') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition-all duration-200 {{ request()->routeIs('admin.classes') ? 'bg-primary text-white shadow-lg shadow-primary/30' : 'hover:bg-neutral-700/50 text-neutral-300' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 flex-shrink-0">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" />
                            </svg>
                            @if(!$collapsed)<span class="text-sm">{{ __('Classes') }}</span>@endif
                        </a>

                        <a href="{{ route('admin.series') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition-all duration-200 {{ request()->routeIs('admin.series') ? 'bg-primary text-white shadow-lg shadow-primary/30' : 'hover:bg-neutral-700/50 text-neutral-300' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 flex-shrink-0">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 0 0 3 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 0 0 5.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 0 0 9.568 3Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6Z" />
                            </svg>
                            @if(!$collapsed)<span class="text-sm">{{ __('Series') }}</span>@endif
                        </a>

                        <a href="{{ route('admin.subjects') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition-all duration-200 {{ request()->routeIs('admin.subjects') ? 'bg-primary text-white shadow-lg shadow-primary/30' : 'hover:bg-neutral-700/50 text-neutral-300' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 flex-shrink-0">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                            </svg>
                            @if(!$collapsed)<span class="text-sm">{{ __('Subjects') }}</span>@endif
                        </a>

                        <a href="{{ route('admin.class-subjects') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition-all duration-200 {{ request()->routeIs('admin.class-subjects') ? 'bg-primary text-white shadow-lg shadow-primary/30' : 'hover:bg-neutral-700/50 text-neutral-300' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 flex-shrink-0">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 16.875h3.375m0 0h3.375m-3.375 0V13.5m0 3.375v3.375M6 10.5h2.25a2.25 2.25 0 0 0 2.25-2.25V6a2.25 2.25 0 0 0-2.25-2.25H6A2.25 2.25 0 0 0 3.75 6v2.25A2.25 2.25 0 0 0 6 10.5Zm0 9.75h2.25A2.25 2.25 0 0 0 10.5 18v-2.25a2.25 2.25 0 0 0-2.25-2.25H6a2.25 2.25 0 0 0-2.25 2.25V18A2.25 2.25 0 0 0 6 20.25Zm9.75-9.75H18a2.25 2.25 0 0 0 2.25-2.25V6A2.25 2.25 0 0 0 18 3.75h-2.25A2.25 2.25 0 0 0 13.5 6v2.25a2.25 2.25 0 0 0 2.25 2.25Z" />
                            </svg>
                            @if(!$collapsed)<span class="text-sm">{{ __('Class Subjects') }}</span>@endif
                        </a>

                        <a href="{{ route('admin.teacher-assignments') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition-all duration-200 {{ request()->routeIs('admin.teacher-assignments') ? 'bg-primary text-white shadow-lg shadow-primary/30' : 'hover:bg-neutral-700/50 text-neutral-300' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 flex-shrink-0">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" />
                            </svg>
                            @if(!$collapsed)<span class="text-sm">{{ __('Teacher Assignments') }}</span>@endif
                        </a>

                        <a href="{{ route('admin.users') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition-all duration-200 {{ request()->routeIs('admin.users') ? 'bg-primary text-white shadow-lg shadow-primary/30' : 'hover:bg-neutral-700/50 text-neutral-300' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 flex-shrink-0">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                            </svg>
                            @if(!$collapsed)<span class="text-sm">{{ __('Users') }}</span>@endif
                        </a>

                        <a href="{{ route('admin.grading-config') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition-all duration-200 {{ request()->routeIs('admin.grading-config') ? 'bg-primary text-white shadow-lg shadow-primary/30' : 'hover:bg-neutral-700/50 text-neutral-300' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 flex-shrink-0">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 0 1 0-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                            </svg>
                            @if(!$collapsed)<span class="text-sm">{{ __('Grading Config') }}</span>@endif
                        </a>

                        <a href="{{ route('admin.timetables') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition-all duration-200 {{ request()->routeIs('admin.timetables') ? 'bg-primary text-white shadow-lg shadow-primary/30' : 'hover:bg-neutral-700/50 text-neutral-300' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 flex-shrink-0">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                            </svg>
                            @if(!$collapsed)<span class="text-sm">{{ __('Timetables') }}</span>@endif
                        </a>
                    </div>
                @endif
            </div>
        @endif

        {{-- Management Section --}}
        @if(auth()->user()->isAdmin() || auth()->user()->isSecretary())
            <div class="mt-6">
                <button
                    wire:click="toggleSection('management')"
                    class="flex items-center justify-between w-full px-3 py-2 text-sm font-medium text-neutral-400 hover:text-neutral-200 transition-colors"
                >
                    @if(!$collapsed)
                        <span class="uppercase tracking-wider text-xs">{{ __('Management') }}</span>
                    @else
                        <span class="sr-only">{{ __('Management') }}</span>
                    @endif
                    @if(!$collapsed)
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 transition-transform {{ $this->isSectionCollapsed('management') ? '-rotate-90' : '' }}">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                        </svg>
                    @endif
                </button>

                @if($collapsed || !$this->isSectionCollapsed('management'))
                    <div class="{{ $collapsed ? 'mt-2 space-y-1' : 'mt-2 space-y-1 pl-2 border-l border-neutral-700/50' }}">
                        <a href="{{ route('students') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition-all duration-200 {{ request()->routeIs('students*') ? 'bg-primary text-white shadow-lg shadow-primary/30' : 'hover:bg-neutral-700/50 text-neutral-300' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 flex-shrink-0">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                            </svg>
                            @if(!$collapsed)<span class="text-sm">{{ __('Students') }}</span>@endif
                        </a>

                        <a href="{{ route('payments') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition-all duration-200 {{ request()->routeIs('payments*') ? 'bg-primary text-white shadow-lg shadow-primary/30' : 'hover:bg-neutral-700/50 text-neutral-300' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 flex-shrink-0">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" />
                            </svg>
                            @if(!$collapsed)<span class="text-sm">{{ __('Payments') }}</span>@endif
                        </a>

                        <a href="{{ route('admin.financial-reports') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition-all duration-200 {{ request()->routeIs('admin.financial-reports') ? 'bg-primary text-white shadow-lg shadow-primary/30' : 'hover:bg-neutral-700/50 text-neutral-300' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 flex-shrink-0">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6a7.5 7.5 0 1 0 7.5 7.5h-7.5V6Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 10.5H21A7.5 7.5 0 0 0 13.5 3v7.5Z" />
                            </svg>
                            @if(!$collapsed)<span class="text-sm">{{ __('Financial Reports') }}</span>@endif
                        </a>
                    </div>
                @endif
            </div>
        @endif

        {{-- Academic Section --}}
        <div class="mt-6">
            <button
                wire:click="toggleSection('academic')"
                class="flex items-center justify-between w-full px-3 py-2 text-sm font-medium text-neutral-400 hover:text-neutral-200 transition-colors"
            >
                @if(!$collapsed)
                    <span class="uppercase tracking-wider text-xs">{{ __('Academic') }}</span>
                @else
                    <span class="sr-only">{{ __('Academic') }}</span>
                @endif
                @if(!$collapsed)
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 transition-transform {{ $this->isSectionCollapsed('academic') ? '-rotate-90' : '' }}">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                    </svg>
                @endif
            </button>

            @if($collapsed || !$this->isSectionCollapsed('academic'))
                <div class="{{ $collapsed ? 'mt-2 space-y-1' : 'mt-2 space-y-1 pl-2 border-l border-neutral-700/50' }}">
                    <a href="{{ route('timetable') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition-all duration-200 {{ request()->routeIs('timetable') ? 'bg-primary text-white shadow-lg shadow-primary/30' : 'hover:bg-neutral-700/50 text-neutral-300' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 flex-shrink-0">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                        </svg>
                        @if(!$collapsed)<span class="text-sm">{{ __('Timetable') }}</span>@endif
                    </a>

                    <a href="{{ route('grades') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition-all duration-200 {{ request()->routeIs('grades') ? 'bg-primary text-white shadow-lg shadow-primary/30' : 'hover:bg-neutral-700/50 text-neutral-300' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 flex-shrink-0">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
                        </svg>
                        @if(!$collapsed)<span class="text-sm">{{ __('Grades') }}</span>@endif
                    </a>

                    @if(auth()->user()->isAdmin() || auth()->user()->isSecretary())
                        <a href="{{ route('class-grades') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition-all duration-200 {{ request()->routeIs('class-grades') ? 'bg-primary text-white shadow-lg shadow-primary/30' : 'hover:bg-neutral-700/50 text-neutral-300' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 flex-shrink-0">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M5.625 4.5h12.75a1.875 1.875 0 0 1 0 3.75H5.625a1.875 1.875 0 0 1 0-3.75Z" />
                            </svg>
                            @if(!$collapsed)<span class="text-sm">{{ __('Class Grades') }}</span>@endif
                        </a>

                        <a href="{{ route('bulletins') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition-all duration-200 {{ request()->routeIs('bulletins') ? 'bg-primary text-white shadow-lg shadow-primary/30' : 'hover:bg-neutral-700/50 text-neutral-300' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 flex-shrink-0">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                            </svg>
                            @if(!$collapsed)<span class="text-sm">{{ __('Bulletins') }}</span>@endif
                        </a>
                    @endif
                </div>
            @endif
        </div>

        {{-- Attendance Section --}}
        @php
            $attendanceEnabled = \App\Models\SchoolSetting::first()?->attendance_enabled ?? false;
        @endphp

        @if($attendanceEnabled)
            <div class="mt-6">
                <button
                    wire:click="toggleSection('attendance')"
                    class="flex items-center justify-between w-full px-3 py-2 text-sm font-medium text-neutral-400 hover:text-neutral-200 transition-colors"
                >
                    @if(!$collapsed)
                        <span class="uppercase tracking-wider text-xs">{{ __('Attendance') }}</span>
                    @else
                        <span class="sr-only">{{ __('Attendance') }}</span>
                    @endif
                    @if(!$collapsed)
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 transition-transform {{ $this->isSectionCollapsed('attendance') ? '-rotate-90' : '' }}">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                        </svg>
                    @endif
                </button>

                @if($collapsed || !$this->isSectionCollapsed('attendance'))
                    <div class="{{ $collapsed ? 'mt-2 space-y-1' : 'mt-2 space-y-1 pl-2 border-l border-neutral-700/50' }}">
                        <a href="{{ route('attendance.by-slot') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition-all duration-200 {{ request()->routeIs('attendance*') ? 'bg-primary text-white shadow-lg shadow-primary/30' : 'hover:bg-neutral-700/50 text-neutral-300' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 flex-shrink-0">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 0 1-1.043 3.296 3.745 3.745 0 0 1-3.296 1.043A3.745 3.745 0 0 1 12 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 0 1-3.296-1.043 3.745 3.745 0 0 1-1.043-3.296A3.745 3.745 0 0 1 3 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 0 1 1.043-3.296 3.746 3.746 0 0 1 3.296-1.043A3.746 3.746 0 0 1 12 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 0 1 3.296 1.043 3.746 3.745 3.745 0 0 1 1.043 3.296A3.745 3.745 0 0 1 21 12Z" />
                            </svg>
                            @if(!$collapsed)<span class="text-sm">{{ __('Mark Attendance') }}</span>@endif
                        </a>

                        @if(auth()->user()->isAdmin() || auth()->user()->isSecretary())
                            <a href="{{ route('attendance.justifications') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition-all duration-200 {{ request()->routeIs('attendance.justifications') ? 'bg-primary text-white shadow-lg shadow-primary/30' : 'hover:bg-neutral-700/50 text-neutral-300' }}">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 flex-shrink-0">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z" />
                                </svg>
                                @if(!$collapsed)<span class="text-sm">{{ __('Justifications') }}</span>@endif
                            </a>

                            <a href="{{ route('admin.attendance-reports') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition-all duration-200 {{ request()->routeIs('admin.attendance-reports') ? 'bg-primary text-white shadow-lg shadow-primary/30' : 'hover:bg-neutral-700/50 text-neutral-300' }}">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 flex-shrink-0">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z" />
                                </svg>
                                @if(!$collapsed)<span class="text-sm">{{ __('Attendance Reports') }}</span>@endif
                            </a>
                        @endif
                    </div>
                @endif
            </div>
        @endif
    </nav>

    {{-- User Info (Bottom) --}}
    @if(!$collapsed)
        <div class="p-4 border-t border-neutral-700/50">
            <a href="{{ route('profile') }}" class="flex items-center gap-3 p-2 rounded-lg hover:bg-neutral-700/50 transition-colors">
                <div class="w-9 h-9 rounded-full bg-neutral-600 flex items-center justify-center">
                    @if(auth()->user()->photo_url)
                        <img src="{{ auth()->user()->photo_url }}" alt="" class="w-full h-full rounded-full object-cover" />
                    @else
                        <span class="text-sm font-medium">{{ auth()->user()->first_name[0] }}</span>
                    @endif
                </div>
                <div class="flex-1 min-w-0">
                    <div class="text-sm font-medium truncate">{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</div>
                    <div class="text-xs text-neutral-400">{{ auth()->user()->role->label() }}</div>
                </div>
            </a>
        </div>
    @else
        <div class="p-4 border-t border-neutral-700/50 flex justify-center">
            <a href="{{ route('profile') }}" class="w-9 h-9 rounded-full bg-neutral-600 flex items-center justify-center hover:bg-neutral-500 transition-colors">
                @if(auth()->user()->photo_url)
                    <img src="{{ auth()->user()->photo_url }}" alt="" class="w-full h-full rounded-full object-cover" />
                @else
                    <span class="text-sm font-medium">{{ auth()->user()->first_name[0] }}</span>
                @endif
            </a>
        </div>
    @endif
</aside>

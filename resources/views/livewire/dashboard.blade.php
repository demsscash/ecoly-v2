<div>
    {{-- Welcome Message --}}
    <div class="mb-6">
        <h1 class="text-2xl font-bold">{{ __('Welcome') }}, {{ auth()->user()->first_name }} !</h1>
        <p class="text-base-content/60">
            @if($schoolYear)
                {{ __('School Year') }}: {{ $schoolYear->name }}
            @else
                {{ __('No active school year') }}
            @endif
        </p>
    </div>

    @if(!$schoolYear)
        <div class="alert alert-warning">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-current shrink-0 w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            <span>{{ __('No active school year.') }} {{ __('Please configure one to see statistics.') }}</span>
        </div>
    @endif

    @if(auth()->user()->hasRole(['admin', 'secretary']))
        {{-- Admin & Secretary Dashboard --}}
        
        {{-- Main Stats Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            {{-- Students --}}
            <div class="stat bg-base-100 rounded-box shadow">
                <div class="stat-figure text-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-10 h-10">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5" />
                    </svg>
                </div>
                <div class="stat-title">{{ __('Students') }}</div>
                <div class="stat-value text-primary">{{ $studentsStats['active'] }}</div>
                <div class="stat-desc">
                    <span class="text-info">{{ $studentsStats['boys'] }} ♂</span> • 
                    <span class="text-error">{{ $studentsStats['girls'] }} ♀</span>
                </div>
            </div>

            {{-- Classes --}}
            <div class="stat bg-base-100 rounded-box shadow">
                <div class="stat-figure text-secondary">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-10 h-10">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Z" />
                    </svg>
                </div>
                <div class="stat-title">{{ __('Classes') }}</div>
                <div class="stat-value text-secondary">{{ $classesStats['total'] }}</div>
                <div class="stat-desc">{{ __('Average') }}: {{ round($classesStats['average_size'], 1) }} {{ __('students') }}</div>
            </div>

            {{-- Collection Rate --}}
            <div class="stat bg-base-100 rounded-box shadow">
                <div class="stat-figure text-success">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-10 h-10">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" />
                    </svg>
                </div>
                <div class="stat-title">{{ __('Collection Rate') }}</div>
                <div class="stat-value {{ $financialStats['collection_rate'] >= 70 ? 'text-success' : ($financialStats['collection_rate'] >= 50 ? 'text-warning' : 'text-error') }}">
                    {{ $financialStats['collection_rate'] }}%
                </div>
                <div class="stat-desc">{{ number_format($financialStats['total_paid'], 0) }} / {{ number_format($financialStats['total_due'], 0) }} MRU</div>
            </div>

            @if(auth()->user()->hasRole('admin'))
            {{-- Users (Admin only) --}}
            <div class="stat bg-base-100 rounded-box shadow">
                <div class="stat-figure text-accent">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-10 h-10">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                    </svg>
                </div>
                <div class="stat-title">{{ __('Users') }}</div>
                <div class="stat-value text-accent">{{ $usersStats['active'] }}</div>
                <div class="stat-desc">{{ $usersStats['teachers'] }} {{ __('teachers') }}</div>
            </div>
            @else
            {{-- Balance --}}
            <div class="stat bg-base-100 rounded-box shadow">
                <div class="stat-figure text-error">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-10 h-10">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                </div>
                <div class="stat-title">{{ __('Balance') }}</div>
                <div class="stat-value text-error">{{ number_format($financialStats['balance'], 0) }}</div>
                <div class="stat-desc">MRU {{ __('remaining') }}</div>
            </div>
            @endif
        </div>

        {{-- Two Columns Layout --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Recent Students --}}
            <div class="card bg-base-100 shadow">
                <div class="card-body">
                    <h2 class="card-title text-base">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M18 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0ZM3 19.235v-.11a6.375 6.375 0 0 1 12.75 0v.109A12.318 12.318 0 0 1 9.374 21c-2.331 0-4.512-.645-6.374-1.766Z" />
                        </svg>
                        {{ __('Recent Students') }}
                    </h2>
                    <div class="space-y-2">
                        @forelse($recentStudents as $student)
                            <a href="{{ route('students.show', $student) }}" class="flex items-center gap-3 p-2 hover:bg-base-200 rounded-lg transition">
                                <div class="avatar">
                                    <div class="w-10 h-10 rounded-full">
                                        <img src="{{ $student->photo_url }}" alt="{{ $student->full_name }}" />
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <div class="font-medium">{{ $student->full_name }}</div>
                                    <div class="text-xs text-base-content/60">{{ $student->class?->name }} • {{ $student->matricule }}</div>
                                </div>
                                <div class="text-xs text-base-content/50">{{ $student->created_at->diffForHumans() }}</div>
                            </a>
                        @empty
                            <p class="text-center text-base-content/60 py-4">{{ __('No students found.') }}</p>
                        @endforelse
                    </div>
                    @if($recentStudents->isNotEmpty())
                        <div class="card-actions justify-end mt-4">
                            <a href="{{ route('students') }}" class="btn btn-sm btn-ghost">{{ __('View all') }} →</a>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Unpaid Students --}}
            <div class="card bg-base-100 shadow">
                <div class="card-body">
                    <h2 class="card-title text-base">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-error">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                        </svg>
                        {{ __('Unpaid Balances') }}
                    </h2>
                    <div class="space-y-2">
                        @forelse($unpaidStudents as $student)
                            @php
                                $summary = $student->getPaymentsSummary();
                            @endphp
                            <a href="{{ route('students.show', $student) }}" class="flex items-center gap-3 p-2 hover:bg-base-200 rounded-lg transition">
                                <div class="avatar">
                                    <div class="w-10 h-10 rounded-full">
                                        <img src="{{ $student->photo_url }}" alt="{{ $student->full_name }}" />
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <div class="font-medium">{{ $student->full_name }}</div>
                                    <div class="text-xs text-base-content/60">{{ $student->class?->name }}</div>
                                </div>
                                <div class="text-right">
                                    <div class="font-bold text-error">{{ number_format($summary['balance'], 0) }} MRU</div>
                                    <div class="text-xs text-base-content/50">{{ __('due') }}</div>
                                </div>
                            </a>
                        @empty
                            <p class="text-center text-base-content/60 py-4">{{ __('All payments up to date') }} ✓</p>
                        @endforelse
                    </div>
                    @if($unpaidStudents->isNotEmpty())
                        <div class="card-actions justify-end mt-4">
                            <a href="{{ route('payments') }}" class="btn btn-sm btn-ghost">{{ __('View all') }} →</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        @if(auth()->user()->hasRole('admin') && isset($trimesterStats))
        {{-- Trimester Progress (Admin only) --}}
        <div class="card bg-base-100 shadow mt-6">
            <div class="card-body">
                <h2 class="card-title text-base">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z" />
                    </svg>
                    {{ __('Current Trimester') }}: {{ $trimesterStats['current']->name }}
                </h2>
                <div class="flex items-center gap-4">
                    <div class="flex-1">
                        <div class="text-sm text-base-content/60 mb-2">{{ __('Grades Completion') }}</div>
                        <progress class="progress progress-primary w-full" value="{{ $trimesterStats['completion_rate'] }}" max="100"></progress>
                    </div>
                    <div class="text-right">
                        <div class="text-2xl font-bold">{{ $trimesterStats['completion_rate'] }}%</div>
                        <div class="text-xs text-base-content/60">{{ $trimesterStats['completed_grades'] }} / {{ $trimesterStats['total_grades'] }}</div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    @endif

    @if(auth()->user()->hasRole('teacher'))
        {{-- Teacher Dashboard --}}
        
        {{-- Teacher Stats --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="stat bg-base-100 rounded-box shadow">
                <div class="stat-figure text-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-10 h-10">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Z" />
                    </svg>
                </div>
                <div class="stat-title">{{ __('My Classes') }}</div>
                <div class="stat-value text-primary">{{ $teacherStats['classes'] }}</div>
                <div class="stat-desc">{{ __('assigned to you') }}</div>
            </div>

            <div class="stat bg-base-100 rounded-box shadow">
                <div class="stat-figure text-secondary">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-10 h-10">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                    </svg>
                </div>
                <div class="stat-title">{{ __('Subjects') }}</div>
                <div class="stat-value text-secondary">{{ $teacherStats['subjects'] }}</div>
                <div class="stat-desc">{{ __('to teach') }}</div>
            </div>

            <div class="stat bg-base-100 rounded-box shadow">
                <div class="stat-figure text-accent">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-10 h-10">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                    </svg>
                </div>
                <div class="stat-title">{{ __('Students') }}</div>
                <div class="stat-value text-accent">{{ $teacherStats['students'] }}</div>
                <div class="stat-desc">{{ __('total') }}</div>
            </div>

            <div class="stat bg-base-100 rounded-box shadow">
                <div class="stat-figure {{ $teacherStats['grades_to_enter'] > 0 ? 'text-warning' : 'text-success' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-10 h-10">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                    </svg>
                </div>
                <div class="stat-title">{{ __('Grades to Enter') }}</div>
                <div class="stat-value {{ $teacherStats['grades_to_enter'] > 0 ? 'text-warning' : 'text-success' }}">
                    {{ $teacherStats['grades_to_enter'] }}
                </div>
                <div class="stat-desc">
                    @if($teacherStats['grades_to_enter'] > 0)
                        {{ __('pending') }}
                    @else
                        {{ __('All done!') }} ✓
                    @endif
                </div>
            </div>
        </div>

        {{-- My Classes --}}
        <div class="card bg-base-100 shadow">
            <div class="card-body">
                <h2 class="card-title">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Z" />
                    </svg>
                    {{ __('My Classes') }}
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @forelse($teacherClasses as $class)
                        <a href="{{ route('grades') }}" class="card bg-base-200 hover:bg-base-300 transition shadow-sm">
                            <div class="card-body">
                                <h3 class="card-title text-base">{{ $class->name }}</h3>
                                <div class="flex justify-between text-sm">
                                    <span class="text-base-content/60">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 inline">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                                        </svg>
                                        {{ $class->students_count }} {{ __('students') }}
                                    </span>
                                    <span class="text-base-content/60">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 inline">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                                        </svg>
                                        {{ $class->subjects_count }} {{ __('subjects') }}
                                    </span>
                                </div>
                            </div>
                        </a>
                    @empty
                        <div class="col-span-full text-center text-base-content/60 py-8">
                            {{ __('No classes assigned yet.') }}
                        </div>
                    @endforelse
                </div>
                @if($teacherClasses->isNotEmpty())
                    <div class="card-actions justify-end mt-4">
                        <a href="{{ route('grades') }}" class="btn btn-primary btn-sm">{{ __('Enter Grades') }} →</a>
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>

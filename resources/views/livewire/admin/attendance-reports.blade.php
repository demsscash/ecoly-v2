<div>
    {{-- Header --}}
    <div class="mb-6">
        <h1 class="text-2xl font-bold">{{ __('Attendance Reports') }}</h1>
        <p class="text-base-content/60">{{ __('View attendance statistics and reports') }}</p>
    </div>

    {{-- Filters --}}
    <div class="card bg-base-100 shadow mb-6">
        <div class="card-body">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                {{-- Start Date --}}
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">{{ __('Start Date') }}</span>
                    </label>
                    <input type="date" wire:model.live="startDate" class="input input-bordered" />
                </div>

                {{-- End Date --}}
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">{{ __('End Date') }}</span>
                    </label>
                    <input type="date" wire:model.live="endDate" class="input input-bordered" />
                </div>

                {{-- Class Filter --}}
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">{{ __('Class') }}</span>
                    </label>
                    <select wire:model.live="filterClass" class="select select-bordered">
                        <option value="">{{ __('All classes') }}</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}">{{ $class->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    {{-- Statistics Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        {{-- Total Days --}}
        <div class="stat bg-base-100 rounded-box shadow">
            <div class="stat-figure text-primary">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                </svg>
            </div>
            <div class="stat-title">{{ __('Period') }}</div>
            <div class="stat-value text-primary">{{ $stats['total_days'] }}</div>
            <div class="stat-desc">{{ __('days') }}</div>
        </div>

        {{-- Attendance Rate --}}
        <div class="stat bg-base-100 rounded-box shadow">
            <div class="stat-figure text-success">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 0 1-1.043 3.296 3.745 3.745 0 0 1-3.296 1.043A3.745 3.745 0 0 1 12 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 0 1-3.296-1.043 3.745 3.745 0 0 1-1.043-3.296A3.745 3.745 0 0 1 3 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 0 1 1.043-3.296 3.746 3.746 0 0 1 3.296-1.043A3.746 3.746 0 0 1 12 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 0 1 3.296 1.043 3.746 3.746 0 0 1 1.043 3.296A3.745 3.745 0 0 1 21 12Z" />
                </svg>
            </div>
            <div class="stat-title">{{ __('Attendance Rate') }}</div>
            <div class="stat-value text-success">{{ $stats['attendance_rate'] }}%</div>
            <div class="stat-desc">{{ $stats['present_count'] }} {{ __('present') }}</div>
        </div>

        {{-- Absences --}}
        <div class="stat bg-base-100 rounded-box shadow">
            <div class="stat-figure text-error">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
            </div>
            <div class="stat-title">{{ __('Absences') }}</div>
            <div class="stat-value text-error">{{ $stats['absent_count'] }}</div>
            <div class="stat-desc">{{ $stats['justified_count'] }} {{ __('justified') }}</div>
        </div>

        {{-- Late & Left Early --}}
        <div class="stat bg-base-100 rounded-box shadow">
            <div class="stat-figure text-warning">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                </svg>
            </div>
            <div class="stat-title">{{ __('Delays') }}</div>
            <div class="stat-value text-warning">{{ $stats['late_count'] + $stats['left_early_count'] }}</div>
            <div class="stat-desc">{{ $stats['late_count'] }} {{ __('late') }}, {{ $stats['left_early_count'] }} {{ __('left early') }}</div>
        </div>
    </div>

    {{-- By Class Statistics --}}
    @if(!empty($stats['by_class']))
        <div class="card bg-base-100 shadow mb-6">
            <div class="card-body">
                <h2 class="card-title">{{ __('Attendance by Class') }}</h2>
                
                <div class="overflow-x-auto">
                    <table class="table table-zebra">
                        <thead>
                            <tr>
                                <th>{{ __('Class') }}</th>
                                <th class="text-right">{{ __('Total Records') }}</th>
                                <th class="text-right">{{ __('Present') }}</th>
                                <th class="text-right">{{ __('Rate') }}</th>
                                <th class="w-1/3">{{ __('Progress') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($stats['by_class'] as $classData)
                                <tr>
                                    <td class="font-medium">{{ $classData['class']->name }}</td>
                                    <td class="text-right">{{ $classData['total'] }}</td>
                                    <td class="text-right">{{ $classData['present'] }}</td>
                                    <td class="text-right">
                                        <span class="badge {{ $classData['rate'] >= 90 ? 'badge-success' : ($classData['rate'] >= 75 ? 'badge-warning' : 'badge-error') }}">
                                            {{ number_format($classData['rate'], 1) }}%
                                        </span>
                                    </td>
                                    <td>
                                        <div class="flex items-center gap-2">
                                            <progress class="progress {{ $classData['rate'] >= 90 ? 'progress-success' : ($classData['rate'] >= 75 ? 'progress-warning' : 'progress-error') }} w-full" 
                                                value="{{ $classData['rate'] }}" max="100"></progress>
                                            <span class="text-sm">{{ number_format($classData['rate'], 0) }}%</span>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    {{-- Export Actions --}}
    <div class="card bg-base-100 shadow">
        <div class="card-body">
            <h2 class="card-title">{{ __('Export Reports') }}</h2>
            <p class="text-base-content/60 mb-4">{{ __('Download attendance reports in different formats') }}</p>
            
            <div class="flex flex-wrap gap-2">
                <button wire:click="exportPdf" class="btn btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                    </svg>
                    {{ __('Export PDF') }}
                </button>
                
                <button wire:click="exportExcel" class="btn btn-success">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.375 19.5h17.25m-17.25 0a1.125 1.125 0 0 1-1.125-1.125M3.375 19.5h7.5c.621 0 1.125-.504 1.125-1.125m-9.75 0V5.625m0 12.75v-1.5c0-.621.504-1.125 1.125-1.125m18.375 2.625V5.625m0 12.75c0 .621-.504 1.125-1.125 1.125m1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125m0 3.75h-7.5A1.125 1.125 0 0 1 12 18.375m9.75-12.75c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125m19.5 0v1.5c0 .621-.504 1.125-1.125 1.125M2.25 5.625v1.5c0 .621.504 1.125 1.125 1.125m0 0h17.25m-17.25 0h7.5c.621 0 1.125.504 1.125 1.125M3.375 8.25c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125m17.25-3.75h-7.5c-.621 0-1.125.504-1.125 1.125m8.625-1.125c.621 0 1.125.504 1.125 1.125v1.5c0 .621-.504 1.125-1.125 1.125m-17.25 0h7.5m-7.5 0c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125M12 10.875v-1.5m0 1.5c0 .621-.504 1.125-1.125 1.125M12 10.875c0 .621.504 1.125 1.125 1.125m-2.25 0c.621 0 1.125.504 1.125 1.125M13.125 12h7.5m-7.5 0c-.621 0-1.125.504-1.125 1.125M20.625 12c.621 0 1.125.504 1.125 1.125v1.5c0 .621-.504 1.125-1.125 1.125m-17.25 0h7.5M12 14.625v-1.5m0 1.5c0 .621-.504 1.125-1.125 1.125M12 14.625c0 .621.504 1.125 1.125 1.125m-2.25 0c.621 0 1.125.504 1.125 1.125m0 1.5v-1.5m0 0c0-.621.504-1.125 1.125-1.125m0 0h7.5" />
                    </svg>
                    {{ __('Export Excel') }}
                </button>
            </div>
        </div>
    </div>
</div>

<div>
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold">{{ __('My Classes') }}</h1>
            <p class="text-base-content/60">{{ __('View your assigned classes and subjects') }}</p>
        </div>
    </div>

    {{-- Year Filter --}}
    <div class="card bg-base-100 shadow mb-6">
        <div class="card-body py-4">
            <div class="flex items-center gap-4">
                <label class="text-sm font-medium">{{ __('School Year') }}:</label>
                <select 
                    wire:model.live="selectedYearId"
                    class="select select-bordered select-sm w-48"
                >
                    @foreach ($years as $year)
                        <option value="{{ $year->id }}">
                            {{ $year->name }}
                            @if ($year->is_active) ({{ __('Active') }}) @endif
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    {{-- Classes Grid --}}
    @if ($classes->isEmpty())
        <div class="card bg-base-100 shadow">
            <div class="card-body text-center py-12 text-base-content/60">
                {{ __('No classes assigned to you for this school year.') }}
            </div>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($classes as $class)
                <div class="card bg-base-100 shadow">
                    <div class="card-body">
                        <div class="flex items-start justify-between">
                            <h2 class="card-title">{{ $class->name }}</h2>
                            @if (in_array($class->id, $mainTeacherClasses))
                                <span class="badge badge-primary">{{ __('Main Teacher') }}</span>
                            @endif
                        </div>
                        
                        <div class="mt-2 space-y-2 text-sm">
                            <div class="flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 text-base-content/60">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                                </svg>
                                <span>{{ $class->students->count() }} {{ __('students') }}</span>
                            </div>
                            
                            <div class="flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 text-base-content/60">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5" />
                                </svg>
                                <span>{{ __('Grade base') }}: {{ $class->grade_base }}/{{ $class->grade_base }}</span>
                            </div>
                        </div>

                        {{-- Subjects --}}
                        <div class="divider my-2"></div>
                        <h3 class="text-sm font-medium mb-2">{{ __('My Subjects') }}:</h3>
                        
                        @if (isset($classSubjects[$class->id]) && count($classSubjects[$class->id]) > 0)
                            <div class="flex flex-wrap gap-2">
                                @foreach ($classSubjects[$class->id] as $subject)
                                    <span class="badge badge-outline" title="{{ $subject->name_fr }}">
                                        {{ $subject->code }}
                                        <span class="text-xs opacity-60 ml-1">({{ $subject->coefficient }})</span>
                                    </span>
                                @endforeach
                            </div>
                        @else
                            <p class="text-sm text-base-content/60">{{ __('No subjects assigned') }}</p>
                        @endif

                        {{-- Actions --}}
                        <div class="card-actions justify-end mt-4">
                            <a href="{{ route('grades') }}?class={{ $class->id }}" class="btn btn-sm btn-primary">
                                {{ __('Enter Grades') }}
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

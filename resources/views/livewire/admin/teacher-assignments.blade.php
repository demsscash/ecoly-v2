<div>
    {{-- Header --}}
    <div class="mb-6">
        <h1 class="text-2xl font-bold">{{ __('Teacher Assignments') }}</h1>
        <p class="text-base-content/60">{{ __('Assign teachers to classes and subjects') }}</p>
    </div>

    {{-- Filters Card --}}
    <div class="card bg-base-100 shadow mb-6">
        <div class="card-body">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- School Year --}}
                <div>
                    <label class="label">
                        <span class="label-text font-semibold">{{ __('School Year') }}</span>
                    </label>
                    <select wire:model.live="selectedYearId" class="select select-bordered w-full">
                        @foreach ($years as $year)
                            <option value="{{ $year->id }}">{{ $year->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                {{-- Teacher --}}
                <div>
                    <label class="label">
                        <span class="label-text font-semibold">{{ __('Teacher') }}</span>
                    </label>
                    <select wire:model.live="selectedTeacherId" class="select select-bordered w-full">
                        <option value="">{{ __('Select a teacher') }}</option>
                        @foreach ($teachers as $teacher)
                            <option value="{{ $teacher->id }}">{{ $teacher->first_name }} {{ $teacher->last_name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    @if ($selectedTeacherId)
        {{-- Assignment Matrix --}}
        <div class="card bg-base-100 shadow">
            <div class="card-body">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-bold text-lg">{{ __('Class & Subject Assignments') }}</h3>
                    <div class="badge badge-primary badge-lg">
                        {{ $classes->count() }} {{ __('Classes') }}
                    </div>
                </div>
                
                @if ($classes->isEmpty())
                    <div class="text-center py-12">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-16 h-16 mx-auto text-base-content/30 mb-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" />
                        </svg>
                        <p class="text-base-content/60">{{ __('No classes found for this school year.') }}</p>
                    </div>
                @else
                    <div class="space-y-4">
                        @foreach ($classes as $class)
                            <div class="border border-base-300 rounded-lg overflow-hidden">
                                {{-- Class Header --}}
                                <div class="bg-base-200 px-4 py-3 flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-lg bg-primary flex items-center justify-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-white">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" />
                                            </svg>
                                        </div>
                                        <div>
                                            <h4 class="font-bold">{{ $class->name }}</h4>
                                            @if ($class->series)
                                                <p class="text-xs text-base-content/60">{{ $class->series->name_fr }}</p>
                                            @endif
                                        </div>
                                        @if ($class->main_teacher_id === $selectedTeacherId)
                                            <span class="badge badge-primary">{{ __('Main Teacher') }}</span>
                                        @endif
                                    </div>
                                    <button 
                                        wire:click="setMainTeacher({{ $class->id }})"
                                        class="btn btn-sm {{ $class->main_teacher_id === $selectedTeacherId ? 'btn-error' : 'btn-ghost' }}"
                                    >
                                        @if ($class->main_teacher_id === $selectedTeacherId)
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                                            </svg>
                                            {{ __('Remove Main') }}
                                        @else
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 0 1 1.04 0l2.125 5.111a.563.563 0 0 0 .475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 0 0-.182.557l1.285 5.385a.562.562 0 0 1-.84.61l-4.725-2.885a.562.562 0 0 0-.586 0L6.982 20.54a.562.562 0 0 1-.84-.61l1.285-5.386a.562.562 0 0 0-.182-.557l-4.204-3.602a.562.562 0 0 1 .321-.988l5.518-.442a.563.563 0 0 0 .475-.345L11.48 3.5Z" />
                                            </svg>
                                            {{ __('Set as Main') }}
                                        @endif
                                    </button>
                                </div>

                                {{-- Subjects Grid --}}
                                <div class="p-4">
                                    @if ($class->subjects->isEmpty())
                                        <p class="text-center py-4 text-base-content/60 text-sm">
                                            {{ __('No subjects assigned to this class yet.') }}
                                        </p>
                                    @else
                                        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                                            @foreach ($class->subjects as $subject)
                                                @php
                                                    $key = "{$class->id}_{$subject->id}";
                                                    $isAssigned = isset($classSubjectAssignments[$key]);
                                                @endphp
                                                <div 
                                                    wire:click="toggleAssignment({{ $class->id }}, {{ $subject->id }})"
                                                    class="cursor-pointer border-2 rounded-lg p-3 transition-all hover:shadow-md {{ $isAssigned ? 'border-primary bg-primary/5' : 'border-base-300 hover:border-base-400' }}"
                                                >
                                                    <div class="flex items-start gap-2">
                                                        <div class="flex-shrink-0 mt-1">
                                                            <input 
                                                                type="checkbox" 
                                                                class="checkbox checkbox-primary checkbox-sm" 
                                                                checked="{{ $isAssigned ? 'checked' : '' }}"
                                                                readonly
                                                            />
                                                        </div>
                                                        <div class="flex-1 min-w-0">
                                                            <p class="font-semibold text-sm truncate">{{ $subject->name_fr }}</p>
                                                            <p class="text-xs text-base-content/60">{{ $subject->code }}</p>
                                                            @if ($subject->pivot->max_grade)
                                                                <p class="text-xs text-base-content/50">/{{ $subject->pivot->max_grade }}</p>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    @else
        {{-- Empty State --}}
        <div class="card bg-base-100 shadow">
            <div class="card-body text-center py-16">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-20 h-20 mx-auto text-base-content/30 mb-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                </svg>
                <h3 class="text-xl font-bold mb-2">{{ __('Select a teacher to start') }}</h3>
                <p class="text-base-content/60">{{ __('Choose a teacher from the dropdown above to manage their class and subject assignments.') }}</p>
            </div>
        </div>
    @endif
</div>

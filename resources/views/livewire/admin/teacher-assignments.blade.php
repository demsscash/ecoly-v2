<div>
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold">{{ __('Teacher Assignments') }}</h1>
            <p class="text-base-content/60">{{ __('Assign teachers to classes and subjects') }}</p>
        </div>
    </div>

    {{-- Filters --}}
    <div class="card bg-base-100 shadow mb-6">
        <div class="card-body py-4">
            <div class="flex flex-wrap items-center gap-4">
                <div>
                    <label class="text-sm font-medium">{{ __('School Year') }}:</label>
                    <select wire:model.live="selectedYearId" class="select select-bordered select-sm w-40 ml-2">
                        @foreach ($years as $year)
                            <option value="{{ $year->id }}">{{ $year->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label class="text-sm font-medium">{{ __('Teacher') }}:</label>
                    <select wire:model.live="selectedTeacherId" class="select select-bordered select-sm w-56 ml-2">
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
                <h3 class="font-bold mb-4">{{ __('Class & Subject Assignments') }}</h3>
                
                @if ($classes->isEmpty())
                    <p class="text-center py-8 text-base-content/60">{{ __('No classes found for this school year.') }}</p>
                @else
                    <div class="space-y-6">
                        @foreach ($classes as $class)
                            <div class="border border-base-200 rounded-lg p-4">
                                <div class="flex items-center justify-between mb-3">
                                    <div class="flex items-center gap-3">
                                        <h4 class="font-bold">{{ $class->name }}</h4>
                                        @if ($class->main_teacher_id === $selectedTeacherId)
                                            <span class="badge badge-primary badge-sm">{{ __('Main Teacher') }}</span>
                                        @endif
                                    </div>
                                    <button 
                                        wire:click="setMainTeacher({{ $class->id }})"
                                        class="btn btn-xs {{ $class->main_teacher_id === $selectedTeacherId ? 'btn-error' : 'btn-outline btn-primary' }}"
                                    >
                                        @if ($class->main_teacher_id === $selectedTeacherId)
                                            {{ __('Remove as Main') }}
                                        @else
                                            {{ __('Set as Main Teacher') }}
                                        @endif
                                    </button>
                                </div>
                                
                                @if ($class->subjects->isEmpty())
                                    <p class="text-sm text-base-content/60">{{ __('No subjects assigned to this class.') }}</p>
                                @else
                                    <div class="flex flex-wrap gap-2">
                                        @foreach ($class->subjects as $subject)
                                            @php
                                                $key = "{$class->id}_{$subject->id}";
                                                $isAssigned = isset($classSubjectAssignments[$key]);
                                                $otherTeacher = $subject->pivot->teacher_id && $subject->pivot->teacher_id != $selectedTeacherId;
                                            @endphp
                                            <button 
                                                wire:click="toggleAssignment({{ $class->id }}, {{ $subject->id }})"
                                                class="btn btn-sm {{ $isAssigned ? 'btn-primary' : ($otherTeacher ? 'btn-ghost opacity-50' : 'btn-outline') }}"
                                                @if($otherTeacher) title="{{ __('Assigned to another teacher') }}" @endif
                                            >
                                                {{ $subject->code }}
                                                @if ($isAssigned)
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                                    </svg>
                                                @endif
                                            </button>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    @else
        <div class="card bg-base-100 shadow">
            <div class="card-body text-center py-12 text-base-content/60">
                {{ __('Please select a teacher to manage assignments.') }}
            </div>
        </div>
    @endif
</div>

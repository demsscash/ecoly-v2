<div>
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold">{{ __('Grade Entry') }}</h1>
            <p class="text-base-content/60">{{ __('Enter student grades by class and subject') }}</p>
        </div>
    </div>

    {{-- Filters --}}
    <div class="card bg-base-100 shadow mb-6">
        <div class="card-body py-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="text-sm font-medium mb-1 block">{{ __('School Year') }}</label>
                    <select wire:model.live="selectedYearId" class="select select-bordered w-full select-sm">
                        @foreach ($years as $year)
                            <option value="{{ $year->id }}">{{ $year->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label class="text-sm font-medium mb-1 block">{{ __('Class') }}</label>
                    <select wire:model.live="selectedClassId" class="select select-bordered w-full select-sm">
                        <option value="">{{ __('Select a class') }}</option>
                        @foreach ($classes as $class)
                            <option value="{{ $class->id }}">{{ $class->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="text-sm font-medium mb-1 block">{{ __('Subject') }}</label>
                    <select wire:model.live="selectedSubjectId" class="select select-bordered w-full select-sm" @disabled(!$selectedClassId)>
                        <option value="">{{ __('Select a subject') }}</option>
                        @foreach ($subjects as $subject)
                            <option value="{{ $subject->id }}">{{ $subject->name_fr }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="text-sm font-medium mb-1 block">{{ __('Trimester') }}</label>
                    <select wire:model.live="selectedTrimesterId" class="select select-bordered w-full select-sm">
                        <option value="">{{ __('Select a trimester') }}</option>
                        @foreach ($trimesters as $trimester)
                            <option value="{{ $trimester->id }}">
                                {{ $trimester->name_fr }}
                                @if ($trimester->status === 'finalized') ({{ __('Finalized') }}) @endif
                                @if ($trimester->status === 'open') ({{ __('Open') }}) @endif
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    {{-- Info Bar --}}
    @if ($selectedClass && $selectedTrimester)
        <div class="alert {{ $canEdit ? 'alert-info' : 'alert-warning' }} mb-6">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-current shrink-0 w-6 h-6">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <div>
                <span class="font-bold">{{ __('Grade base') }}: {{ $selectedClass->grade_base }}/{{ $selectedClass->grade_base }}</span>
                <span class="mx-2">|</span>
                <span>{{ __('Control') }}: {{ $controlWeight }}% | {{ __('Exam') }}: {{ $examWeight }}%</span>
                @if (!$canEdit)
                    <span class="mx-2">|</span>
                    <span class="text-warning font-bold">{{ __('Trimester finalized - Read only') }}</span>
                @endif
            </div>
        </div>
    @endif

    {{-- Grades Table --}}
    @if ($selectedClassId && $selectedSubjectId && $selectedTrimesterId && count($grades) > 0)
        <div class="card bg-base-100 shadow">
            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th class="w-8">#</th>
                            <th>{{ __('Student') }}</th>
                            <th class="w-28">{{ __('Control') }} ({{ $controlWeight }}%)</th>
                            <th class="w-28">{{ __('Exam') }} ({{ $examWeight }}%)</th>
                            <th class="w-28">{{ __('Average') }}</th>
                            <th>{{ __('Appreciation') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($grades as $studentId => $grade)
                            <tr>
                                <td class="text-base-content/60">{{ $loop->iteration }}</td>
                                <td class="font-medium">{{ $grade['student_name'] }}</td>
                                <td>
                                    <input 
                                        type="number"
                                        wire:model.blur="grades.{{ $studentId }}.control_grade"
                                        wire:change="calculateAverage({{ $studentId }})"
                                        min="0"
                                        max="{{ $selectedClass->grade_base }}"
                                        step="0.25"
                                        class="input input-bordered input-sm w-full"
                                        @disabled(!$canEdit)
                                    />
                                </td>
                                <td>
                                    <input 
                                        type="number"
                                        wire:model.blur="grades.{{ $studentId }}.exam_grade"
                                        wire:change="calculateAverage({{ $studentId }})"
                                        min="0"
                                        max="{{ $selectedClass->grade_base }}"
                                        step="0.25"
                                        class="input input-bordered input-sm w-full"
                                        @disabled(!$canEdit)
                                    />
                                </td>
                                <td>
                                    <input 
                                        type="text"
                                        wire:model="grades.{{ $studentId }}.average"
                                        class="input input-bordered input-sm w-full bg-base-200"
                                        readonly
                                    />
                                </td>
                                <td>
                                    <input 
                                        type="text"
                                        wire:model="grades.{{ $studentId }}.appreciation"
                                        placeholder="{{ __('Optional') }}"
                                        class="input input-bordered input-sm w-full"
                                        @disabled(!$canEdit)
                                    />
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            @if ($canEdit)
                <div class="card-body pt-0">
                    <div class="flex justify-end">
                        <button wire:click="save" class="btn btn-primary" wire:loading.attr="disabled">
                            <span wire:loading.remove>{{ __('Save Grades') }}</span>
                            <span wire:loading class="loading loading-spinner loading-sm"></span>
                        </button>
                    </div>
                </div>
            @endif
        </div>
    @elseif ($selectedClassId && $selectedSubjectId && $selectedTrimesterId)
        <div class="card bg-base-100 shadow">
            <div class="card-body text-center py-12 text-base-content/60">
                {{ __('No active students in this class.') }}
            </div>
        </div>
    @else
        <div class="card bg-base-100 shadow">
            <div class="card-body text-center py-12 text-base-content/60">
                {{ __('Select a class, subject, and trimester to enter grades.') }}
            </div>
        </div>
    @endif
</div>

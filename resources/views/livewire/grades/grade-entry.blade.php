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
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                {{-- Class --}}
                <div>
                    <label class="label py-1"><span class="label-text">{{ __('Class') }}</span></label>
                    <select wire:model.live="selectedClassId" class="select select-bordered w-full">
                        <option value="">{{ __('Select a class') }}</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}">{{ $class->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                {{-- Subject with grade base --}}
                <div>
                    <label class="label py-1"><span class="label-text">{{ __('Subject') }}</span></label>
                    <select wire:model.live="selectedSubjectId" class="select select-bordered w-full" @if(!$selectedClassId) disabled @endif>
                        <option value="">{{ __('Select a subject') }}</option>
                        @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}">
                                {{ $subject->name_fr }} ({{ $subject->code }}) - /{{ $subject->grade_base_display }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                {{-- Trimester --}}
                <div>
                    <label class="label py-1"><span class="label-text">{{ __('Trimester') }}</span></label>
                    <select wire:model.live="selectedTrimesterId" class="select select-bordered w-full">
                        @foreach($trimesters as $trimester)
                            <option value="{{ $trimester->id }}">{{ $trimester->name_fr }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    @if($isFinalized)
        <div class="alert alert-warning mb-6">
            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
            <span>{{ __('Trimester finalized - Read only') }}</span>
        </div>
    @endif

    @if($selectedClassId && $selectedSubjectId && $selectedTrimesterId)
        {{-- Grade Base Info --}}
        <div class="alert alert-info mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-current shrink-0 w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <span>{{ __('Grade base') }}: <strong>{{ $subjectGradeBase }}</strong> {{ __('points') }}</span>
        </div>

        {{-- Grades Table --}}
        <div class="card bg-base-100 shadow">
            <div class="card-body p-0">
                <div class="overflow-x-auto">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>{{ __('Student') }}</th>
                                <th class="text-center w-32">{{ __('Control') }} <span class="text-xs text-base-content/60">/{{ $subjectGradeBase }}</span></th>
                                <th class="text-center w-32">{{ __('Exam') }} <span class="text-xs text-base-content/60">/{{ $subjectGradeBase }}</span></th>
                                <th class="text-center w-32">{{ __('Average') }} <span class="text-xs text-base-content/60">/{{ $subjectGradeBase }}</span></th>
                                <th class="w-48">{{ __('Appreciation') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($grades as $studentId => $data)
                                <tr>
                                    <td class="font-medium">{{ $data['student_name'] }}</td>
                                    <td>
                                        <input type="number" 
                                            wire:model.live.debounce.500ms="grades.{{ $studentId }}.control_grade"
                                            wire:change="calculateAverage({{ $studentId }})"
                                            step="0.25" min="0" max="{{ $subjectGradeBase }}"
                                            class="input input-bordered input-sm w-full text-center font-mono"
                                            placeholder="0-{{ $subjectGradeBase }}"
                                            @if($isFinalized) disabled @endif />
                                    </td>
                                    <td>
                                        <input type="number"
                                            wire:model.live.debounce.500ms="grades.{{ $studentId }}.exam_grade"
                                            wire:change="calculateAverage({{ $studentId }})"
                                            step="0.25" min="0" max="{{ $subjectGradeBase }}"
                                            class="input input-bordered input-sm w-full text-center font-mono"
                                            placeholder="0-{{ $subjectGradeBase }}"
                                            @if($isFinalized) disabled @endif />
                                    </td>
                                    <td class="text-center font-mono font-bold">
                                        @if($data['average'] !== null)
                                            @php
                                                $passThreshold = ($subjectGradeBase / 20) * 10;
                                            @endphp
                                            <span class="{{ $data['average'] >= $passThreshold ? 'text-success' : 'text-error' }}">
                                                {{ number_format($data['average'], 2) }}
                                            </span>
                                        @else
                                            <span class="text-base-content/30">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <input type="text"
                                            wire:model="grades.{{ $studentId }}.appreciation"
                                            class="input input-bordered input-sm w-full text-sm"
                                            placeholder="{{ __('Auto') }}"
                                            @if($isFinalized) disabled @endif />
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-8 text-base-content/60">
                                        {{ __('No active students in this class.') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                @if(count($grades) > 0 && !$isFinalized)
                    <div class="p-4 border-t border-base-200 flex justify-end">
                        <button wire:click="save" wire:loading.attr="disabled" class="btn btn-primary">
                            <span wire:loading wire:target="save" class="loading loading-spinner loading-sm"></span>
                            {{ __('Save Grades') }}
                        </button>
                    </div>
                @endif
            </div>
        </div>
    @else
        <div class="alert">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-info shrink-0 w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <span>{{ __('Select a class, subject, and trimester to enter grades.') }}</span>
        </div>
    @endif
</div>

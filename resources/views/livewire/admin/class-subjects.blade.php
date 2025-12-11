<div>
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold">{{ __('Class Subjects') }}</h1>
            <p class="text-base-content/60">{{ __('Assign subjects to classes') }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Class Selector --}}
        <div class="lg:col-span-1">
            <div class="card bg-base-100 shadow">
                <div class="card-body">
                    <h3 class="card-title text-base">{{ __('Classes') }}</h3>
                    <ul class="menu menu-sm bg-base-200 rounded-box">
                        @foreach($classes as $class)
                            <li>
                                <a wire:click="selectClass({{ $class->id }})" class="{{ $selectedClassId == $class->id ? 'active' : '' }}">
                                    {{ $class->name }}
                                    <span class="badge badge-sm">{{ $class->subjects->count() }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>

        {{-- Subjects List --}}
        <div class="lg:col-span-2">
            @if($selectedClassId)
                <div class="card bg-base-100 shadow">
                    <div class="card-body">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="card-title text-base">{{ __('Subjects') }} - {{ $selectedClass?->name }}</h3>
                            <button wire:click="$set('showAddModal', true)" class="btn btn-primary btn-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                                {{ __('Add Subject') }}
                            </button>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>{{ __('Subject') }}</th>
                                        <th class="text-center">{{ __('Grade Base') }}</th>
                                        <th>{{ __('Teacher') }}</th>
                                        <th>{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($classSubjects as $cs)
                                        <tr>
                                            <td>
                                                <div class="font-medium">{{ $cs->name_fr }}</div>
                                                <div class="text-xs text-base-content/60">{{ $cs->code }}</div>
                                            </td>
                                            <td class="text-center">
                                                <select wire:change="updateGradeBase({{ $cs->id }}, $event.target.value)"
                                                    class="select select-bordered select-xs w-20">
                                                    @foreach([10, 20, 30, 40, 50] as $base)
                                                        <option value="{{ $base }}" {{ ($cs->pivot->grade_base ?? $selectedClass->grade_base ?? 20) == $base ? 'selected' : '' }}>
                                                            /{{ $base }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <select wire:change="updateTeacher({{ $cs->id }}, $event.target.value)"
                                                    class="select select-bordered select-xs w-full max-w-xs">
                                                    <option value="">{{ __('No teacher') }}</option>
                                                    @foreach($teachers as $teacher)
                                                        <option value="{{ $teacher->id }}" {{ $cs->pivot->teacher_id == $teacher->id ? 'selected' : '' }}>
                                                            {{ $teacher->first_name }} {{ $teacher->last_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <button wire:click="removeSubject({{ $cs->id }})" wire:confirm="{{ __('Remove this subject from class?') }}" class="btn btn-ghost btn-xs text-error">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" /></svg>
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-8 text-base-content/60">
                                                {{ __('No subjects assigned to this class.') }}
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @else
                <div class="alert">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-info shrink-0 w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span>{{ __('Please select a class to manage its subjects.') }}</span>
                </div>
            @endif
        </div>
    </div>

    {{-- Add Subject Modal --}}
    @if($showAddModal)
    <div class="modal modal-open">
        <div class="modal-box">
            <h3 class="font-bold text-lg mb-4">{{ __('Add Subject to Class') }}</h3>
            <form wire:submit="addSubject">
                <div class="form-control mb-4">
                    <label class="label"><span class="label-text">{{ __('Subject') }}</span></label>
                    <select wire:model="newSubjectId" class="select select-bordered w-full" required>
                        <option value="">{{ __('Select a subject') }}</option>
                        @foreach($availableSubjects as $subject)
                            <option value="{{ $subject->id }}">{{ $subject->name_fr }} ({{ $subject->code }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-control mb-4">
                    <label class="label"><span class="label-text">{{ __('Grade Base') }}</span></label>
                    <select wire:model="newGradeBase" class="select select-bordered w-full">
                        <option value="">{{ __('Class default') }} (/{{ $selectedClass?->grade_base ?? 20 }})</option>
                        <option value="10">/10</option>
                        <option value="20">/20</option>
                        <option value="30">/30</option>
                        <option value="40">/40</option>
                        <option value="50">/50</option>
                    </select>
                </div>
                <div class="modal-action">
                    <button type="button" wire:click="$set('showAddModal', false)" class="btn">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('Add') }}</button>
                </div>
            </form>
        </div>
        <div class="modal-backdrop" wire:click="$set('showAddModal', false)"></div>
    </div>
    @endif
</div>

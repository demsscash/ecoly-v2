<div>
    {{-- Header --}}
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold">{{ __('Class Subjects') }}</h1>
            <p class="text-base-content/60">{{ __('Assign subjects to classes') }}</p>
        </div>
        <button wire:click="openCreateModal" class="btn btn-primary">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            {{ __('New Assignment') }}
        </button>
    </div>

    {{-- Filter --}}
    <div class="card bg-base-100 shadow mb-6">
        <div class="card-body">
            <div class="flex items-center gap-4">
                <label class="label">
                    <span class="label-text font-semibold">{{ __('Filter by Class') }}</span>
                </label>
                <select wire:model.live="filterClass" class="select select-bordered">
                    <option value="">{{ __('All Classes') }}</option>
                    @foreach($classes as $class)
                        <option value="{{ $class->id }}">{{ $class->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="card bg-base-100 shadow">
        <div class="card-body">
            <h2 class="card-title mb-4">{{ __('Assignments List') }}</h2>

            <div class="overflow-x-auto">
                <table class="table table-zebra">
                    <thead>
                        <tr>
                            <th>{{ __('Class') }}</th>
                            <th>{{ __('Subject') }}</th>
                            <th>{{ __('Teacher') }}</th>
                            <th class="text-center">{{ __('Max Grade') }}</th>
                            <th class="text-center">{{ __('Coefficient') }}</th>
                            <th class="text-right">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($assignments as $assignment)
                            <tr>
                                <td>
                                    <div class="font-semibold">{{ $assignment->class->name }}</div>
                                    @if($assignment->class->serie)
                                        <span class="badge badge-primary badge-sm">{{ $assignment->class->serie->name }}</span>
                                    @endif
                                </td>
                                <td class="font-semibold">{{ $assignment->subject->name_fr }}</td>
                                <td>
                                    @if($assignment->teacher)
                                        <div class="flex items-center gap-2">
                                            <div class="avatar placeholder">
                                                <div class="bg-neutral text-neutral-content rounded-full w-8">
                                                    <span class="text-xs">{{ substr($assignment->teacher->first_name, 0, 1) }}{{ substr($assignment->teacher->last_name, 0, 1) }}</span>
                                                </div>
                                            </div>
                                            <span>{{ $assignment->teacher->first_name }} {{ $assignment->teacher->last_name }}</span>
                                        </div>
                                    @else
                                        <span class="text-base-content/40">{{ __('Not assigned') }}</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-neutral">/{{ $assignment->max_grade }}</span>
                                </td>
                                <td class="text-center">
                                    @if($assignment->class->usesCoefficients())
                                        <span class="badge badge-info">{{ $assignment->coefficient }}</span>
                                    @else
                                        <span class="text-base-content/40">-</span>
                                    @endif
                                </td>
                                <td class="text-right">
                                    <div class="flex justify-end gap-2">
                                        <button wire:click="openEditModal({{ $assignment->id }})" class="btn btn-ghost btn-sm">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                            </svg>
                                        </button>
                                        <button wire:click="delete({{ $assignment->id }})"
                                            wire:confirm="{{ __('Are you sure you want to delete this assignment?') }}"
                                            class="btn btn-ghost btn-sm text-error">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-12">
                                    <div class="flex flex-col items-center gap-4">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-16 h-16 text-base-content/20">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                                        </svg>
                                        <div class="text-center">
                                            <p class="text-lg font-semibold text-base-content/60">{{ __('No assignments found.') }}</p>
                                            <p class="text-sm text-base-content/40">{{ __('Start by assigning subjects to classes.') }}</p>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($assignments->hasPages())
                <div class="mt-4 pt-4 border-t border-base-200">
                    {{ $assignments->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- Modal --}}
    @if($showModal)
        <div class="modal modal-open">
            <div class="modal-box max-w-2xl">
                <h3 class="font-bold text-xl mb-6">
                    {{ $editingAssignmentId ? __('Edit Assignment') : __('New Assignment') }}
                </h3>

                <form wire:submit="save">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- Class --}}
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-semibold">{{ __('Class') }} *</span>
                            </label>
                            <select wire:model.live="class_id" class="select select-bordered" required>
                                <option value="">{{ __('Select class') }}</option>
                                @foreach($classes as $class)
                                    <option value="{{ $class->id }}">{{ $class->name }}</option>
                                @endforeach
                            </select>
                            @error('class_id')
                                <span class="text-error text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Subject --}}
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-semibold">{{ __('Subject') }} *</span>
                            </label>
                            <select wire:model="subject_id" class="select select-bordered" required>
                                <option value="">{{ __('Select subject') }}</option>
                                @foreach($subjects as $subject)
                                    <option value="{{ $subject->id }}">{{ $subject->name_fr }}</option>
                                @endforeach
                            </select>
                            @error('subject_id')
                                <span class="text-error text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Teacher --}}
                        <div class="form-control md:col-span-2">
                            <label class="label">
                                <span class="label-text font-semibold">{{ __('Teacher') }}</span>
                            </label>
                            <select wire:model="teacher_id" class="select select-bordered">
                                <option value="">{{ __('No teacher assigned') }}</option>
                                @foreach($teachers as $teacher)
                                    <option value="{{ $teacher->id }}">{{ $teacher->first_name }} {{ $teacher->last_name }}</option>
                                @endforeach
                            </select>
                            @error('teacher_id')
                                <span class="text-error text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        @php
                            $selectedClass = $class_id ? $classes->firstWhere('id', $class_id) : null;
                            $isCollegeOrLycee = $selectedClass && ($selectedClass->isCollege() || $selectedClass->isLycee());
                        @endphp

                        {{-- Max Grade (only for Fondamental) --}}
                        @if(!$isCollegeOrLycee)
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text font-semibold">{{ __('Max Grade') }} *</span>
                                </label>
                                <input type="number" wire:model="max_grade" class="input input-bordered" 
                                    min="1" max="100" required />
                                <label class="label">
                                    <span class="label-text-alt">{{ __('Ex: 10, 20, 100') }}</span>
                                </label>
                                @error('max_grade')
                                    <span class="text-error text-sm mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                        @else
                            {{-- Max Grade fixed at 20 for College/Lycee --}}
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text font-semibold">{{ __('Max Grade') }}</span>
                                </label>
                                <input type="text" value="/20" class="input input-bordered bg-base-200" disabled />
                                <label class="label">
                                    <span class="label-text-alt">{{ __('Fixed at 20 for College/Lycée') }}</span>
                                </label>
                            </div>
                        @endif

                        {{-- Coefficient (only for College/Lycee) --}}
                        @if($isCollegeOrLycee)
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text font-semibold">{{ __('Coefficient') }} *</span>
                                </label>
                                <input type="number" wire:model="coefficient" class="input input-bordered" 
                                    min="1" max="10" required />
                                <label class="label">
                                    <span class="label-text-alt">{{ __('Weight for general average (1-10)') }}</span>
                                </label>
                                @error('coefficient')
                                    <span class="text-error text-sm mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                        @endif
                    </div>

                    {{-- Info Alert --}}
                    @if($isCollegeOrLycee)
                        <div class="alert alert-info mt-4">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-current shrink-0 w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="text-sm">{{ __('College/Lycée: All grades are on /20 with coefficients for weighted averages.') }}</span>
                        </div>
                    @endif

                    {{-- Actions --}}
                    <div class="modal-action">
                        <button type="button" wire:click="$set('showModal', false)" class="btn btn-ghost">
                            {{ __('Cancel') }}
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                            {{ __('Save') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>

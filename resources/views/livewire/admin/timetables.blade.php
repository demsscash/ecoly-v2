<div>
    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold">{{ __('Timetables') }}</h1>
            <p class="text-base-content/60">{{ __('Manage class schedules') }}</p>
        </div>
        <button wire:click="openCreateModal" class="btn btn-primary">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            {{ __('New Entry') }}
        </button>
    </div>

    {{-- Filters --}}
    <div class="card bg-base-100 shadow mb-6">
        <div class="card-body">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="label">
                        <span class="label-text font-semibold">{{ __('Class') }}</span>
                    </label>
                    <select wire:model.live="filterClass" class="select select-bordered w-full">
                        <option value="">{{ __('All Classes') }}</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}">{{ $class->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label class="label">
                        <span class="label-text font-semibold">{{ __('Day') }}</span>
                    </label>
                    <select wire:model.live="filterDay" class="select select-bordered w-full">
                        <option value="">{{ __('All Days') }}</option>
                        @foreach($days as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="card bg-base-100 shadow">
        <div class="card-body">
            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th>{{ __('Day') }}</th>
                            <th>{{ __('Time') }}</th>
                            <th>{{ __('Class') }}</th>
                            <th>{{ __('Subject') }}</th>
                            <th>{{ __('Teacher') }}</th>
                            <th>{{ __('Room') }}</th>
                            <th>{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($timetables as $timetable)
                            <tr>
                                <td>{{ $timetable->day_label }}</td>
                                <td>{{ $timetable->timeSlot->time_range }}</td>
                                <td><span class="badge badge-primary">{{ $timetable->class->name }}</span></td>
                                <td>{{ $timetable->subject->name_fr }}</td>
                                <td>{{ $timetable->teacher?->first_name }} {{ $timetable->teacher?->last_name }}</td>
                                <td>{{ $timetable->room ?? '-' }}</td>
                                <td>
                                    <div class="flex gap-2">
                                        <button wire:click="openEditModal({{ $timetable->id }})" class="btn btn-ghost btn-sm">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                            </svg>
                                        </button>
                                        <button wire:click="delete({{ $timetable->id }})" wire:confirm="{{ __('Are you sure?') }}" class="btn btn-ghost btn-sm text-error">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-8 text-base-content/60">
                                    {{ __('No timetable entries found.') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($timetables->hasPages())
                <div class="mt-4">
                    {{ $timetables->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- Modal --}}
    @if($showModal)
    <div class="modal modal-open">
        <div class="modal-box max-w-2xl">
            <h3 class="font-bold text-lg mb-4">
                {{ $editingId ? __('Edit Entry') : __('New Entry') }}
            </h3>
            
            <form wire:submit="save">
                <div class="grid grid-cols-2 gap-4">
                    {{-- Class --}}
                    <div class="form-control">
                        <label class="label"><span class="label-text">{{ __('Class') }} *</span></label>
                        <select wire:model="class_id" class="select select-bordered" required>
                            <option value="">{{ __('Select a class') }}</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}">{{ $class->name }}</option>
                            @endforeach
                        </select>
                        @error('class_id') <span class="text-error text-sm">{{ $message }}</span> @enderror
                    </div>

                    {{-- Subject --}}
                    <div class="form-control">
                        <label class="label"><span class="label-text">{{ __('Subject') }} *</span></label>
                        <select wire:model="subject_id" class="select select-bordered" required>
                            <option value="">{{ __('Select a subject') }}</option>
                            @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}">{{ $subject->name_fr }}</option>
                            @endforeach
                        </select>
                        @error('subject_id') <span class="text-error text-sm">{{ $message }}</span> @enderror
                    </div>

                    {{-- Teacher --}}
                    <div class="form-control">
                        <label class="label"><span class="label-text">{{ __('Teacher') }}</span></label>
                        <select wire:model="teacher_id" class="select select-bordered">
                            <option value="">{{ __('Select a teacher') }}</option>
                            @foreach($teachers as $teacher)
                                <option value="{{ $teacher->id }}">{{ $teacher->first_name }} {{ $teacher->last_name }}</option>
                            @endforeach
                        </select>
                        @error('teacher_id') <span class="text-error text-sm">{{ $message }}</span> @enderror
                    </div>

                    {{-- Time Slot --}}
                    <div class="form-control">
                        <label class="label"><span class="label-text">{{ __('Time Slot') }} *</span></label>
                        <select wire:model="time_slot_id" class="select select-bordered" required>
                            <option value="">{{ __('Select time slot') }}</option>
                            @foreach($timeSlots as $slot)
                                <option value="{{ $slot->id }}">{{ $slot->name }} ({{ $slot->time_range }})</option>
                            @endforeach
                        </select>
                        @error('time_slot_id') <span class="text-error text-sm">{{ $message }}</span> @enderror
                    </div>

                    {{-- Day --}}
                    <div class="form-control">
                        <label class="label"><span class="label-text">{{ __('Day') }} *</span></label>
                        <select wire:model="day_of_week" class="select select-bordered" required>
                            @foreach($days as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('day_of_week') <span class="text-error text-sm">{{ $message }}</span> @enderror
                    </div>

                    {{-- Room --}}
                    <div class="form-control">
                        <label class="label"><span class="label-text">{{ __('Room') }}</span></label>
                        <input type="text" wire:model="room" class="input input-bordered" placeholder="Ex: A101" />
                        @error('room') <span class="text-error text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>

                {{-- Notes --}}
                <div class="form-control mt-4">
                    <label class="label"><span class="label-text">{{ __('Notes') }}</span></label>
                    <textarea wire:model="notes" class="textarea textarea-bordered" rows="2"></textarea>
                    @error('notes') <span class="text-error text-sm">{{ $message }}</span> @enderror
                </div>

                <div class="modal-action">
                    <button type="button" wire:click="$set('showModal', false)" class="btn">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>

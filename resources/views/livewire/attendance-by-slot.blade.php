<div>
    {{-- Header --}}
    <div class="mb-6">
        <h1 class="text-2xl font-bold">{{ __('Attendance by Time Slot') }}</h1>
        <p class="text-base-content/60">{{ __('Mark attendance for each class period') }}</p>
    </div>

    {{-- Filters --}}
    <div class="card bg-base-100 shadow mb-6">
        <div class="card-body">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                {{-- Date --}}
                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-semibold">{{ __('Date') }}</span>
                    </label>
                    <input type="date" wire:model.live="selectedDate" class="input input-bordered" />
                </div>

                {{-- Class --}}
                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-semibold">{{ __('Class') }}</span>
                    </label>
                    <select wire:model.live="selectedClassId" class="select select-bordered">
                        <option value="">{{ __('Select a class') }}</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}">{{ $class->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Time Slot --}}
                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-semibold">{{ __('Time Slot') }}</span>
                    </label>
                    <select wire:model.live="selectedSlotId" class="select select-bordered" 
                        @if($availableSlots->isEmpty()) disabled @endif>
                        <option value="">{{ __('Select time slot') }}</option>
                        @foreach($availableSlots as $slot)
                            <option value="{{ $slot->id }}">{{ $slot->name }} ({{ $slot->time_range }})</option>
                        @endforeach
                    </select>
                    @if($selectedClassId && $availableSlots->isEmpty())
                        <label class="label">
                            <span class="label-text-alt text-warning">{{ __('No classes scheduled for this day') }}</span>
                        </label>
                    @endif
                </div>
            </div>

            {{-- Current Course Info --}}
            @if($currentTimetable)
                <div class="alert alert-info mt-4">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-current shrink-0 w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div>
                        <div class="font-bold">{{ $currentTimetable->subject->name_fr }}</div>
                        <div class="text-sm">
                            {{ __('Teacher') }}: {{ $currentTimetable->teacher?->first_name }} {{ $currentTimetable->teacher?->last_name }}
                            @if($currentTimetable->room)
                                • {{ __('Room') }}: {{ $currentTimetable->room }}
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    @if($selectedClassId && $selectedSlotId && !empty($attendances))
        {{-- Attendance Table --}}
        <div class="card bg-base-100 shadow">
            <div class="card-body">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="font-bold text-lg">{{ __('Students') }} ({{ count($attendances) }})</h3>
                    <button wire:click="markAllPresent" class="btn btn-success btn-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                        </svg>
                        {{ __('Mark All Present') }}
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="table table-zebra">
                        <thead>
                            <tr>
                                <th class="w-16"></th>
                                <th>{{ __('Matricule') }}</th>
                                <th>{{ __('Student') }}</th>
                                <th class="text-center">✅ {{ __('Present') }}</th>
                                <th class="text-center">❌ {{ __('Absent') }}</th>
                                <th class="text-center">⏰ {{ __('Late') }}</th>
                                <th class="text-center">🏃 {{ __('Left Early') }}</th>
                                <th>{{ __('Note') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($attendances as $studentId => $data)
                                <tr>
                                    {{-- Photo --}}
                                    <td>
                                        <div class="avatar">
                                            <div class="w-10 h-10 rounded-full">
                                                <img src="{{ $data['student']->photo_url }}" alt="{{ $data['student']->full_name }}" />
                                            </div>
                                        </div>
                                    </td>
                                    
                                    {{-- Matricule --}}
                                    <td class="font-mono text-sm">{{ $data['student']->matricule }}</td>
                                    
                                    {{-- Name --}}
                                    <td class="font-medium">{{ $data['student']->full_name }}</td>
                                    
                                    {{-- Present --}}
                                    <td class="text-center">
                                        <input type="radio" 
                                            name="status_{{ $studentId }}"
                                            wire:click="setStatus({{ $studentId }}, 'present')"
                                            class="radio radio-success" 
                                            @checked($data['status'] === 'present') />
                                    </td>
                                    
                                    {{-- Absent --}}
                                    <td class="text-center">
                                        <input type="radio" 
                                            name="status_{{ $studentId }}"
                                            wire:click="setStatus({{ $studentId }}, 'absent')"
                                            class="radio radio-error" 
                                            @checked($data['status'] === 'absent') />
                                    </td>
                                    
                                    {{-- Late --}}
                                    <td class="text-center">
                                        <input type="radio" 
                                            name="status_{{ $studentId }}"
                                            wire:click="setStatus({{ $studentId }}, 'late')"
                                            class="radio radio-warning" 
                                            @checked($data['status'] === 'late') />
                                    </td>
                                    
                                    {{-- Left Early --}}
                                    <td class="text-center">
                                        <input type="radio" 
                                            name="status_{{ $studentId }}"
                                            wire:click="setStatus({{ $studentId }}, 'left_early')"
                                            class="radio radio-warning" 
                                            @checked($data['status'] === 'left_early') />
                                    </td>
                                    
                                    {{-- Note --}}
                                    <td>
                                        <input type="text" 
                                            wire:model="attendances.{{ $studentId }}.note"
                                            class="input input-bordered input-sm w-full" 
                                            placeholder="{{ __('Optional note') }}" />
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Save Button --}}
                <div class="flex justify-end mt-4">
                    <button wire:click="save" class="btn btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                        {{ __('Save Attendance') }}
                    </button>
                </div>
            </div>
        </div>
    @else
        {{-- Empty State --}}
        <div class="card bg-base-100 shadow">
            <div class="card-body text-center py-16">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-20 h-20 mx-auto text-base-content/30 mb-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
                <h3 class="text-xl font-bold mb-2">{{ __('Select date, class and time slot') }}</h3>
                <p class="text-base-content/60">{{ __('Choose filters above to mark attendance for a specific class period.') }}</p>
            </div>
        </div>
    @endif
</div>

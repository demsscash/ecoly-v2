<div>
    {{-- Header --}}
    <div class="mb-6">
        <h1 class="text-2xl font-bold">{{ __('Attendance Marking') }}</h1>
        <p class="text-base-content/60">{{ __('Mark student attendance') }}</p>
    </div>

    {{-- Filters --}}
    <div class="card bg-base-100 shadow mb-6">
        <div class="card-body">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                {{-- Date --}}
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">{{ __('Date') }}</span>
                    </label>
                    <input type="date" wire:model.live="selectedDate" class="input input-bordered" />
                </div>

                {{-- Class --}}
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">{{ __('Class') }}</span>
                    </label>
                    <select wire:model.live="selectedClassId" class="select select-bordered">
                        <option value="">{{ __('Select a class') }}</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}">{{ $class->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Quick actions --}}
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">{{ __('Quick Actions') }}</span>
                    </label>
                    <button wire:click="markAllPresent" class="btn btn-outline btn-success w-full" 
                        @if(empty($attendances)) disabled @endif>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                        </svg>
                        {{ __('Mark All Present') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    @if($selectedClassId && !empty($attendances))
        {{-- Attendance Table --}}
        <div class="card bg-base-100 shadow">
            <div class="card-body">
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
                                <th class="text-center">📝 {{ __('Justified') }}</th>
                                <th class="text-center">🏃 {{ __('Left Early') }}</th>
                                <th>{{ __('Note') }}</th>
                            </tr>
                        </thead>
                        <tbody wire:key="attendance-table-{{ $refreshKey }}">
                            @foreach($attendances as $studentId => $data)
                                <tr wire:key="student-{{ $studentId }}-{{ $refreshKey }}">
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
                                    
                                    {{-- Justified --}}
                                    <td class="text-center">
                                        <input type="radio" 
                                            name="status_{{ $studentId }}"
                                            wire:click="setStatus({{ $studentId }}, 'justified')"
                                            class="radio radio-info" 
                                            @checked($data['status'] === 'justified') />
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
                                            class="input input-bordered input-sm w-full max-w-xs" 
                                            placeholder="{{ __('Optional note') }}" />
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Save Button --}}
                <div class="card-actions justify-end mt-4">
                    <button wire:click="save" class="btn btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                        {{ __('Save Attendance') }}
                    </button>
                </div>
            </div>
        </div>
    @elseif($selectedClassId)
        <div class="alert alert-info">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-current shrink-0 w-6 h-6">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span>{{ __('No students found in this class.') }}</span>
        </div>
    @else
        <div class="alert alert-info">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-current shrink-0 w-6 h-6">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span>{{ __('Please select a class and date to mark attendance.') }}</span>
        </div>
    @endif
</div>

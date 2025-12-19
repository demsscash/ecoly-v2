<div>
    {{-- Header --}}
    <div class="mb-6">
        <h1 class="text-2xl font-bold">{{ __('Justifications') }}</h1>
        <p class="text-base-content/60">{{ __('Manage attendance justifications') }}</p>
    </div>

    {{-- Filters --}}
    <div class="card bg-base-100 shadow mb-6">
        <div class="card-body">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                {{-- Search --}}
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">{{ __('Search') }}</span>
                    </label>
                    <input type="text" wire:model.live.debounce.300ms="search" 
                        placeholder="{{ __('Student name or matricule') }}" 
                        class="input input-bordered" />
                </div>

                {{-- Class --}}
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">{{ __('Class') }}</span>
                    </label>
                    <select wire:model.live="filterClass" class="select select-bordered">
                        <option value="">{{ __('All classes') }}</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}">{{ $class->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Status --}}
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">{{ __('Status') }}</span>
                    </label>
                    <select wire:model.live="filterStatus" class="select select-bordered">
                        <option value="">{{ __('All statuses') }}</option>
                        <option value="absent">{{ __('Absent') }}</option>
                        <option value="late">{{ __('Late') }}</option>
                        <option value="left_early">{{ __('Left Early') }}</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="card bg-base-100 shadow">
        <div class="card-body p-0">
            <div class="overflow-x-auto">
                <table class="table table-zebra">
                    <thead>
                        <tr>
                            <th>{{ __('Date') }}</th>
                            <th>{{ __('Student') }}</th>
                            <th>{{ __('Class') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th>{{ __('Marked By') }}</th>
                            <th>{{ __('Note') }}</th>
                            <th>{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($attendances as $attendance)
                            <tr>
                                <td>{{ $attendance->date->format('d/m/Y') }}</td>
                                <td>
                                    <div class="flex items-center gap-3">
                                        <div class="avatar">
                                            <div class="w-10 h-10 rounded-full">
                                                <img src="{{ $attendance->student->photo_url }}" 
                                                    alt="{{ $attendance->student->full_name }}" />
                                            </div>
                                        </div>
                                        <div>
                                            <div class="font-medium">{{ $attendance->student->full_name }}</div>
                                            <div class="text-sm text-base-content/60">{{ $attendance->student->matricule }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $attendance->student->class?->name ?? '-' }}</td>
                                <td>
                                    <span class="badge {{ $attendance->getStatusBadgeClass() }}">
                                        {{ $attendance->getStatusLabel() }}
                                    </span>
                                </td>
                                <td>{{ $attendance->markedBy?->first_name }} {{ $attendance->markedBy?->last_name }}</td>
                                <td>
                                    @if($attendance->justification_note)
                                        <div class="tooltip" data-tip="{{ $attendance->justification_note }}">
                                            <span class="text-sm">{{ Str::limit($attendance->justification_note, 30) }}</span>
                                        </div>
                                    @else
                                        <span class="text-base-content/40">-</span>
                                    @endif
                                </td>
                                <td>
                                    <button wire:click="openJustifyModal({{ $attendance->id }})" 
                                        class="btn btn-ghost btn-sm">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                        </svg>
                                        {{ __('Justify') }}
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-8 text-base-content/60">
                                    {{ __('No records found.') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($attendances->hasPages())
                <div class="p-4 border-t border-base-200">
                    {{ $attendances->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- Justify Modal --}}
    @if($showJustifyModal)
    <div class="modal modal-open">
        <div class="modal-box">
            <h3 class="font-bold text-lg mb-4">{{ __('Add Justification') }}</h3>
            
            <form wire:submit="saveJustification">
                <div class="form-control mb-4">
                    <label class="label">
                        <span class="label-text">{{ __('Justification Note') }} *</span>
                    </label>
                    <textarea wire:model="justificationNote" 
                        class="textarea textarea-bordered h-24" 
                        placeholder="{{ __('Reason for absence/lateness...') }}"
                        required></textarea>
                    @error('justificationNote') 
                        <span class="text-error text-sm mt-1">{{ $message }}</span> 
                    @enderror
                </div>

                <div class="form-control mb-4">
                    <label class="label">
                        <span class="label-text">{{ __('Justification Document') }}</span>
                    </label>
                    <input type="file" wire:model="justificationFile" 
                        accept=".pdf,.jpg,.jpeg,.png"
                        class="file-input file-input-bordered w-full" />
                    <label class="label">
                        <span class="label-text-alt">{{ __('PDF, JPG, PNG - Max 2MB') }}</span>
                    </label>
                    @error('justificationFile') 
                        <span class="text-error text-sm mt-1">{{ $message }}</span> 
                    @enderror
                </div>

                <div class="modal-action">
                    <button type="button" wire:click="$set('showJustifyModal', false)" 
                        class="btn">
                        {{ __('Cancel') }}
                    </button>
                    <button type="submit" class="btn btn-primary">
                        {{ __('Save') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>

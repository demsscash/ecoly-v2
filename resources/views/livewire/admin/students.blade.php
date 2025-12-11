<div x-data="{ showModal: false, editMode: false }">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold">{{ __('Students') }}</h1>
            <p class="text-base-content/60">{{ __('Manage student enrollment') }}</p>
        </div>
        <button 
            @click="showModal = true; editMode = false; $wire.create()"
            class="btn btn-primary"
        >
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            {{ __('New Student') }}
        </button>
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
                    <label class="text-sm font-medium">{{ __('Class') }}:</label>
                    <select wire:model.live="selectedClassId" class="select select-bordered select-sm w-40 ml-2">
                        <option value="">{{ __('All classes') }}</option>
                        @foreach ($classes as $class)
                            <option value="{{ $class->id }}">{{ $class->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="text-sm font-medium">{{ __('Status') }}:</label>
                    <select wire:model.live="statusFilter" class="select select-bordered select-sm w-36 ml-2">
                        <option value="">{{ __('All') }}</option>
                        @foreach ($statuses as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex-1 max-w-xs">
                    <input 
                        type="text"
                        wire:model.live.debounce.300ms="search"
                        placeholder="{{ __('Search...') }}"
                        class="input input-bordered input-sm w-full"
                    />
                </div>
            </div>
        </div>
    </div>

    {{-- Students Table --}}
    <div class="card bg-base-100 shadow">
        <div class="overflow-x-auto">
            <table class="table">
                <thead>
                    <tr>
                        <th>{{ __('Matricule') }}</th>
                        <th>{{ __('Name') }}</th>
                        <th>{{ __('Class') }}</th>
                        <th>{{ __('Guardian') }}</th>
                        <th>{{ __('Phone') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th>{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($students as $student)
                        <tr>
                            <td class="font-mono">{{ $student->matricule }}</td>
                            <td>
                                <div class="font-medium">{{ $student->full_name }}</div>
                                @if ($student->first_name_ar)
                                    <div class="text-sm text-base-content/60" dir="rtl">{{ $student->full_name_ar }}</div>
                                @endif
                            </td>
                            <td>{{ $student->class?->name ?? '-' }}</td>
                            <td>{{ $student->guardian_name }}</td>
                            <td>{{ $student->guardian_phone }}</td>
                            <td>
                                <select 
                                    wire:change="updateStatus({{ $student->id }}, $event.target.value)"
                                    class="select select-bordered select-xs w-28"
                                >
                                    @foreach ($statuses as $value => $label)
                                        <option value="{{ $value }}" @selected($student->status === $value)>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <div class="flex items-center gap-1">
                                    <button 
                                        @click="showModal = true; editMode = true; $wire.edit({{ $student->id }})"
                                        class="btn btn-ghost btn-xs"
                                        title="{{ __('Edit') }}"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                        </svg>
                                    </button>
                                    
                                    <button 
                                        wire:click="delete({{ $student->id }})"
                                        wire:confirm="{{ __('Delete this student?') }}"
                                        class="btn btn-ghost btn-xs text-error"
                                        title="{{ __('Delete') }}"
                                    >
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
                                {{ __('No students found.') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if ($students->hasPages())
            <div class="p-4 border-t border-base-200">
                {{ $students->links() }}
            </div>
        @endif
    </div>

    {{-- Modal --}}
    <div 
        x-show="showModal" 
        x-cloak
        style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; z-index: 9999; overflow-y: auto;"
    >
        <div style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; background-color: rgba(0,0,0,0.5);" @click="showModal = false"></div>
        
        <div style="display: flex; min-height: 100vh; align-items: flex-start; justify-content: center; padding: 2rem 1rem;">
            <div 
                style="position: relative; background-color: #ffffff; border-radius: 0.5rem; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25); width: 100%; max-width: 48rem; padding: 1.5rem;"
                class="bg-base-100"
                @click.stop
            >
                <button @click="showModal = false" style="position: absolute; right: 0.5rem; top: 0.5rem;" class="btn btn-sm btn-circle btn-ghost">✕</button>
                
                <h3 class="font-bold text-lg mb-4">
                    <span x-show="!editMode">{{ __('New Student') }}</span>
                    <span x-show="editMode">{{ __('Edit Student') }}</span>
                </h3>
                
                <form wire:submit="save" class="space-y-6">
                    {{-- Student Info --}}
                    <div>
                        <h4 class="font-medium text-sm text-base-content/60 mb-3">{{ __('Student Information') }}</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium mb-1">{{ __('First Name') }} <span class="text-error">*</span></label>
                                <input type="text" wire:model="first_name" class="input input-bordered w-full input-sm" />
                                @error('first_name') <p class="text-error text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-1">{{ __('Last Name') }} <span class="text-error">*</span></label>
                                <input type="text" wire:model="last_name" class="input input-bordered w-full input-sm" />
                                @error('last_name') <p class="text-error text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-1">{{ __('First Name') }} (AR)</label>
                                <input type="text" wire:model="first_name_ar" dir="rtl" class="input input-bordered w-full input-sm" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-1">{{ __('Last Name') }} (AR)</label>
                                <input type="text" wire:model="last_name_ar" dir="rtl" class="input input-bordered w-full input-sm" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-1">{{ __('Birth Date') }} <span class="text-error">*</span></label>
                                <input type="date" wire:model="birth_date" class="input input-bordered w-full input-sm" />
                                @error('birth_date') <p class="text-error text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-1">{{ __('Gender') }} <span class="text-error">*</span></label>
                                <select wire:model="gender" class="select select-bordered w-full select-sm">
                                    <option value="male">{{ __('Male') }}</option>
                                    <option value="female">{{ __('Female') }}</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-1">{{ __('Birth Place') }}</label>
                                <input type="text" wire:model="birth_place" class="input input-bordered w-full input-sm" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-1">{{ __('Nationality') }}</label>
                                <input type="text" wire:model="nationality" class="input input-bordered w-full input-sm" />
                            </div>
                        </div>
                    </div>

                    {{-- Guardian Info --}}
                    <div>
                        <h4 class="font-medium text-sm text-base-content/60 mb-3">{{ __('Guardian Information') }}</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium mb-1">{{ __('Guardian Name') }} <span class="text-error">*</span></label>
                                <input type="text" wire:model="guardian_name" class="input input-bordered w-full input-sm" />
                                @error('guardian_name') <p class="text-error text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-1">{{ __('Guardian Name') }} (AR)</label>
                                <input type="text" wire:model="guardian_name_ar" dir="rtl" class="input input-bordered w-full input-sm" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-1">{{ __('Phone') }} <span class="text-error">*</span></label>
                                <input type="text" wire:model="guardian_phone" class="input input-bordered w-full input-sm" />
                                @error('guardian_phone') <p class="text-error text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-1">{{ __('Phone 2') }}</label>
                                <input type="text" wire:model="guardian_phone_2" class="input input-bordered w-full input-sm" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-1">{{ __('Email') }}</label>
                                <input type="email" wire:model="guardian_email" class="input input-bordered w-full input-sm" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-1">{{ __('Profession') }}</label>
                                <input type="text" wire:model="guardian_profession" class="input input-bordered w-full input-sm" />
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium mb-1">{{ __('Address') }}</label>
                                <input type="text" wire:model="address" class="input input-bordered w-full input-sm" />
                            </div>
                        </div>
                    </div>

                    {{-- School Info --}}
                    <div>
                        <h4 class="font-medium text-sm text-base-content/60 mb-3">{{ __('School Information') }}</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium mb-1">{{ __('Class') }}</label>
                                <select wire:model="class_id" class="select select-bordered w-full select-sm">
                                    <option value="">{{ __('Not assigned') }}</option>
                                    @foreach ($classes as $class)
                                        <option value="{{ $class->id }}">{{ $class->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-1">{{ __('Enrollment Date') }} <span class="text-error">*</span></label>
                                <input type="date" wire:model="enrollment_date" class="input input-bordered w-full input-sm" />
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium mb-1">{{ __('Previous School') }}</label>
                                <input type="text" wire:model="previous_school" class="input input-bordered w-full input-sm" />
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium mb-1">{{ __('Notes') }}</label>
                                <textarea wire:model="notes" rows="2" class="textarea textarea-bordered w-full"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end gap-2">
                        <button type="button" @click="showModal = false" class="btn">{{ __('Cancel') }}</button>
                        <button type="submit" class="btn btn-primary" wire:loading.attr="disabled" @click="showModal = false">
                            <span wire:loading.remove>{{ __('Save') }}</span>
                            <span wire:loading class="loading loading-spinner loading-sm"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

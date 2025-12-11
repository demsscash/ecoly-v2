<div x-data="{ showModal: false, editMode: false }">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold">{{ __('Classes') }}</h1>
            <p class="text-base-content/60">{{ __('Manage classes and sections') }}</p>
        </div>
        <button 
            @click="showModal = true; editMode = false; $wire.create()"
            class="btn btn-primary"
        >
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            {{ __('New Class') }}
        </button>
    </div>

    {{-- Year Filter --}}
    <div class="card bg-base-100 shadow mb-6">
        <div class="card-body py-4">
            <div class="flex items-center gap-4">
                <label class="text-sm font-medium">{{ __('School Year') }}:</label>
                <select 
                    wire:model.live="selectedYearId"
                    class="select select-bordered select-sm w-48"
                >
                    @foreach ($years as $year)
                        <option value="{{ $year->id }}">
                            {{ $year->name }}
                            @if ($year->is_active) ({{ __('Active') }}) @endif
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    {{-- Classes Table --}}
    <div class="card bg-base-100 shadow">
        <div class="overflow-x-auto">
            <table class="table">
                <thead>
                    <tr>
                        <th>{{ __('Name') }}</th>
                        <th>{{ __('Level') }}</th>
                        <th>{{ __('Section') }}</th>
                        <th>{{ __('Grade Base') }}</th>
                        <th>{{ __('Capacity') }}</th>
                        <th>{{ __('Tuition Fee') }}</th>
                        <th>{{ __('Registration Fee') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th>{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($classes as $class)
                        <tr>
                            <td class="font-medium">{{ $class->name }}</td>
                            <td>{{ $class->level_name }}</td>
                            <td>{{ $class->section ?? '-' }}</td>
                            <td>{{ $class->grade_base }}</td>
                            <td>{{ $class->capacity }}</td>
                            <td>{{ number_format($class->tuition_fee, 0, ',', ' ') }} MRU</td>
                            <td>{{ number_format($class->registration_fee, 0, ',', ' ') }} MRU</td>
                            <td>
                                @if ($class->is_active)
                                    <span class="badge badge-success">{{ __('Active') }}</span>
                                @else
                                    <span class="badge badge-ghost">{{ __('Inactive') }}</span>
                                @endif
                            </td>
                            <td>
                                <div class="flex items-center gap-1">
                                    {{-- Edit --}}
                                    <button 
                                        @click="showModal = true; editMode = true; $wire.edit({{ $class->id }})"
                                        class="btn btn-ghost btn-xs"
                                        title="{{ __('Edit') }}"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                        </svg>
                                    </button>

                                    {{-- Toggle Active --}}
                                    <button 
                                        wire:click="toggleActive({{ $class->id }})"
                                        class="btn btn-ghost btn-xs {{ $class->is_active ? 'text-warning' : 'text-success' }}"
                                        title="{{ $class->is_active ? __('Deactivate') : __('Activate') }}"
                                    >
                                        @if ($class->is_active)
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                            </svg>
                                        @else
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                            </svg>
                                        @endif
                                    </button>
                                    
                                    {{-- Delete --}}
                                    <button 
                                        wire:click="delete({{ $class->id }})"
                                        wire:confirm="{{ __('Delete this class?') }}"
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
                            <td colspan="9" class="text-center py-8 text-base-content/60">
                                {{ __('No classes found for this school year.') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Modal --}}
    <div 
        x-show="showModal" 
        x-cloak
        style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; z-index: 9999; overflow-y: auto;"
    >
        {{-- Backdrop --}}
        <div 
            style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; background-color: rgba(0,0,0,0.5);" 
            @click="showModal = false"
        ></div>
        
        {{-- Modal Content --}}
        <div style="display: flex; min-height: 100vh; align-items: center; justify-content: center; padding: 1rem;">
            <div 
                style="position: relative; background-color: #ffffff; border-radius: 0.5rem; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25); width: 100%; max-width: 32rem; padding: 1.5rem;"
                class="bg-base-100"
                @click.stop
            >
                {{-- Close button --}}
                <button 
                    @click="showModal = false" 
                    style="position: absolute; right: 0.5rem; top: 0.5rem;"
                    class="btn btn-sm btn-circle btn-ghost"
                >✕</button>
                
                <h3 class="font-bold text-lg mb-4">
                    <span x-show="!editMode">{{ __('New Class') }}</span>
                    <span x-show="editMode">{{ __('Edit Class') }}</span>
                </h3>
                
                <form wire:submit="save" class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        {{-- Level --}}
                        <div class="w-full">
                            <label class="block text-sm font-medium mb-2">
                                {{ __('Level') }} <span class="text-error">*</span>
                            </label>
                            <select 
                                wire:model="level"
                                class="select select-bordered w-full @error('level') select-error @enderror"
                            >
                                <option value="">{{ __('Select level') }}</option>
                                @foreach ($levels as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('level')
                                <p class="text-error text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Section --}}
                        <div class="w-full">
                            <label class="block text-sm font-medium mb-2">
                                {{ __('Section') }}
                            </label>
                            <select 
                                wire:model="section"
                                class="select select-bordered w-full"
                            >
                                <option value="">{{ __('No section') }}</option>
                                @foreach ($sections as $sec)
                                    <option value="{{ $sec }}">{{ $sec }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        {{-- Grade Base --}}
                        <div class="w-full">
                            <label class="block text-sm font-medium mb-2">
                                {{ __('Grade Base') }} <span class="text-error">*</span>
                            </label>
                            <select 
                                wire:model="grade_base"
                                class="select select-bordered w-full"
                            >
                                <option value="10">{{ __('Out of') }} 10</option>
                                <option value="20">{{ __('Out of') }} 20</option>
                            </select>
                        </div>

                        {{-- Capacity --}}
                        <div class="w-full">
                            <label class="block text-sm font-medium mb-2">
                                {{ __('Capacity') }} <span class="text-error">*</span>
                            </label>
                            <input 
                                type="number" 
                                wire:model="capacity"
                                min="1"
                                max="100"
                                class="input input-bordered w-full @error('capacity') input-error @enderror" 
                            />
                            @error('capacity')
                                <p class="text-error text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        {{-- Tuition Fee --}}
                        <div class="w-full">
                            <label class="block text-sm font-medium mb-2">
                                {{ __('Tuition Fee') }} (MRU) <span class="text-error">*</span>
                            </label>
                            <input 
                                type="number" 
                                wire:model="tuition_fee"
                                min="0"
                                step="100"
                                class="input input-bordered w-full @error('tuition_fee') input-error @enderror" 
                            />
                            @error('tuition_fee')
                                <p class="text-error text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Registration Fee --}}
                        <div class="w-full">
                            <label class="block text-sm font-medium mb-2">
                                {{ __('Registration Fee') }} (MRU) <span class="text-error">*</span>
                            </label>
                            <input 
                                type="number" 
                                wire:model="registration_fee"
                                min="0"
                                step="100"
                                class="input input-bordered w-full @error('registration_fee') input-error @enderror" 
                            />
                            @error('registration_fee')
                                <p class="text-error text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="flex justify-end gap-2 mt-6">
                        <button type="button" @click="showModal = false" class="btn">
                            {{ __('Cancel') }}
                        </button>
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

<div x-data="{ showModal: false, editMode: false }">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold">{{ __('School Years') }}</h1>
            <p class="text-base-content/60">{{ __('Manage academic years') }}</p>
        </div>
        <button 
            @click="showModal = true; editMode = false; $wire.create()"
            class="btn btn-primary"
        >
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            {{ __('New Year') }}
        </button>
    </div>

    {{-- Toggle Archived --}}
    <div class="flex items-center gap-2 mb-4">
        <input 
            type="checkbox" 
            wire:model.live="showArchived"
            id="showArchived"
            class="checkbox checkbox-sm" 
        />
        <label for="showArchived" class="text-sm cursor-pointer">
            {{ __('Show archived years') }}
        </label>
    </div>

    {{-- Table --}}
    <div class="card bg-base-100 shadow">
        <div class="overflow-x-auto">
            <table class="table">
                <thead>
                    <tr>
                        <th>{{ __('Name') }}</th>
                        <th>{{ __('Start Date') }}</th>
                        <th>{{ __('End Date') }}</th>
                        <th>{{ __('Payment Months') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th>{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($years as $year)
                        <tr>
                            <td class="font-medium">{{ $year->name }}</td>
                            <td>{{ $year->start_date->format('d/m/Y') }}</td>
                            <td>{{ $year->end_date->format('d/m/Y') }}</td>
                            <td>{{ $year->payment_months }} {{ __('months') }}</td>
                            <td>
                                @if ($year->is_active)
                                    <span class="badge badge-success">{{ __('Active') }}</span>
                                @elseif ($year->is_archived)
                                    <span class="badge badge-ghost">{{ __('Archived') }}</span>
                                @else
                                    <span class="badge badge-warning">{{ __('Inactive') }}</span>
                                @endif
                            </td>
                            <td>
                                <div class="flex items-center gap-1">
                                    {{-- Activate button --}}
                                    @if (!$year->is_active && !$year->is_archived)
                                        <button 
                                            wire:click="activate({{ $year->id }})"
                                            wire:confirm="{{ __('Activate this school year?') }}"
                                            class="btn btn-ghost btn-xs text-success"
                                            title="{{ __('Activate') }}"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                            </svg>
                                        </button>
                                    @endif

                                    {{-- Deactivate button --}}
                                    @if ($year->is_active)
                                        <button 
                                            wire:click="deactivate({{ $year->id }})"
                                            wire:confirm="{{ __('Deactivate this school year?') }}"
                                            class="btn btn-ghost btn-xs text-error"
                                            title="{{ __('Deactivate') }}"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                            </svg>
                                        </button>
                                    @endif
                                    
                                    {{-- Edit button --}}
                                    <button 
                                        @click="showModal = true; editMode = true; $wire.edit({{ $year->id }})"
                                        class="btn btn-ghost btn-xs"
                                        title="{{ __('Edit') }}"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                        </svg>
                                    </button>
                                    
                                    {{-- Archive button --}}
                                    @if (!$year->is_archived && !$year->is_active)
                                        <button 
                                            wire:click="archive({{ $year->id }})"
                                            wire:confirm="{{ __('Archive this school year?') }}"
                                            class="btn btn-ghost btn-xs text-warning"
                                            title="{{ __('Archive') }}"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m20.25 7.5-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5m8.25 3v6.75m0 0-3-3m3 3 3-3M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z" />
                                            </svg>
                                        </button>
                                    @endif
                                    
                                    {{-- Delete button --}}
                                    @if ($year->is_archived)
                                        <button 
                                            wire:click="delete({{ $year->id }})"
                                            wire:confirm="{{ __('Delete this school year permanently?') }}"
                                            class="btn btn-ghost btn-xs text-error"
                                            title="{{ __('Delete') }}"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                            </svg>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-8 text-base-content/60">
                                {{ __('No school years found.') }}
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
        class="fixed inset-0 z-50 overflow-y-auto"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
    >
        {{-- Backdrop --}}
        <div class="fixed inset-0 bg-black/50" @click="showModal = false"></div>
        
        {{-- Modal Content --}}
        <div class="flex min-h-full items-center justify-center p-4">
            <div 
                class="relative bg-base-100 rounded-lg shadow-xl w-full max-w-md p-6"
                @click.stop
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
            >
                {{-- Close button --}}
                <button @click="showModal = false" class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                
                <h3 class="font-bold text-lg mb-4">
                    <span x-show="!editMode">{{ __('New School Year') }}</span>
                    <span x-show="editMode">{{ __('Edit School Year') }}</span>
                </h3>
                
                <form wire:submit="save" class="space-y-4">
                    {{-- Name --}}
                    <div class="w-full">
                        <label class="block text-sm font-medium mb-2">
                            {{ __('Name') }} <span class="text-error">*</span>
                        </label>
                        <input 
                            type="text" 
                            wire:model="name"
                            placeholder="2024-2025"
                            class="input input-bordered w-full @error('name') input-error @enderror" 
                        />
                        @error('name')
                            <p class="text-error text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Start Date --}}
                    <div class="w-full">
                        <label class="block text-sm font-medium mb-2">
                            {{ __('Start Date') }} <span class="text-error">*</span>
                        </label>
                        <input 
                            type="date" 
                            wire:model="start_date"
                            class="input input-bordered w-full @error('start_date') input-error @enderror" 
                        />
                        @error('start_date')
                            <p class="text-error text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- End Date --}}
                    <div class="w-full">
                        <label class="block text-sm font-medium mb-2">
                            {{ __('End Date') }} <span class="text-error">*</span>
                        </label>
                        <input 
                            type="date" 
                            wire:model="end_date"
                            class="input input-bordered w-full @error('end_date') input-error @enderror" 
                        />
                        @error('end_date')
                            <p class="text-error text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Payment Months --}}
                    <div class="w-full">
                        <label class="block text-sm font-medium mb-2">
                            {{ __('Payment Months') }} <span class="text-error">*</span>
                        </label>
                        <select 
                            wire:model="payment_months"
                            class="select select-bordered w-full"
                        >
                            <option value="9">9 {{ __('months') }}</option>
                            <option value="10">10 {{ __('months') }}</option>
                        </select>
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

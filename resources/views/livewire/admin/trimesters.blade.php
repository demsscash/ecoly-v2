<div x-data="{ showModal: false }">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold">{{ __('Trimesters') }}</h1>
            <p class="text-base-content/60">{{ __('Manage trimesters and grading periods') }}</p>
        </div>
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

    {{-- Trimesters Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @forelse ($trimesters as $trimester)
            <div class="card bg-base-100 shadow">
                <div class="card-body">
                    {{-- Status Badge --}}
                    <div class="flex justify-between items-start">
                        <div>
                            <h2 class="card-title">{{ $trimester->name_fr }}</h2>
                            <p class="text-base-content/60" dir="rtl">{{ $trimester->name_ar }}</p>
                        </div>
                        @if ($trimester->status === 'open')
                            <span class="badge badge-success">{{ __('Open') }}</span>
                        @elseif ($trimester->status === 'finalized')
                            <span class="badge badge-info">{{ __('Finalized') }}</span>
                        @else
                            <span class="badge badge-ghost">{{ __('Closed') }}</span>
                        @endif
                    </div>

                    {{-- Dates --}}
                    <div class="mt-4 space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-base-content/60">{{ __('Start Date') }}:</span>
                            <span class="font-medium">{{ $trimester->start_date->format('d/m/Y') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-base-content/60">{{ __('End Date') }}:</span>
                            <span class="font-medium">{{ $trimester->end_date->format('d/m/Y') }}</span>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="card-actions justify-end mt-4">
                        {{-- Edit --}}
                        <button 
                            @click="showModal = true; $wire.edit({{ $trimester->id }})"
                            class="btn btn-ghost btn-sm"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                            </svg>
                            {{ __('Edit') }}
                        </button>

                        {{-- Status Actions --}}
                        @if ($trimester->status === 'closed')
                            <button 
                                wire:click="open({{ $trimester->id }})"
                                wire:confirm="{{ __('Open this trimester for grading?') }}"
                                class="btn btn-success btn-sm"
                            >
                                {{ __('Open') }}
                            </button>
                        @elseif ($trimester->status === 'open')
                            <button 
                                wire:click="close({{ $trimester->id }})"
                                wire:confirm="{{ __('Close this trimester?') }}"
                                class="btn btn-ghost btn-sm"
                            >
                                {{ __('Close') }}
                            </button>
                            <button 
                                wire:click="finalize({{ $trimester->id }})"
                                wire:confirm="{{ __('Finalize this trimester? This will lock all grades.') }}"
                                class="btn btn-info btn-sm"
                            >
                                {{ __('Finalize') }}
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-3 text-center py-12 text-base-content/60">
                {{ __('No trimesters found for this school year.') }}
            </div>
        @endforelse
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
                style="position: relative; background-color: #ffffff; border-radius: 0.5rem; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25); width: 100%; max-width: 28rem; padding: 1.5rem;"
                class="bg-base-100"
                @click.stop
            >
                {{-- Close button --}}
                <button 
                    @click="showModal = false" 
                    style="position: absolute; right: 0.5rem; top: 0.5rem;"
                    class="btn btn-sm btn-circle btn-ghost"
                >✕</button>
                
                <h3 class="font-bold text-lg mb-4">{{ __('Edit Trimester') }}</h3>
                
                <form wire:submit="save" class="space-y-4">
                    {{-- Name FR --}}
                    <div class="w-full">
                        <label class="block text-sm font-medium mb-2">
                            {{ __('Name') }} (FR) <span class="text-error">*</span>
                        </label>
                        <input 
                            type="text" 
                            wire:model="name_fr"
                            class="input input-bordered w-full @error('name_fr') input-error @enderror" 
                        />
                        @error('name_fr')
                            <p class="text-error text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Name AR --}}
                    <div class="w-full">
                        <label class="block text-sm font-medium mb-2">
                            {{ __('Name') }} (AR) <span class="text-error">*</span>
                        </label>
                        <input 
                            type="text" 
                            wire:model="name_ar"
                            dir="rtl"
                            class="input input-bordered w-full @error('name_ar') input-error @enderror" 
                        />
                        @error('name_ar')
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

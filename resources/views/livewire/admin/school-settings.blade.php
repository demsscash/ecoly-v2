<div>
    {{-- Header --}}
    <div class="mb-6">
        <h1 class="text-2xl font-bold">{{ __('School Settings') }}</h1>
        <p class="text-base-content/60">{{ __('Configure your school information') }}</p>
    </div>

    {{-- Success Message --}}
    @if (session('success'))
        <div class="alert alert-success mb-6">
            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <form wire:submit="save">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            
            {{-- Basic Information --}}
            <div class="card bg-base-100 shadow">
                <div class="card-body">
                    <h2 class="card-title text-lg">{{ __('Basic Information') }}</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                        {{-- Name FR --}}
                        <div class="w-full">
                            <label class="block text-sm font-medium mb-2">
                                {{ __('School Name') }} (FR) <span class="text-error">*</span>
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
                                {{ __('School Name') }} (AR) <span class="text-error">*</span>
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

                        {{-- Address FR --}}
                        <div class="w-full">
                            <label class="block text-sm font-medium mb-2">
                                {{ __('Address') }} (FR)
                            </label>
                            <textarea 
                                wire:model="address_fr"
                                rows="2"
                                class="textarea textarea-bordered w-full"
                            ></textarea>
                        </div>

                        {{-- Address AR --}}
                        <div class="w-full">
                            <label class="block text-sm font-medium mb-2">
                                {{ __('Address') }} (AR)
                            </label>
                            <textarea 
                                wire:model="address_ar"
                                dir="rtl"
                                rows="2"
                                class="textarea textarea-bordered w-full"
                            ></textarea>
                        </div>

                        {{-- Phone --}}
                        <div class="w-full">
                            <label class="block text-sm font-medium mb-2">
                                {{ __('Phone') }}
                            </label>
                            <input 
                                type="text" 
                                wire:model="phone"
                                class="input input-bordered w-full" 
                            />
                        </div>

                        {{-- Email --}}
                        <div class="w-full">
                            <label class="block text-sm font-medium mb-2">
                                {{ __('Email') }}
                            </label>
                            <input 
                                type="email" 
                                wire:model="email"
                                class="input input-bordered w-full @error('email') input-error @enderror" 
                            />
                            @error('email')
                                <p class="text-error text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Academic Inspection --}}
                        <div class="w-full">
                            <label class="block text-sm font-medium mb-2">
                                {{ __('Academic Inspection') }}
                            </label>
                            <input 
                                type="text" 
                                wire:model="academic_inspection"
                                class="input input-bordered w-full" 
                            />
                        </div>

                        {{-- School Code --}}
                        <div class="w-full">
                            <label class="block text-sm font-medium mb-2">
                                {{ __('School Code') }}
                            </label>
                            <input 
                                type="text" 
                                wire:model="school_code"
                                class="input input-bordered w-full" 
                            />
                        </div>

                        {{-- Director Name FR --}}
                        <div class="w-full">
                            <label class="block text-sm font-medium mb-2">
                                {{ __('Director Name') }} (FR)
                            </label>
                            <input 
                                type="text" 
                                wire:model="director_name_fr"
                                class="input input-bordered w-full" 
                            />
                        </div>

                        {{-- Director Name AR --}}
                        <div class="w-full">
                            <label class="block text-sm font-medium mb-2">
                                {{ __('Director Name') }} (AR)
                            </label>
                            <input 
                                type="text" 
                                wire:model="director_name_ar"
                                dir="rtl"
                                class="input input-bordered w-full" 
                            />
                        </div>
                    </div>
                </div>
            </div>

            {{-- Images --}}
            <div class="card bg-base-100 shadow">
                <div class="card-body">
                    <h2 class="card-title text-lg">{{ __('Images') }}</h2>
                    
                    <div class="space-y-6 mt-4">
                        {{-- Logo --}}
                        <div>
                            <label class="block text-sm font-medium mb-2">{{ __('Logo') }}</label>
                            <div class="flex items-center gap-4">
                                @if ($school->logo_path)
                                    <div class="relative">
                                        <img src="{{ Storage::url($school->logo_path) }}" alt="Logo" class="w-20 h-20 object-contain border rounded">
                                        <button 
                                            type="button" 
                                            wire:click="deleteLogo"
                                            class="btn btn-circle btn-error btn-xs absolute -top-2 -right-2"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>
                                @endif
                                @if ($logo)
                                    <img src="{{ $logo->temporaryUrl() }}" alt="New Logo" class="w-20 h-20 object-contain border rounded">
                                @endif
                                <input 
                                    type="file" 
                                    wire:model="logo"
                                    accept="image/*"
                                    class="file-input file-input-bordered file-input-sm w-full max-w-xs" 
                                />
                            </div>
                            @error('logo')
                                <p class="text-error text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Stamp --}}
                        <div>
                            <label class="block text-sm font-medium mb-2">{{ __('Stamp') }}</label>
                            <div class="flex items-center gap-4">
                                @if ($school->stamp_path)
                                    <div class="relative">
                                        <img src="{{ Storage::url($school->stamp_path) }}" alt="Stamp" class="w-20 h-20 object-contain border rounded">
                                        <button 
                                            type="button" 
                                            wire:click="deleteStamp"
                                            class="btn btn-circle btn-error btn-xs absolute -top-2 -right-2"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>
                                @endif
                                @if ($stamp)
                                    <img src="{{ $stamp->temporaryUrl() }}" alt="New Stamp" class="w-20 h-20 object-contain border rounded">
                                @endif
                                <input 
                                    type="file" 
                                    wire:model="stamp"
                                    accept="image/*"
                                    class="file-input file-input-bordered file-input-sm w-full max-w-xs" 
                                />
                            </div>
                            @error('stamp')
                                <p class="text-error text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Signature --}}
                        <div>
                            <label class="block text-sm font-medium mb-2">{{ __('Director Signature') }}</label>
                            <div class="flex items-center gap-4">
                                @if ($school->signature_path)
                                    <div class="relative">
                                        <img src="{{ Storage::url($school->signature_path) }}" alt="Signature" class="w-20 h-20 object-contain border rounded">
                                        <button 
                                            type="button" 
                                            wire:click="deleteSignature"
                                            class="btn btn-circle btn-error btn-xs absolute -top-2 -right-2"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>
                                @endif
                                @if ($signature)
                                    <img src="{{ $signature->temporaryUrl() }}" alt="New Signature" class="w-20 h-20 object-contain border rounded">
                                @endif
                                <input 
                                    type="file" 
                                    wire:model="signature"
                                    accept="image/*"
                                    class="file-input file-input-bordered file-input-sm w-full max-w-xs" 
                                />
                            </div>
                            @error('signature')
                                <p class="text-error text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Submit Button --}}
        <div class="mt-6">
            <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                <span wire:loading.remove>{{ __('Save') }}</span>
                <span wire:loading class="loading loading-spinner loading-sm"></span>
            </button>
        </div>
    </form>
</div>

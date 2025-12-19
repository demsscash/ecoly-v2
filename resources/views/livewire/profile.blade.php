<div>
    {{-- Header --}}
    <div class="mb-6">
        <h1 class="text-2xl font-bold">{{ __('My Profile') }}</h1>
        <p class="text-base-content/60">{{ __('Manage your account information and security') }}</p>
    </div>

    {{-- Tabs --}}
    <div class="mb-6">
        <div class="tabs tabs-boxed bg-base-100 shadow p-2 gap-2">
            <button wire:click="$set('activeTab', 'profile')" 
                class="tab gap-2 {{ $activeTab === 'profile' ? 'tab-active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                </svg>
                <span>{{ __('Profile Information') }}</span>
            </button>
            
            <button wire:click="$set('activeTab', 'security')" 
                class="tab gap-2 {{ $activeTab === 'security' ? 'tab-active' : '' }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                </svg>
                <span>{{ __('Security') }}</span>
            </button>
        </div>
    </div>

    {{-- Profile Information Tab --}}
    @if($activeTab === 'profile')
        <div class="card bg-base-100 shadow">
            <div class="card-body">
                <h2 class="card-title mb-6 text-xl">{{ __('Profile Information') }}</h2>

                <form wire:submit="updateProfile">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Photo --}}
                        <div class="col-span-2">
                            <label class="label">
                                <span class="label-text font-semibold text-base">{{ __('Profile Photo') }}</span>
                            </label>
                            
                            <div class="flex items-center gap-6">
                                <div class="avatar">
                                    <div class="w-24 h-24 rounded-full ring ring-primary ring-offset-base-100 ring-offset-2">
                                        @if($photo)
                                            <img src="{{ $photo->temporaryUrl() }}" alt="Preview" />
                                        @else
                                            <img src="{{ $current_photo_url }}" alt="{{ $first_name }} {{ $last_name }}" />
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="flex flex-col gap-3">
                                    <input type="file" wire:model="photo" accept="image/*" class="file-input file-input-bordered w-full max-w-xs" />
                                    
                                    @if(auth()->user()->photo_path)
                                        <button type="button" wire:click="removePhoto" class="btn btn-ghost btn-sm text-error self-start">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                            </svg>
                                            {{ __('Remove Photo') }}
                                        </button>
                                    @endif
                                    
                                    <div class="text-sm text-base-content/60">
                                        {{ __('JPG, PNG or GIF - Max 2MB') }}
                                    </div>
                                    
                                    @error('photo') 
                                        <span class="text-error text-sm">{{ $message }}</span> 
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Divider --}}
                        <div class="col-span-2 divider"></div>

                        {{-- First Name --}}
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-semibold">{{ __('First Name') }} *</span>
                            </label>
                            <input type="text" wire:model="first_name" class="input input-bordered" required />
                            @error('first_name') 
                                <span class="text-error text-sm mt-1">{{ $message }}</span> 
                            @enderror
                        </div>

                        {{-- Last Name --}}
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-semibold">{{ __('Last Name') }} *</span>
                            </label>
                            <input type="text" wire:model="last_name" class="input input-bordered" required />
                            @error('last_name') 
                                <span class="text-error text-sm mt-1">{{ $message }}</span> 
                            @enderror
                        </div>

                        {{-- Email --}}
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-semibold">{{ __('Email') }} *</span>
                            </label>
                            <input type="email" wire:model="email" class="input input-bordered" required />
                            @error('email') 
                                <span class="text-error text-sm mt-1">{{ $message }}</span> 
                            @enderror
                        </div>

                        {{-- Phone --}}
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-semibold">{{ __('Phone') }}</span>
                            </label>
                            <input type="text" wire:model="phone" class="input input-bordered" placeholder="+222 XX XX XX XX" />
                            @error('phone') 
                                <span class="text-error text-sm mt-1">{{ $message }}</span> 
                            @enderror
                        </div>

                        {{-- Divider --}}
                        <div class="col-span-2 divider">{{ __('Account Information') }}</div>

                        {{-- Role (Read-only) --}}
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-semibold">{{ __('Role') }}</span>
                            </label>
                            <input type="text" value="{{ __(ucfirst(auth()->user()->role->value)) }}" class="input input-bordered bg-base-200" disabled />
                        </div>

                        {{-- Created At (Read-only) --}}
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-semibold">{{ __('Member Since') }}</span>
                            </label>
                            <input type="text" value="{{ auth()->user()->created_at->format('d/m/Y') }}" class="input input-bordered bg-base-200" disabled />
                        </div>
                    </div>

                    {{-- Submit Button --}}
                    <div class="card-actions justify-end mt-8 pt-6 border-t border-base-200">
                        <button type="submit" class="btn btn-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                            {{ __('Save Changes') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Security Tab --}}
    @if($activeTab === 'security')
        <div class="card bg-base-100 shadow">
            <div class="card-body">
                <h2 class="card-title mb-6 text-xl">{{ __('Change Password') }}</h2>
                
                <p class="text-base-content/60 mb-6">{{ __('Update your password to keep your account secure.') }}</p>

                <form wire:submit="updatePassword">
                    <div class="grid grid-cols-1 gap-6 max-w-md">
                        {{-- Current Password --}}
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-semibold">{{ __('Current Password') }} *</span>
                            </label>
                            <input type="password" wire:model="current_password" class="input input-bordered" required autocomplete="current-password" />
                            @error('current_password') 
                                <span class="text-error text-sm mt-1">{{ $message }}</span> 
                            @enderror
                        </div>

                        <div class="divider"></div>

                        {{-- New Password --}}
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-semibold">{{ __('New Password') }} *</span>
                            </label>
                            <input type="password" wire:model="new_password" class="input input-bordered" required autocomplete="new-password" />
                            <label class="label">
                                <span class="label-text-alt text-base-content/60">{{ __('Minimum 8 characters') }}</span>
                            </label>
                            @error('new_password') 
                                <span class="text-error text-sm mt-1">{{ $message }}</span> 
                            @enderror
                        </div>

                        {{-- Confirm New Password --}}
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-semibold">{{ __('Confirm New Password') }} *</span>
                            </label>
                            <input type="password" wire:model="new_password_confirmation" class="input input-bordered" required autocomplete="new-password" />
                            @error('new_password_confirmation') 
                                <span class="text-error text-sm mt-1">{{ $message }}</span> 
                            @enderror
                        </div>
                    </div>

                    {{-- Submit Button --}}
                    <div class="card-actions justify-end mt-8 pt-6 border-t border-base-200">
                        <button type="submit" class="btn btn-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                            </svg>
                            {{ __('Update Password') }}
                        </button>
                    </div>
                </form>

                {{-- Security Info --}}
                <div class="alert alert-info mt-6">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-current shrink-0 w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div>
                        <div class="font-semibold">{{ __('Password Security Tips') }}</div>
                        <div class="text-sm">{{ __('Choose a strong password with a mix of letters, numbers, and symbols.') }}</div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<div x-data="{ showModal: false, editMode: false }">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold">{{ __('Users') }}</h1>
            <p class="text-base-content/60">{{ __('Manage system users') }}</p>
        </div>
        <button 
            @click="showModal = true; editMode = false; $wire.create()"
            class="btn btn-primary"
        >
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            {{ __('New User') }}
        </button>
    </div>

    {{-- Search --}}
    <div class="card bg-base-100 shadow mb-6">
        <div class="card-body py-4">
            <div class="flex items-center gap-4">
                <div class="relative flex-1 max-w-sm">
                    <input 
                        type="text"
                        wire:model.live.debounce.300ms="search"
                        placeholder="{{ __('Search users...') }}"
                        class="input input-bordered w-full pl-10"
                    />
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-base-content/40">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Users Table --}}
    <div class="card bg-base-100 shadow">
        <div class="overflow-x-auto">
            <table class="table">
                <thead>
                    <tr>
                        <th>{{ __('Name') }}</th>
                        <th>{{ __('Email') }}</th>
                        <th>{{ __('Phone') }}</th>
                        <th>{{ __('Role') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th>{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                        <tr>
                            <td class="font-medium">{{ $user->first_name }} {{ $user->last_name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->phone ?? '-' }}</td>
                            <td>
                                @if ($user->role->value === 'admin')
                                    <span class="badge badge-primary">{{ __('Admin') }}</span>
                                @elseif ($user->role->value === 'secretary')
                                    <span class="badge badge-secondary">{{ __('Secretary') }}</span>
                                @else
                                    <span class="badge badge-accent">{{ __('Teacher') }}</span>
                                @endif
                            </td>
                            <td>
                                @if ($user->is_active)
                                    <span class="badge badge-success">{{ __('Active') }}</span>
                                @else
                                    <span class="badge badge-ghost">{{ __('Inactive') }}</span>
                                @endif
                            </td>
                            <td>
                                <div class="flex items-center gap-1">
                                    {{-- Edit --}}
                                    <button 
                                        @click="showModal = true; editMode = true; $wire.edit({{ $user->id }})"
                                        class="btn btn-ghost btn-xs"
                                        title="{{ __('Edit') }}"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                        </svg>
                                    </button>

                                    {{-- Toggle Active --}}
                                    @if ($user->id !== auth()->id())
                                        <button 
                                            wire:click="toggleActive({{ $user->id }})"
                                            class="btn btn-ghost btn-xs {{ $user->is_active ? 'text-warning' : 'text-success' }}"
                                            title="{{ $user->is_active ? __('Deactivate') : __('Activate') }}"
                                        >
                                            @if ($user->is_active)
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="m9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                                </svg>
                                            @else
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                                </svg>
                                            @endif
                                        </button>
                                    @endif

                                    {{-- Reset Password --}}
                                    <button 
                                        wire:click="resetPassword({{ $user->id }})"
                                        wire:confirm="{{ __('Reset password to default?') }}"
                                        class="btn btn-ghost btn-xs text-info"
                                        title="{{ __('Reset Password') }}"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                                        </svg>
                                    </button>
                                    
                                    {{-- Delete --}}
                                    @if ($user->id !== auth()->id())
                                        <button 
                                            wire:click="delete({{ $user->id }})"
                                            wire:confirm="{{ __('Delete this user?') }}"
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
                                {{ __('No users found.') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{-- Pagination --}}
        @if ($users->hasPages())
            <div class="p-4 border-t border-base-200">
                {{ $users->links() }}
            </div>
        @endif
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
                
                <h3 class="font-bold text-lg mb-4">
                    <span x-show="!editMode">{{ __('New User') }}</span>
                    <span x-show="editMode">{{ __('Edit User') }}</span>
                </h3>
                
                <form wire:submit="save" class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        {{-- First Name --}}
                        <div class="w-full">
                            <label class="block text-sm font-medium mb-2">
                                {{ __('First Name') }} <span class="text-error">*</span>
                            </label>
                            <input 
                                type="text" 
                                wire:model="first_name"
                                class="input input-bordered w-full @error('first_name') input-error @enderror" 
                            />
                            @error('first_name')
                                <p class="text-error text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Last Name --}}
                        <div class="w-full">
                            <label class="block text-sm font-medium mb-2">
                                {{ __('Last Name') }} <span class="text-error">*</span>
                            </label>
                            <input 
                                type="text" 
                                wire:model="last_name"
                                class="input input-bordered w-full @error('last_name') input-error @enderror" 
                            />
                            @error('last_name')
                                <p class="text-error text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Email --}}
                    <div class="w-full">
                        <label class="block text-sm font-medium mb-2">
                            {{ __('Email') }} <span class="text-error">*</span>
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

                    {{-- Phone --}}
                    <div class="w-full">
                        <label class="block text-sm font-medium mb-2">
                            {{ __('Phone') }}
                        </label>
                        <input 
                            type="text" 
                            wire:model="phone"
                            class="input input-bordered w-full @error('phone') input-error @enderror" 
                        />
                        @error('phone')
                            <p class="text-error text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Role --}}
                    <div class="w-full">
                        <label class="block text-sm font-medium mb-2">
                            {{ __('Role') }} <span class="text-error">*</span>
                        </label>
                        <select 
                            wire:model="role"
                            class="select select-bordered w-full"
                        >
                            @foreach ($roles as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Password --}}
                    <div class="w-full">
                        <label class="block text-sm font-medium mb-2">
                            {{ __('Password') }} 
                            @if (!$editingId)<span class="text-error">*</span>@endif
                            @if ($editingId)<span class="text-base-content/60 text-xs">({{ __('Leave empty to keep current') }})</span>@endif
                        </label>
                        <input 
                            type="password" 
                            wire:model="password"
                            class="input input-bordered w-full @error('password') input-error @enderror" 
                        />
                        @error('password')
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

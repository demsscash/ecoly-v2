<div>
    <div class="card bg-base-100 shadow-xl">
        <div class="card-body">
            
            {{-- Logo --}}
            <div class="flex flex-col items-center mb-6">
                <div class="w-16 h-16 rounded-xl bg-primary flex items-center justify-center mb-4">
                    <span class="text-white font-bold text-3xl">E</span>
                </div>
                <h1 class="text-2xl font-bold">Ecoly</h1>
                <p class="text-base-content/60">{{ __('Sign in') }}</p>
            </div>
            
            {{-- Login Form --}}
            <form wire:submit="login" class="space-y-4">
                
                {{-- Email --}}
                <div class="w-full">
                    <label class="block text-sm font-medium mb-2">
                        {{ __('Email') }}
                    </label>
                    <input 
                        type="email" 
                        wire:model="email"
                        placeholder="admin@ecoly.mr" 
                        class="input input-bordered w-full @error('email') input-error @enderror" 
                        autofocus
                    />
                    @error('email')
                        <p class="text-error text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                {{-- Password --}}
                <div class="w-full">
                    <label class="block text-sm font-medium mb-2">
                        {{ __('Password') }}
                    </label>
                    <input 
                        type="password" 
                        wire:model="password"
                        placeholder="••••••••" 
                        class="input input-bordered w-full @error('password') input-error @enderror" 
                    />
                    @error('password')
                        <p class="text-error text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                {{-- Remember Me --}}
                <div class="flex items-center gap-2">
                    <input 
                        type="checkbox" 
                        wire:model="remember"
                        id="remember"
                        class="checkbox checkbox-primary checkbox-sm" 
                    />
                    <label for="remember" class="text-sm cursor-pointer">
                        {{ __('Remember me') }}
                    </label>
                </div>
                
                {{-- Submit Button --}}
                <button 
                    type="submit" 
                    class="btn btn-primary w-full"
                    wire:loading.attr="disabled"
                >
                    <span wire:loading.remove>{{ __('Sign in') }}</span>
                    <span wire:loading class="loading loading-spinner loading-sm"></span>
                </button>
                
            </form>
            
            {{-- Language Switcher --}}
            <div class="divider"></div>
            <div class="flex justify-center">
                <livewire:language-switcher />
            </div>
            
        </div>
    </div>
</div>

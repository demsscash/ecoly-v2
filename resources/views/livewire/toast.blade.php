<div style="position: fixed; bottom: 24px; right: 24px; z-index: 99999; display: flex; flex-direction: column; gap: 12px;">
    @foreach ($toasts as $toast)
        <div 
            x-data="{ show: true }"
            x-init="setTimeout(() => { show = false; $wire.removeToast('{{ $toast['id'] }}') }, 3000)"
            x-show="show"
            x-cloak
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform translate-x-full"
            x-transition:enter-end="opacity-100 transform translate-x-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 transform translate-x-0"
            x-transition:leave-end="opacity-0 transform translate-x-full"
            wire:key="{{ $toast['id'] }}"
            style="display: flex; align-items: center; gap: 12px; padding: 16px; border-radius: 8px; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); min-width: 320px; max-width: 420px; border-left: 4px solid; {{ $toast['type'] === 'success' ? 'background-color: #f0fdf4; border-color: #22c55e; color: #166534;' : '' }}{{ $toast['type'] === 'error' ? 'background-color: #fef2f2; border-color: #ef4444; color: #991b1b;' : '' }}{{ $toast['type'] === 'warning' ? 'background-color: #fffbeb; border-color: #f59e0b; color: #92400e;' : '' }}{{ $toast['type'] === 'info' ? 'background-color: #eff6ff; border-color: #3b82f6; color: #1e40af;' : '' }}"
        >
            @if ($toast['type'] === 'success')
                <svg xmlns="http://www.w3.org/2000/svg" style="width: 24px; height: 24px; flex-shrink: 0; color: #22c55e;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            @elseif ($toast['type'] === 'error')
                <svg xmlns="http://www.w3.org/2000/svg" style="width: 24px; height: 24px; flex-shrink: 0; color: #ef4444;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            @elseif ($toast['type'] === 'warning')
                <svg xmlns="http://www.w3.org/2000/svg" style="width: 24px; height: 24px; flex-shrink: 0; color: #f59e0b;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            @else
                <svg xmlns="http://www.w3.org/2000/svg" style="width: 24px; height: 24px; flex-shrink: 0; color: #3b82f6;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            @endif
            <span style="flex: 1; font-size: 14px; font-weight: 500;">{{ $toast['message'] }}</span>
            <button 
                @click="show = false; $wire.removeToast('{{ $toast['id'] }}')" 
                style="flex-shrink: 0; opacity: 0.5; cursor: pointer; background: none; border: none; padding: 4px;"
                onmouseover="this.style.opacity='1'" 
                onmouseout="this.style.opacity='0.5'"
            >
                <svg xmlns="http://www.w3.org/2000/svg" style="width: 16px; height: 16px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    @endforeach
</div>

<div>
    <x-dropdown>
        <x-slot:trigger>
            <x-button icon="o-language" class="btn-ghost btn-sm">
                {{ $currentLocale === 'fr' ? 'FR' : 'AR' }}
            </x-button>
        </x-slot:trigger>
        <x-menu-item 
            title="Français" 
            wire:click="switchLanguage('fr')"
            class="{{ $currentLocale === 'fr' ? 'bg-base-200' : '' }}" 
        />
        <x-menu-item 
            title="العربية" 
            wire:click="switchLanguage('ar')"
            class="{{ $currentLocale === 'ar' ? 'bg-base-200' : '' }}" 
        />
    </x-dropdown>
</div>

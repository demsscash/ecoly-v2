<div class="dropdown dropdown-end">
    <label tabindex="0" class="btn btn-ghost btn-sm">
        {{ $currentLocale === 'fr' ? 'FR' : 'AR' }}
    </label>
    <ul tabindex="0" class="dropdown-content menu p-2 shadow-lg bg-base-100 rounded-xl w-32 mt-2 border border-base-200">
        <li>
            <button wire:click="switchLanguage('fr')" class="{{ $currentLocale === 'fr' ? 'bg-base-200' : '' }}">
                Français
            </button>
        </li>
        <li>
            <button wire:click="switchLanguage('ar')" class="{{ $currentLocale === 'ar' ? 'bg-base-200' : '' }}">
                العربية
            </button>
        </li>
    </ul>
</div>

<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class LanguageSwitcher extends Component
{
    public string $currentLocale;

    /**
     * Initialize component with current locale.
     */
    public function mount(): void
    {
        $this->currentLocale = App::getLocale();
    }

    /**
     * Switch application language.
     */
    public function switchLanguage(string $locale): void
    {
        if (in_array($locale, ['fr', 'ar'])) {
            Session::put('locale', $locale);
            $this->currentLocale = $locale;
            
            $this->redirect(request()->header('Referer', '/'), navigate: true);
        }
    }

    /**
     * Render the component.
     */
    public function render()
    {
        return view('livewire.language-switcher');
    }
}

<?php

namespace App\Livewire;

use Livewire\Component;

class ThemeToggle extends Component
{
    public string $theme = 'light';

    /**
     * Initialize component with theme from session or default.
     */
    public function mount(): void
    {
        $this->theme = session('theme', 'light');
    }

    /**
     * Toggle between light and dark theme.
     */
    public function toggleTheme(): void
    {
        $this->theme = $this->theme === 'light' ? 'dark' : 'light';
        session(['theme' => $this->theme]);
        
        $this->dispatch('theme-changed', theme: $this->theme);
    }

    /**
     * Render the component.
     */
    public function render()
    {
        return view('livewire.theme-toggle');
    }
}

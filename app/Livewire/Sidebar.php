<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Reactive;

class Sidebar extends Component
{
    public bool $collapsed = false;
    public array $collapsedSections = [];

    public function mount(): void
    {
        // Load collapsed state from session
        $this->collapsed = session('sidebar_collapsed', false);
        $this->collapsedSections = session('collapsed_sections', []);
    }

    public function toggleCollapse(): void
    {
        $this->collapsed = !$this->collapsed;
        session(['sidebar_collapsed' => $this->collapsed]);
    }

    public function toggleSection(string $section): void
    {
        if (in_array($section, $this->collapsedSections)) {
            $this->collapsedSections = array_diff($this->collapsedSections, [$section]);
        } else {
            $this->collapsedSections[] = $section;
        }
        session(['collapsed_sections' => $this->collapsedSections]);
    }

    public function isSectionCollapsed(string $section): bool
    {
        return in_array($section, $this->collapsedSections);
    }

    public function render()
    {
        return view('livewire.sidebar');
    }
}

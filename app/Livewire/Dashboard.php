<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.app')]
#[Title('Tableau de bord - Ecoly')]
class Dashboard extends Component
{
    /**
     * Render the component.
     */
    public function render()
    {
        return view('livewire.dashboard');
    }
}

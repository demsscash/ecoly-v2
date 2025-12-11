<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;

class Toast extends Component
{
    public array $toasts = [];

    #[On('toast')]
    public function showToast(string $message, string $type = 'success'): void
    {
        $this->toasts[] = [
            'id' => uniqid(),
            'message' => $message,
            'type' => $type,
        ];
    }

    public function removeToast(string $id): void
    {
        $this->toasts = array_filter($this->toasts, fn($toast) => $toast['id'] !== $id);
    }

    public function render()
    {
        return view('livewire.toast');
    }
}

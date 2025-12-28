<?php

namespace App\Livewire\Admin;

use App\Models\Serie;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.app')]
#[Title('Séries - Ecoly')]
class Series extends Component
{
    use WithPagination;

    public bool $showModal = false;
    public ?int $editingSerieId = null;
    
    public string $name = '';
    public string $code = '';
    public string $description = '';

    public function openCreateModal(): void
    {
        $this->reset(['name', 'code', 'description', 'editingSerieId']);
        $this->showModal = true;
    }

    public function openEditModal(int $serieId): void
    {
        $serie = Serie::findOrFail($serieId);
        
        $this->editingSerieId = $serie->id;
        $this->name = $serie->name;
        $this->code = $serie->code;
        $this->description = $serie->description ?? '';
        
        $this->showModal = true;
    }

    public function save(): void
    {
        // Fix: Ensure null instead of empty string for unique validation
        $ignoreId = $this->editingSerieId ?: null;
        
        $this->validate([
            'name' => 'required|string|max:255',
            'code' => [
                'required',
                'string',
                'max:10',
                'unique:series,code,' . ($ignoreId ?? 'NULL')
            ],
            'description' => 'nullable|string|max:1000',
        ]);

        if ($this->editingSerieId) {
            // Update
            $serie = Serie::findOrFail($this->editingSerieId);
            $serie->update([
                'name' => $this->name,
                'code' => $this->code,
                'description' => $this->description,
            ]);
            
            $message = __('Serie updated successfully.');
        } else {
            // Create
            Serie::create([
                'name' => $this->name,
                'code' => $this->code,
                'description' => $this->description,
            ]);
            
            $message = __('Serie created successfully.');
        }

        $this->showModal = false;
        $this->reset(['name', 'code', 'description', 'editingSerieId']);
        
        $this->dispatch('toast', message: $message, type: 'success');
    }

    public function delete(int $serieId): void
    {
        $serie = Serie::findOrFail($serieId);
        
        // Check if serie is used by any class
        if ($serie->classes()->count() > 0) {
            $this->dispatch('toast', 
                message: __('Cannot delete serie that is assigned to classes.'), 
                type: 'error'
            );
            return;
        }
        
        $serie->delete();
        
        $this->dispatch('toast', message: __('Serie deleted successfully.'), type: 'success');
    }

    public function render()
    {
        $series = Serie::withCount('classes')
            ->orderBy('name')
            ->paginate(10);

        return view('livewire.admin.series', [
            'series' => $series,
        ]);
    }
}

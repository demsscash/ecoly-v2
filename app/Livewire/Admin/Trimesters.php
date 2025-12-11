<?php

namespace App\Livewire\Admin;

use App\Models\SchoolYear;
use App\Models\Trimester;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.app')]
#[Title('Trimestres - Ecoly')]
class Trimesters extends Component
{
    public ?int $selectedYearId = null;
    public ?int $editingId = null;
    
    public string $name_fr = '';
    public string $name_ar = '';
    public string $start_date = '';
    public string $end_date = '';
    
    public bool $showModal = false;

    public function mount(): void
    {
        $activeYear = SchoolYear::active();
        $this->selectedYearId = $activeYear?->id ?? SchoolYear::latest()->first()?->id;
    }

    protected function rules(): array
    {
        return [
            'name_fr' => 'required|string|max:50',
            'name_ar' => 'required|string|max:50',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ];
    }

    public function edit(int $id): void
    {
        $trimester = Trimester::findOrFail($id);
        
        $this->editingId = $trimester->id;
        $this->name_fr = $trimester->name_fr;
        $this->name_ar = $trimester->name_ar;
        $this->start_date = $trimester->start_date->format('Y-m-d');
        $this->end_date = $trimester->end_date->format('Y-m-d');
        
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate();

        $trimester = Trimester::findOrFail($this->editingId);
        $trimester->update([
            'name_fr' => $this->name_fr,
            'name_ar' => $this->name_ar,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
        ]);

        $this->dispatch('toast', message: __('Trimester updated successfully.'), type: 'success');
        $this->showModal = false;
        $this->resetForm();
    }

    public function open(int $id): void
    {
        $trimester = Trimester::findOrFail($id);
        
        // Close other open trimesters for the same year
        Trimester::where('school_year_id', $trimester->school_year_id)
            ->where('status', 'open')
            ->update(['status' => 'closed']);
        
        $trimester->update(['status' => 'open']);
        
        $this->dispatch('toast', message: __('Trimester opened successfully.'), type: 'success');
    }

    public function close(int $id): void
    {
        $trimester = Trimester::findOrFail($id);
        $trimester->update(['status' => 'closed']);
        
        $this->dispatch('toast', message: __('Trimester closed successfully.'), type: 'success');
    }

    public function finalize(int $id): void
    {
        $trimester = Trimester::findOrFail($id);
        $trimester->update(['status' => 'finalized']);
        
        $this->dispatch('toast', message: __('Trimester finalized successfully.'), type: 'warning');
    }

    private function resetForm(): void
    {
        $this->editingId = null;
        $this->name_fr = '';
        $this->name_ar = '';
        $this->start_date = '';
        $this->end_date = '';
    }

    public function render()
    {
        $years = SchoolYear::orderByDesc('start_date')->get();
        
        $trimesters = $this->selectedYearId 
            ? Trimester::where('school_year_id', $this->selectedYearId)
                ->orderBy('number')
                ->get()
            : collect();

        return view('livewire.admin.trimesters', [
            'years' => $years,
            'trimesters' => $trimesters,
        ]);
    }
}

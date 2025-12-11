<?php

namespace App\Livewire\Admin;

use App\Models\SchoolClass;
use App\Models\SchoolYear;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.app')]
#[Title('Classes - Ecoly')]
class Classes extends Component
{
    public ?int $selectedYearId = null;
    public ?int $editingId = null;
    
    public string $level = '';
    public string $section = '';
    public int $grade_base = 10;
    public int $capacity = 40;
    public string $tuition_fee = '0';
    public string $registration_fee = '0';
    
    public bool $showModal = false;

    public function mount(): void
    {
        $activeYear = SchoolYear::active();
        $this->selectedYearId = $activeYear?->id ?? SchoolYear::latest()->first()?->id;
    }

    protected function rules(): array
    {
        return [
            'level' => 'required|string|in:1,2,3,4,5,6',
            'section' => 'nullable|string|max:5',
            'grade_base' => 'required|integer|in:10,20',
            'capacity' => 'required|integer|min:1|max:100',
            'tuition_fee' => 'required|numeric|min:0',
            'registration_fee' => 'required|numeric|min:0',
        ];
    }

    public function create(): void
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function edit(int $id): void
    {
        $class = SchoolClass::findOrFail($id);
        
        $this->editingId = $class->id;
        $this->level = $class->level;
        $this->section = $class->section ?? '';
        $this->grade_base = $class->grade_base;
        $this->capacity = $class->capacity;
        $this->tuition_fee = (string) $class->tuition_fee;
        $this->registration_fee = (string) $class->registration_fee;
        
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate();

        $name = $this->generateClassName();

        $data = [
            'school_year_id' => $this->selectedYearId,
            'name' => $name,
            'level' => $this->level,
            'section' => $this->section ?: null,
            'grade_base' => $this->grade_base,
            'capacity' => $this->capacity,
            'tuition_fee' => $this->tuition_fee,
            'registration_fee' => $this->registration_fee,
        ];

        if ($this->editingId) {
            $class = SchoolClass::findOrFail($this->editingId);
            $class->update($data);
            $this->dispatch('toast', message: __('Class updated successfully.'), type: 'success');
        } else {
            SchoolClass::create($data);
            $this->dispatch('toast', message: __('Class created successfully.'), type: 'success');
        }

        $this->showModal = false;
        $this->resetForm();
    }

    private function generateClassName(): string
    {
        $levels = SchoolClass::levels();
        $levelName = $levels[$this->level] ?? $this->level;
        
        return $this->section 
            ? "{$levelName} {$this->section}"
            : $levelName;
    }

    public function toggleActive(int $id): void
    {
        $class = SchoolClass::findOrFail($id);
        $class->update(['is_active' => !$class->is_active]);
        
        $message = $class->is_active 
            ? __('Class activated successfully.') 
            : __('Class deactivated successfully.');
            
        $this->dispatch('toast', message: $message, type: 'success');
    }

    public function delete(int $id): void
    {
        $class = SchoolClass::findOrFail($id);
        
        // Check if has students
        if ($class->students()->exists()) {
            $this->dispatch('toast', message: __('Cannot delete class with students.'), type: 'error');
            return;
        }
        
        $class->delete();
        $this->dispatch('toast', message: __('Class deleted successfully.'), type: 'success');
    }

    private function resetForm(): void
    {
        $this->editingId = null;
        $this->level = '';
        $this->section = '';
        $this->grade_base = 10;
        $this->capacity = 40;
        $this->tuition_fee = '0';
        $this->registration_fee = '0';
    }

    public function render()
    {
        $years = SchoolYear::orderByDesc('start_date')->get();
        
        $classes = $this->selectedYearId 
            ? SchoolClass::forYear($this->selectedYearId)
                ->orderBy('level')
                ->orderBy('section')
                ->get()
            : collect();

        return view('livewire.admin.classes', [
            'years' => $years,
            'classes' => $classes,
            'levels' => SchoolClass::levels(),
            'sections' => SchoolClass::sections(),
        ]);
    }
}

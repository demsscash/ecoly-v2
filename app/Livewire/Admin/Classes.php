<?php

namespace App\Livewire\Admin;

use App\Models\SchoolClass;
use App\Models\SchoolYear;
use App\Models\Serie;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.app')]
#[Title('Classes - Ecoly')]
class Classes extends Component
{
    use WithPagination;

    public bool $showModal = false;
    public ?int $editingClassId = null;

    public int $level = 1;
    public string $class_number = '';
    public string $level_type = 'fondamental';
    public ?int $serie_id = null;
    public ?int $school_year_id = null;

    public function mount(): void
    {
        $activeYear = SchoolYear::where('is_active', true)->first();
        $this->school_year_id = $activeYear?->id;
    }

    public function openCreateModal(): void
    {
        $this->reset(['level', 'class_number', 'level_type', 'serie_id', 'editingClassId']);
        $this->level_type = 'fondamental';
        $this->level = 1;
        $this->showModal = true;
    }

    public function openEditModal(int $classId): void
    {
        $class = SchoolClass::findOrFail($classId);

        $this->editingClassId = $class->id;
        $this->level = $class->level;
        $this->class_number = $class->section;
        $this->level_type = $class->level_type;
        $this->serie_id = $class->serie_id;
        $this->school_year_id = $class->school_year_id;

        $this->showModal = true;
    }

    public function updatedLevelType(): void
    {
        // Auto-adjust level based on type
        if ($this->level_type === 'fondamental') {
            $this->level = 1;
            $this->serie_id = null;
        } elseif ($this->level_type === 'college') {
            $this->level = 1;
            $this->serie_id = null;
        } elseif ($this->level_type === 'lycee') {
            $this->level = 5; // Default to 5ème for lycee
        }
    }

    public function updatedLevel(): void
    {
        if (!$this->requiresSerie()) {
            $this->serie_id = null;
        }
    }

    public function save(): void
    {
        $rules = [
            'level' => 'required|integer|min:1|max:7',
            'class_number' => 'nullable|string|max:10',
            'level_type' => 'required|in:fondamental,college,lycee',
            'school_year_id' => 'required|exists:school_years,id',
        ];

        // Serie is required for lycee levels 5, 6, 7
        if ($this->requiresSerie()) {
            $rules['serie_id'] = 'required|exists:series,id';
            $rules['class_number'] = 'required|string|max:10'; // Required for lycee
        } else {
            $rules['serie_id'] = 'nullable';
        }

        $this->validate($rules);

        // Generate class name automatically
        $name = $this->generateClassName();

        $data = [
            'name' => $name,
            'level' => $this->level,
            'section' => $this->class_number,
            'level_type' => $this->level_type,
            'serie_id' => $this->requiresSerie() ? $this->serie_id : null,
            'school_year_id' => $this->school_year_id,
        ];

        if ($this->editingClassId) {
            $class = SchoolClass::findOrFail($this->editingClassId);
            $class->update($data);
            $message = __('Class updated successfully.');
        } else {
            SchoolClass::create($data);
            $message = __('Class created successfully.');
        }

        $this->showModal = false;
        $this->reset(['level', 'class_number', 'level_type', 'serie_id', 'editingClassId']);

        $this->dispatch('toast', message: $message, type: 'success');
    }

    public function delete(int $classId): void
    {
        $class = SchoolClass::findOrFail($classId);

        if ($class->students()->count() > 0) {
            $this->dispatch('toast',
                message: __('Cannot delete class that has students.'),
                type: 'error'
            );
            return;
        }

        $class->delete();
        $this->dispatch('toast', message: __('Class deleted successfully.'), type: 'success');
    }

    /**
     * Generate class name based on level_type, level, serie, and class_number
     */
    private function generateClassName(): string
    {
        $levelSuffix = $this->level === 1 ? 'ère' : 'ème';
        
        if ($this->level_type === 'fondamental') {
            // Format: "1ère 1", "2ème 2", etc.
            $name = "{$this->level}{$levelSuffix}";
            if ($this->class_number) {
                $name .= " {$this->class_number}";
            }
            return $name;
        }
        
        if ($this->level_type === 'college') {
            // Format: "1ère Collège 1", "3ème Collège 2", etc.
            $name = "{$this->level}{$levelSuffix} Collège";
            if ($this->class_number) {
                $name .= " {$this->class_number}";
            }
            return $name;
        }
        
        // Lycee
        // Format: "7ème C1", "6ème D2", "5ème LIT1"
        $serie = Serie::find($this->serie_id);
        $serieCode = $serie ? $serie->code : '';
        
        return "{$this->level}{$levelSuffix} {$serieCode}{$this->class_number}";
    }

    /**
     * Check if current level_type and level require a serie
     */
    private function requiresSerie(): bool
    {
        return $this->level_type === 'lycee' && in_array($this->level, [5, 6, 7]);
    }

    public function render()
    {
        $classes = SchoolClass::with(['schoolYear', 'serie'])
            ->withCount('students')
            ->where('school_year_id', $this->school_year_id)
            ->orderBy('level')
            ->orderBy('section')
            ->paginate(15);

        $schoolYears = SchoolYear::orderBy('start_date', 'desc')->get();
        $series = Serie::orderBy('name')->get();

        return view('livewire.admin.classes', [
            'classes' => $classes,
            'schoolYears' => $schoolYears,
            'series' => $series,
        ]);
    }
}

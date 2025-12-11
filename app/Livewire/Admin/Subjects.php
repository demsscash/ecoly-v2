<?php

namespace App\Livewire\Admin;

use App\Models\Subject;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.app')]
#[Title('Matières - Ecoly')]
class Subjects extends Component
{
    public ?int $editingId = null;
    
    public string $name_fr = '';
    public string $name_ar = '';
    public string $code = '';
    public string $coefficient = '1';
    
    public bool $showModal = false;

    protected function rules(): array
    {
        $uniqueRule = $this->editingId 
            ? "unique:subjects,code,{$this->editingId}"
            : 'unique:subjects,code';

        return [
            'name_fr' => 'required|string|max:100',
            'name_ar' => 'required|string|max:100',
            'code' => ['required', 'string', 'max:10', 'alpha_num', $uniqueRule],
            'coefficient' => 'required|numeric|min:0.5|max:10',
        ];
    }

    public function create(): void
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function edit(int $id): void
    {
        $subject = Subject::findOrFail($id);
        
        $this->editingId = $subject->id;
        $this->name_fr = $subject->name_fr;
        $this->name_ar = $subject->name_ar;
        $this->code = $subject->code;
        $this->coefficient = (string) $subject->coefficient;
        
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'name_fr' => $this->name_fr,
            'name_ar' => $this->name_ar,
            'code' => strtoupper($this->code),
            'coefficient' => $this->coefficient,
        ];

        if ($this->editingId) {
            $subject = Subject::findOrFail($this->editingId);
            $subject->update($data);
            $this->dispatch('toast', message: __('Subject updated successfully.'), type: 'success');
        } else {
            Subject::create($data);
            $this->dispatch('toast', message: __('Subject created successfully.'), type: 'success');
        }

        $this->showModal = false;
        $this->resetForm();
    }

    public function toggleActive(int $id): void
    {
        $subject = Subject::findOrFail($id);
        $subject->update(['is_active' => !$subject->is_active]);
        
        $message = $subject->is_active 
            ? __('Subject activated successfully.') 
            : __('Subject deactivated successfully.');
            
        $this->dispatch('toast', message: $message, type: 'success');
    }

    public function delete(int $id): void
    {
        $subject = Subject::findOrFail($id);
        
        // Check if assigned to classes
        if ($subject->classes()->exists()) {
            $this->dispatch('toast', message: __('Cannot delete subject assigned to classes.'), type: 'error');
            return;
        }
        
        $subject->delete();
        $this->dispatch('toast', message: __('Subject deleted successfully.'), type: 'success');
    }

    private function resetForm(): void
    {
        $this->editingId = null;
        $this->name_fr = '';
        $this->name_ar = '';
        $this->code = '';
        $this->coefficient = '1';
    }

    public function render()
    {
        $subjects = Subject::orderBy('name_fr')->get();

        return view('livewire.admin.subjects', [
            'subjects' => $subjects,
        ]);
    }
}

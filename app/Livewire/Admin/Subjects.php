<?php

namespace App\Livewire\Admin;

use App\Models\Subject;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Matières - Ecoly')]
class Subjects extends Component
{
    use WithPagination;

    public bool $showModal = false;
    public ?int $editingId = null;
    public string $search = '';

    public string $code = '';
    public string $name_fr = '';
    public string $name_ar = '';
    public bool $is_active = true;

    protected function rules(): array
    {
        return [
            'code' => 'required|string|max:10|unique:subjects,code,' . $this->editingId,
            'name_fr' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ];
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function create(): void
    {
        $this->reset(['editingId', 'code', 'name_fr', 'name_ar', 'is_active']);
        $this->is_active = true;
        $this->showModal = true;
    }

    public function edit(Subject $subject): void
    {
        $this->editingId = $subject->id;
        $this->code = $subject->code;
        $this->name_fr = $subject->name_fr;
        $this->name_ar = $subject->name_ar ?? '';
        $this->is_active = $subject->is_active;
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate();

        Subject::updateOrCreate(
            ['id' => $this->editingId],
            [
                'code' => strtoupper($this->code),
                'name_fr' => $this->name_fr,
                'name_ar' => $this->name_ar ?: null,
                'is_active' => $this->is_active,
            ]
        );

        $this->showModal = false;
        $this->dispatch('toast', message: $this->editingId ? __('Subject updated successfully.') : __('Subject created successfully.'), type: 'success');
    }

    public function delete(Subject $subject): void
    {
        if ($subject->grades()->exists()) {
            $this->dispatch('toast', message: __('Cannot delete subject with existing grades.'), type: 'error');
            return;
        }

        $subject->delete();
        $this->dispatch('toast', message: __('Subject deleted successfully.'), type: 'success');
    }

    public function render()
    {
        $subjects = Subject::query()
            ->when($this->search, function ($q) {
                $q->where('name_fr', 'ilike', '%' . $this->search . '%')
                    ->orWhere('name_ar', 'ilike', '%' . $this->search . '%')
                    ->orWhere('code', 'ilike', '%' . $this->search . '%');
            })
            ->orderBy('name_fr')
            ->paginate(15);

        return view('livewire.admin.subjects', [
            'subjects' => $subjects,
        ]);
    }
}

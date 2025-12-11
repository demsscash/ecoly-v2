<?php

namespace App\Livewire\Admin;

use App\Models\Student;
use App\Models\SchoolClass;
use App\Models\SchoolYear;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Storage;

#[Layout('layouts.app')]
#[Title('Élèves - Ecoly')]
class Students extends Component
{
    use WithPagination, WithFileUploads;

    public ?int $selectedYearId = null;
    public ?int $selectedClassId = null;
    public string $search = '';
    public string $statusFilter = '';
    
    public ?int $editingId = null;
    public bool $showModal = false;

    // Form fields
    public string $nni = '';
    public string $first_name = '';
    public string $last_name = '';
    public string $first_name_ar = '';
    public string $last_name_ar = '';
    public string $birth_date = '';
    public string $birth_place = '';
    public string $birth_place_ar = '';
    public string $gender = 'male';
    public string $nationality = 'Mauritanienne';
    public string $guardian_name = '';
    public string $guardian_name_ar = '';
    public string $guardian_phone = '';
    public string $guardian_phone_2 = '';
    public string $guardian_email = '';
    public string $guardian_profession = '';
    public string $address = '';
    public string $address_ar = '';
    public ?int $class_id = null;
    public string $enrollment_date = '';
    public string $previous_school = '';
    public string $notes = '';
    public $photo = null;
    public ?string $existingPhoto = null;

    public function mount(): void
    {
        $activeYear = SchoolYear::active();
        $this->selectedYearId = $activeYear?->id ?? SchoolYear::latest()->first()?->id;
        $this->enrollment_date = date('Y-m-d');
    }

    public function updatedSelectedYearId(): void
    {
        $this->selectedClassId = null;
        $this->resetPage();
    }

    public function updatedSelectedClassId(): void
    {
        $this->resetPage();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    public function create(): void
    {
        $this->reset([
            'editingId', 'nni', 'first_name', 'last_name', 'first_name_ar', 'last_name_ar',
            'birth_date', 'birth_place', 'birth_place_ar', 'gender', 'nationality',
            'guardian_name', 'guardian_name_ar', 'guardian_phone', 'guardian_phone_2',
            'guardian_email', 'guardian_profession', 'address', 'address_ar',
            'class_id', 'previous_school', 'notes', 'photo', 'existingPhoto'
        ]);
        $this->enrollment_date = date('Y-m-d');
        $this->gender = 'male';
        $this->nationality = 'Mauritanienne';
        $this->showModal = true;
    }

    public function edit(int $id): void
    {
        $student = Student::findOrFail($id);
        
        $this->editingId = $student->id;
        $this->nni = $student->nni ?? '';
        $this->first_name = $student->first_name;
        $this->last_name = $student->last_name;
        $this->first_name_ar = $student->first_name_ar ?? '';
        $this->last_name_ar = $student->last_name_ar ?? '';
        $this->birth_date = $student->birth_date->format('Y-m-d');
        $this->birth_place = $student->birth_place ?? '';
        $this->birth_place_ar = $student->birth_place_ar ?? '';
        $this->gender = $student->gender;
        $this->nationality = $student->nationality;
        $this->guardian_name = $student->guardian_name;
        $this->guardian_name_ar = $student->guardian_name_ar ?? '';
        $this->guardian_phone = $student->guardian_phone;
        $this->guardian_phone_2 = $student->guardian_phone_2 ?? '';
        $this->guardian_email = $student->guardian_email ?? '';
        $this->guardian_profession = $student->guardian_profession ?? '';
        $this->address = $student->address ?? '';
        $this->address_ar = $student->address_ar ?? '';
        $this->class_id = $student->class_id;
        $this->enrollment_date = $student->enrollment_date->format('Y-m-d');
        $this->previous_school = $student->previous_school ?? '';
        $this->notes = $student->notes ?? '';
        $this->existingPhoto = $student->photo_path;
        $this->photo = null;
        
        $this->showModal = true;
    }

    public function save(): void
    {
        $rules = [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'birth_date' => 'required|date',
            'gender' => 'required|in:male,female',
            'guardian_name' => 'required|string|max:255',
            'guardian_phone' => 'required|string|max:20',
            'enrollment_date' => 'required|date',
            'photo' => 'nullable|image|max:2048',
            'nni' => 'nullable|string|size:10|unique:students,nni' . ($this->editingId ? ',' . $this->editingId : ''),
        ];

        // Validate NNI format
        if ($this->nni && !Student::isValidNni($this->nni)) {
            $this->addError('nni', __('NNI must be exactly 10 digits.'));
            return;
        }

        $this->validate($rules);

        $data = [
            'nni' => $this->nni ?: null,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'first_name_ar' => $this->first_name_ar ?: null,
            'last_name_ar' => $this->last_name_ar ?: null,
            'birth_date' => $this->birth_date,
            'birth_place' => $this->birth_place ?: null,
            'birth_place_ar' => $this->birth_place_ar ?: null,
            'gender' => $this->gender,
            'nationality' => $this->nationality,
            'guardian_name' => $this->guardian_name,
            'guardian_name_ar' => $this->guardian_name_ar ?: null,
            'guardian_phone' => $this->guardian_phone,
            'guardian_phone_2' => $this->guardian_phone_2 ?: null,
            'guardian_email' => $this->guardian_email ?: null,
            'guardian_profession' => $this->guardian_profession ?: null,
            'address' => $this->address ?: null,
            'address_ar' => $this->address_ar ?: null,
            'class_id' => $this->class_id,
            'enrollment_date' => $this->enrollment_date,
            'previous_school' => $this->previous_school ?: null,
            'notes' => $this->notes ?: null,
        ];

        // Handle photo upload
        if ($this->photo) {
            if ($this->editingId) {
                $student = Student::find($this->editingId);
                if ($student->photo_path) {
                    Storage::disk('public')->delete($student->photo_path);
                }
            }
            $data['photo_path'] = $this->photo->store('students', 'public');
        }

        if ($this->editingId) {
            $student = Student::findOrFail($this->editingId);
            $student->update($data);
            $this->dispatch('toast', message: __('Student updated successfully.'), type: 'success');
        } else {
            $data['school_year_id'] = $this->selectedYearId;
            $data['matricule'] = Student::generateMatricule($this->selectedYearId);
            Student::create($data);
            $this->dispatch('toast', message: __('Student created successfully.'), type: 'success');
        }

        $this->showModal = false;
        $this->reset(['editingId', 'photo', 'existingPhoto']);
    }

    public function updateStatus(int $id, string $status): void
    {
        $student = Student::findOrFail($id);
        $student->update(['status' => $status]);
        $this->dispatch('toast', message: __('Status updated successfully.'), type: 'success');
    }

    public function delete(int $id): void
    {
        $student = Student::findOrFail($id);
        
        // Soft delete - photo kept for potential restore
        $student->delete();
        
        $this->dispatch('toast', message: __('Student deleted successfully.'), type: 'success');
    }

    public function removePhoto(): void
    {
        if ($this->editingId) {
            $student = Student::find($this->editingId);
            if ($student && $student->photo_path) {
                Storage::disk('public')->delete($student->photo_path);
                $student->update(['photo_path' => null]);
                $this->existingPhoto = null;
                $this->dispatch('toast', message: __('Photo removed.'), type: 'success');
            }
        }
        $this->photo = null;
    }

    public function render()
    {
        $years = SchoolYear::orderByDesc('start_date')->get();
        
        $classes = $this->selectedYearId 
            ? SchoolClass::forYear($this->selectedYearId)
                ->active()
                ->orderBy('level')
                ->orderBy('section')
                ->get()
            : collect();

        $students = Student::query()
            ->with(['class', 'schoolYear'])
            ->when($this->selectedYearId, fn($q) => $q->forYear($this->selectedYearId))
            ->when($this->selectedClassId, fn($q) => $q->inClass($this->selectedClassId))
            ->when($this->statusFilter, fn($q) => $q->where('status', $this->statusFilter))
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('first_name', 'ilike', "%{$this->search}%")
                      ->orWhere('last_name', 'ilike', "%{$this->search}%")
                      ->orWhere('matricule', 'ilike', "%{$this->search}%")
                      ->orWhere('nni', 'ilike', "%{$this->search}%")
                      ->orWhere('guardian_phone', 'ilike', "%{$this->search}%");
                });
            })
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->paginate(20);

        return view('livewire.admin.students', [
            'years' => $years,
            'classes' => $classes,
            'students' => $students,
            'statuses' => [
                'active' => __('Active'),
                'inactive' => __('Inactive'),
                'transferred' => __('Transferred'),
                'graduated' => __('Graduated'),
            ],
        ]);
    }
}

<?php

namespace App\Livewire\Admin;

use App\Models\Student;
use App\Models\SchoolClass;
use App\Models\SchoolYear;
use App\Models\SchoolSetting;
use App\Exports\StudentsExport;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

#[Layout('layouts.app')]
#[Title('Élèves - Ecoly')]
class Students extends Component
{
    use WithPagination, WithFileUploads;

    public string $search = '';
    public string $filterClass = '';
    public string $filterStatus = '';

    public ?int $editingId = null;
    public string $first_name = '';
    public string $last_name = '';
    public string $first_name_ar = '';
    public string $last_name_ar = '';
    public string $birth_date = '';
    public string $birth_place = '';
    public string $birth_place_ar = '';
    public string $gender = 'male';
    public string $nationality = 'Mauritanienne';
    public ?string $nni = null;
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
    public string $status = 'active';
    public string $notes = '';

    public $photo = null;
    public ?string $existingPhoto = null;
    public bool $showModal = false;

    protected function rules(): array
    {
        $nniRule = $this->editingId 
            ? 'nullable|string|size:10|unique:students,nni,' . $this->editingId
            : 'nullable|string|size:10|unique:students,nni';

        return [
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'first_name_ar' => 'nullable|string|max:100',
            'last_name_ar' => 'nullable|string|max:100',
            'birth_date' => 'required|date',
            'birth_place' => 'required|string|max:100',
            'birth_place_ar' => 'nullable|string|max:100',
            'gender' => 'required|in:male,female',
            'nationality' => 'required|string|max:50',
            'nni' => $nniRule,
            'guardian_name' => 'required|string|max:100',
            'guardian_name_ar' => 'nullable|string|max:100',
            'guardian_phone' => 'required|string|max:20',
            'guardian_phone_2' => 'nullable|string|max:20',
            'guardian_email' => 'nullable|email|max:100',
            'guardian_profession' => 'nullable|string|max:100',
            'address' => 'nullable|string|max:255',
            'address_ar' => 'nullable|string|max:255',
            'class_id' => 'nullable|exists:classes,id',
            'enrollment_date' => 'required|date',
            'previous_school' => 'nullable|string|max:100',
            'status' => 'required|in:active,inactive,transferred,graduated',
            'notes' => 'nullable|string',
            'photo' => 'nullable|image|max:2048',
        ];
    }

    public function updatingSearch(): void { $this->resetPage(); }
    public function updatingFilterClass(): void { $this->resetPage(); }
    public function updatingFilterStatus(): void { $this->resetPage(); }

    public function create(): void
    {
        $this->reset(['editingId', 'first_name', 'last_name', 'first_name_ar', 'last_name_ar', 'birth_date', 'birth_place', 'birth_place_ar', 'gender', 'nationality', 'nni', 'guardian_name', 'guardian_name_ar', 'guardian_phone', 'guardian_phone_2', 'guardian_email', 'guardian_profession', 'address', 'address_ar', 'class_id', 'enrollment_date', 'previous_school', 'status', 'notes', 'photo', 'existingPhoto']);
        $this->enrollment_date = now()->format('Y-m-d');
        $this->status = 'active';
        $this->gender = 'male';
        $this->nationality = 'Mauritanienne';
        $this->showModal = true;
    }

    public function edit(int $id): void
    {
        $student = Student::findOrFail($id);
        $this->editingId = $student->id;
        $this->first_name = $student->first_name;
        $this->last_name = $student->last_name;
        $this->first_name_ar = $student->first_name_ar ?? '';
        $this->last_name_ar = $student->last_name_ar ?? '';
        $this->birth_date = $student->birth_date->format('Y-m-d');
        $this->birth_place = $student->birth_place;
        $this->birth_place_ar = $student->birth_place_ar ?? '';
        $this->gender = $student->gender;
        $this->nationality = $student->nationality;
        $this->nni = $student->nni;
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
        $this->status = $student->status;
        $this->notes = $student->notes ?? '';
        $this->existingPhoto = $student->photo_path;
        $this->photo = null;
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate();

        if ($this->nni && !Student::isValidNni($this->nni)) {
            $this->addError('nni', __('NNI must be exactly 10 digits.'));
            return;
        }

        $schoolYear = SchoolYear::where('is_active', true)->first();
        if (!$schoolYear) {
            $this->dispatch('toast', message: __('No active school year.'), type: 'error');
            return;
        }

        $data = [
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'first_name_ar' => $this->first_name_ar ?: null,
            'last_name_ar' => $this->last_name_ar ?: null,
            'birth_date' => $this->birth_date,
            'birth_place' => $this->birth_place,
            'birth_place_ar' => $this->birth_place_ar ?: null,
            'gender' => $this->gender,
            'nationality' => $this->nationality,
            'nni' => $this->nni ?: null,
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
            'status' => $this->status,
            'notes' => $this->notes ?: null,
        ];

        if ($this->editingId) {
            $student = Student::findOrFail($this->editingId);
            if ($this->photo) {
                if ($student->photo_path) Storage::disk('public')->delete($student->photo_path);
                $data['photo_path'] = $this->photo->store('students', 'public');
            }
            $student->update($data);
            $message = __('Student updated successfully.');
        } else {
            $data['school_year_id'] = $schoolYear->id;
            $data['matricule'] = Student::generateMatricule($schoolYear->id);
            if ($this->photo) $data['photo_path'] = $this->photo->store('students', 'public');
            Student::create($data);
            $message = __('Student created successfully.');
        }

        $this->showModal = false;
        $this->dispatch('toast', message: $message, type: 'success');
    }

    public function removePhoto(): void
    {
        if ($this->editingId && $this->existingPhoto) {
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

    public function delete(int $id): void
    {
        Student::findOrFail($id)->delete();
        $this->dispatch('toast', message: __('Student deleted successfully.'), type: 'success');
    }

    public function exportExcel()
    {
        $schoolYear = SchoolYear::where('is_active', true)->first();
        if (!$schoolYear) {
            $this->dispatch('toast', message: __('No active school year.'), type: 'error');
            return;
        }

        return Excel::download(
            new StudentsExport($schoolYear->id, $this->filterClass ?: null, $this->filterStatus ?: null),
            'eleves_' . now()->format('Y-m-d_His') . '.xlsx'
        );
    }

    public function exportPdf()
    {
        $schoolYear = SchoolYear::where('is_active', true)->first();
        if (!$schoolYear) {
            $this->dispatch('toast', message: __('No active school year.'), type: 'error');
            return;
        }

        $query = Student::with(['class'])->where('school_year_id', $schoolYear->id)->orderBy('last_name')->orderBy('first_name');
        if ($this->filterClass) $query->where('class_id', $this->filterClass);
        if ($this->filterStatus) $query->where('status', $this->filterStatus);

        $pdf = Pdf::loadView('pdf.students-list', [
            'students' => $query->get(),
            'school' => SchoolSetting::instance(),
            'schoolYear' => $schoolYear,
            'class' => $this->filterClass ? SchoolClass::find($this->filterClass) : null,
            'date' => now(),
        ]);
        $pdf->setPaper('A4', 'landscape');

        return response()->streamDownload(fn() => print($pdf->output()), 'liste_eleves_' . now()->format('Y-m-d_His') . '.pdf');
    }

    public function render()
    {
        $schoolYear = SchoolYear::where('is_active', true)->first();

        $query = Student::with(['class', 'schoolYear'])
            ->when($schoolYear, fn($q) => $q->where('school_year_id', $schoolYear->id))
            ->when($this->search, fn($q) => $q->where(fn($query) => $query->where('first_name', 'ilike', "%{$this->search}%")->orWhere('last_name', 'ilike', "%{$this->search}%")->orWhere('matricule', 'ilike', "%{$this->search}%")->orWhere('nni', 'ilike', "%{$this->search}%")->orWhere('guardian_name', 'ilike', "%{$this->search}%")->orWhere('guardian_phone', 'ilike', "%{$this->search}%")))
            ->when($this->filterClass, fn($q) => $q->where('class_id', $this->filterClass))
            ->when($this->filterStatus, fn($q) => $q->where('status', $this->filterStatus))
            ->orderBy('last_name')->orderBy('first_name');

        $classes = SchoolClass::when($schoolYear, fn($q) => $q->where('school_year_id', $schoolYear->id))->where('is_active', true)->orderBy('level')->orderBy('name')->get();

        return view('livewire.admin.students', [
            'students' => $query->paginate(15),
            'classes' => $classes,
        ]);
    }
}

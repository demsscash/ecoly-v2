<?php

namespace App\Livewire;

use App\Models\Attendance;
use App\Models\SchoolClass;
use App\Models\SchoolYear;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\WithPagination;
use Livewire\WithFileUploads;

#[Layout('layouts.app')]
#[Title('Justifications - Ecoly')]
class AttendanceJustifications extends Component
{
    use WithPagination, WithFileUploads;

    public string $search = '';
    public string $filterClass = '';
    public string $filterStatus = '';
    
    public bool $showJustifyModal = false;
    public ?int $selectedAttendanceId = null;
    public string $justificationNote = '';
    public $justificationFile;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedFilterClass(): void
    {
        $this->resetPage();
    }

    public function updatedFilterStatus(): void
    {
        $this->resetPage();
    }

    public function openJustifyModal(int $attendanceId): void
    {
        $attendance = Attendance::find($attendanceId);
        
        if (!$attendance) return;

        $this->selectedAttendanceId = $attendanceId;
        $this->justificationNote = $attendance->justification_note ?? '';
        $this->justificationFile = null;
        $this->showJustifyModal = true;
    }

    public function saveJustification(): void
    {
        $this->validate([
            'justificationNote' => 'required|string|max:1000',
            'justificationFile' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $attendance = Attendance::find($this->selectedAttendanceId);
        
        if (!$attendance) {
            $this->dispatch('toast', message: __('Attendance record not found.'), type: 'error');
            return;
        }

        // Upload file if provided
        $filePath = null;
        if ($this->justificationFile) {
            $filePath = $this->justificationFile->store('justifications', 'public');
        }

        // Update attendance
        $attendance->update([
            'status' => 'justified',
            'justification_note' => $this->justificationNote,
            'justification_file' => $filePath ?? $attendance->justification_file,
        ]);

        $this->showJustifyModal = false;
        $this->reset(['selectedAttendanceId', 'justificationNote', 'justificationFile']);
        
        $this->dispatch('toast', message: __('Justification saved successfully.'), type: 'success');
    }

    public function render()
    {
        $schoolYear = SchoolYear::where('is_active', true)->first();
        
        $query = Attendance::with(['student.class', 'markedBy'])
            ->whereHas('student', function($q) use ($schoolYear) {
                $q->where('school_year_id', $schoolYear?->id)
                  ->where('status', 'active');
            })
            ->whereIn('status', ['absent', 'late', 'left_early']);

        // Search
        if ($this->search) {
            $query->whereHas('student', function($q) {
                $q->where('first_name', 'like', '%' . $this->search . '%')
                  ->orWhere('last_name', 'like', '%' . $this->search . '%')
                  ->orWhere('matricule', 'like', '%' . $this->search . '%');
            });
        }

        // Filter by class
        if ($this->filterClass) {
            $query->whereHas('student', fn($q) => $q->where('class_id', $this->filterClass));
        }

        // Filter by status
        if ($this->filterStatus) {
            $query->where('status', $this->filterStatus);
        }

        $attendances = $query->orderBy('date', 'desc')->paginate(20);
        $classes = SchoolClass::orderBy('level')->orderBy('section')->get();

        return view('livewire.attendance-justifications', [
            'attendances' => $attendances,
            'classes' => $classes,
        ]);
    }
}

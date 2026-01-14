<?php

namespace App\Livewire\Admin;

use App\Models\Grade;
use App\Models\SchoolClass;
use App\Models\SchoolYear;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Trimester;
use App\Services\GradeCalculationService;
use App\Exports\ClassGradesExport;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Maatwebsite\Excel\Facades\Excel;

#[Layout('layouts.app')]
#[Title('Notes par classe - Ecoly')]
class ClassGrades extends Component
{
    public ?int $selectedClassId = null;
    public ?int $selectedTrimesterId = null;
    public bool $showHistory = false;
    public ?int $historyGradeId = null;

    protected GradeCalculationService $gradeCalc;

    public function boot(GradeCalculationService $gradeCalc): void
    {
        $this->gradeCalc = $gradeCalc;
    }

    public function mount(): void
    {
        $schoolYear = SchoolYear::where('is_active', true)->first();

        if ($schoolYear) {
            $firstClass = SchoolClass::where('school_year_id', $schoolYear->id)
                ->where('is_active', true)
                ->orderBy('level')
                ->first();
            $this->selectedClassId = $firstClass?->id;

            $trimester = Trimester::where('school_year_id', $schoolYear->id)
                ->where('status', 'open')
                ->first()
                ?? Trimester::where('school_year_id', $schoolYear->id)->first();
            $this->selectedTrimesterId = $trimester?->id;
        }
    }

    /**
     * Get class data with rankings for a class/trimester
     */
    public function getClassData(): array
    {
        if (!$this->selectedClassId || !$this->selectedTrimesterId) {
            return [
                'rankings' => [],
                'subjects' => collect(),
            ];
        }

        $class = SchoolClass::find($this->selectedClassId);
        $trimester = Trimester::find($this->selectedTrimesterId);

        if (!$class || !$trimester) {
            return [
                'rankings' => [],
                'subjects' => collect(),
            ];
        }

        return $this->gradeCalc->calculateClassRankings($class, $trimester);
    }

    /**
     * Validate all grades for a student in this trimester
     */
    public function validateStudentGrades(int $studentId): void
    {
        if (!auth()->user()->isAdmin()) {
            $this->dispatch('toast', message: __('Only admins can validate grades.'), type: 'error');
            return;
        }

        Grade::where('student_id', $studentId)
            ->where('trimester_id', $this->selectedTrimesterId)
            ->where('is_validated', false)
            ->update([
                'is_validated' => true,
                'validated_by' => auth()->id(),
                'validated_at' => now(),
            ]);

        $this->dispatch('toast', message: __('Grades validated successfully.'), type: 'success');
    }

    /**
     * Validate all grades for the class
     */
    public function validateAllGrades(): void
    {
        if (!auth()->user()->isAdmin()) {
            $this->dispatch('toast', message: __('Only admins can validate grades.'), type: 'error');
            return;
        }

        Grade::where('class_id', $this->selectedClassId)
            ->where('trimester_id', $this->selectedTrimesterId)
            ->where('is_validated', false)
            ->update([
                'is_validated' => true,
                'validated_by' => auth()->id(),
                'validated_at' => now(),
            ]);

        $this->dispatch('toast', message: __('All grades validated successfully.'), type: 'success');
    }

    /**
     * Show grade history modal
     */
    public function showGradeHistory(int $gradeId): void
    {
        $this->historyGradeId = $gradeId;
        $this->showHistory = true;
    }

    /**
     * Export grades to Excel
     */
    public function exportExcel()
    {
        if (!$this->selectedClassId || !$this->selectedTrimesterId) {
            $this->dispatch('toast', message: __('Please select a class and trimester.'), type: 'error');
            return;
        }

        $class = SchoolClass::find($this->selectedClassId);
        $trimester = Trimester::find($this->selectedTrimesterId);

        $filename = 'notes_' . str_replace(' ', '_', $class->name) . '_' . $trimester->name_fr . '_' . now()->format('Y-m-d') . '.xlsx';

        return Excel::download(
            new ClassGradesExport($this->selectedClassId, $this->selectedTrimesterId, $this->gradeCalc),
            $filename
        );
    }

    public function render()
    {
        $schoolYear = SchoolYear::where('is_active', true)->first();

        $classes = SchoolClass::when($schoolYear, fn($q) => $q->where('school_year_id', $schoolYear->id))
            ->where('is_active', true)
            ->orderBy('level')
            ->orderBy('name')
            ->get();

        $trimesters = Trimester::when($schoolYear, fn($q) => $q->where('school_year_id', $schoolYear->id))
            ->orderBy('start_date')
            ->get();

        $classData = $this->getClassData();
        $rankings = $classData['rankings'];
        $subjects = $classData['subjects'];

        $statistics = $this->gradeCalc->getClassStatistics($rankings);

        $subjectStats = [];
        foreach ($subjects as $subject) {
            $subjectStats[$subject->id] = $this->gradeCalc->getSubjectStatistics($rankings, $subject->id);
        }

        $selectedClass = $this->selectedClassId ? SchoolClass::find($this->selectedClassId) : null;

        $gradeHistory = null;
        if ($this->historyGradeId) {
            $gradeHistory = Grade::with(['histories.user', 'student', 'subject'])
                ->find($this->historyGradeId);
        }

        return view('livewire.admin.class-grades', [
            'classes' => $classes,
            'trimesters' => $trimesters,
            'subjects' => $subjects,
            'rankings' => $rankings,
            'statistics' => $statistics,
            'subjectStats' => $subjectStats,
            'selectedClass' => $selectedClass,
            'gradeHistory' => $gradeHistory,
        ]);
    }
}

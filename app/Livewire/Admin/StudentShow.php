<?php

namespace App\Livewire\Admin;

use App\Models\Student;
use App\Models\Grade;
use App\Models\Trimester;
use App\Models\SchoolSetting;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Barryvdh\DomPDF\Facade\Pdf;

#[Layout('layouts.app')]
class StudentShow extends Component
{
    public Student $student;
    public ?int $selectedTrimesterId = null;

    public function mount(Student $student): void
    {
        $this->student = $student->load(['class', 'schoolYear', 'grades.subject', 'grades.trimester']);
        
        // Select current or first trimester
        $currentTrimester = Trimester::where('school_year_id', $student->school_year_id)
            ->where('status', 'open')
            ->first();
        
        $this->selectedTrimesterId = $currentTrimester?->id 
            ?? Trimester::where('school_year_id', $student->school_year_id)->first()?->id;
    }

    public function getTitle(): string
    {
        return $this->student->full_name . ' - Ecoly';
    }

    public function downloadAttestation()
    {
        $school = SchoolSetting::instance();
        $student = $this->student;
        
        $pdf = Pdf::loadView('pdf.attestation-inscription', [
            'student' => $student,
            'school' => $school,
            'date' => now(),
        ]);

        $pdf->setPaper('A4', 'portrait');

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'attestation_' . $student->matricule . '.pdf');
    }

    public function render()
    {
        $trimesters = Trimester::where('school_year_id', $this->student->school_year_id)
            ->orderBy('start_date')
            ->get();

        // Get grades for selected trimester
        $grades = collect();
        $trimesterAverage = null;
        
        if ($this->selectedTrimesterId) {
            $grades = Grade::with('subject')
                ->where('student_id', $this->student->id)
                ->where('trimester_id', $this->selectedTrimesterId)
                ->get()
                ->keyBy('subject_id');

            // Calculate trimester average
            if ($grades->isNotEmpty()) {
                $totalWeighted = 0;
                $totalCoef = 0;
                
                foreach ($grades as $grade) {
                    if ($grade->average !== null) {
                        $coef = $grade->subject->coefficient ?? 1;
                        $totalWeighted += $grade->average * $coef;
                        $totalCoef += $coef;
                    }
                }
                
                $trimesterAverage = $totalCoef > 0 ? round($totalWeighted / $totalCoef, 2) : null;
            }
        }

        // Get subjects for the class
        $subjects = $this->student->class?->subjects()
            ->orderBy('name_fr')
            ->get() ?? collect();

        return view('livewire.admin.student-show', [
            'trimesters' => $trimesters,
            'grades' => $grades,
            'subjects' => $subjects,
            'trimesterAverage' => $trimesterAverage,
        ]);
    }
}

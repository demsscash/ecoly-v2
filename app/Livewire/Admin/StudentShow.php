<?php

namespace App\Livewire\Admin;

use App\Models\Student;
use App\Models\Grade;
use App\Models\Trimester;
use App\Models\SchoolSetting;
use App\Models\GradingConfig;
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
        $this->student = $student->load(['class', 'schoolYear']);
        
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

    /**
     * Calculate trimester average for a student
     */
    protected function calculateTrimesterAverage(int $trimesterId): ?float
    {
        $grades = Grade::with('subject')
            ->where('student_id', $this->student->id)
            ->where('trimester_id', $trimesterId)
            ->get();

        if ($grades->isEmpty()) {
            return null;
        }

        $totalWeighted = 0;
        $totalCoef = 0;

        foreach ($grades as $grade) {
            if ($grade->average !== null) {
                $coef = $grade->subject->classes()
                    ->where('classes.id', $this->student->class_id)
                    ->first()?->pivot?->coefficient ?? $grade->subject->coefficient;
                $totalWeighted += $grade->average * $coef;
                $totalCoef += $coef;
            }
        }

        return $totalCoef > 0 ? round($totalWeighted / $totalCoef, 2) : null;
    }

    /**
     * Calculate annual average (average of all trimesters)
     */
    public function getAnnualAverage(): ?float
    {
        $trimesters = Trimester::where('school_year_id', $this->student->school_year_id)
            ->orderBy('start_date')
            ->get();

        $trimesterAverages = [];

        foreach ($trimesters as $trimester) {
            $avg = $this->calculateTrimesterAverage($trimester->id);
            if ($avg !== null) {
                $trimesterAverages[] = $avg;
            }
        }

        if (empty($trimesterAverages)) {
            return null;
        }

        return round(array_sum($trimesterAverages) / count($trimesterAverages), 2);
    }

    /**
     * Get all trimester averages for display
     */
    public function getTrimesterAverages(): array
    {
        $trimesters = Trimester::where('school_year_id', $this->student->school_year_id)
            ->orderBy('start_date')
            ->get();

        $averages = [];

        foreach ($trimesters as $trimester) {
            $averages[$trimester->id] = [
                'name' => $trimester->name_fr,
                'average' => $this->calculateTrimesterAverage($trimester->id),
                'rank' => $this->getStudentRankForTrimester($trimester->id),
            ];
        }

        return $averages;
    }

    /**
     * Calculate student rank in class for a specific trimester
     */
    protected function getStudentRankForTrimester(int $trimesterId): array
    {
        if (!$this->student->class_id) {
            return ['rank' => null, 'total' => 0];
        }

        $classmates = Student::where('class_id', $this->student->class_id)
            ->where('status', 'active')
            ->get();

        $averages = [];

        foreach ($classmates as $classmate) {
            $grades = Grade::with('subject')
                ->where('student_id', $classmate->id)
                ->where('trimester_id', $trimesterId)
                ->get();

            $totalWeighted = 0;
            $totalCoef = 0;

            foreach ($grades as $grade) {
                if ($grade->average !== null) {
                    $coef = $grade->subject->classes()
                        ->where('classes.id', $this->student->class_id)
                        ->first()?->pivot?->coefficient ?? $grade->subject->coefficient;
                    $totalWeighted += $grade->average * $coef;
                    $totalCoef += $coef;
                }
            }

            $averages[$classmate->id] = $totalCoef > 0 ? round($totalWeighted / $totalCoef, 2) : null;
        }

        $validAverages = array_filter($averages, fn($v) => $v !== null);
        arsort($validAverages);

        $rank = 1;
        $lastAvg = null;
        $studentRank = null;

        foreach ($validAverages as $studentId => $avg) {
            if ($avg !== $lastAvg) {
                $rank = array_search($studentId, array_keys($validAverages)) + 1;
                $lastAvg = $avg;
            }
            if ($studentId === $this->student->id) {
                $studentRank = $rank;
                break;
            }
        }

        return [
            'rank' => $studentRank,
            'total' => count($validAverages),
        ];
    }

    /**
     * Get student annual rank
     */
    public function getAnnualRank(): array
    {
        if (!$this->student->class_id) {
            return ['rank' => null, 'total' => 0];
        }

        $classmates = Student::where('class_id', $this->student->class_id)
            ->where('status', 'active')
            ->get();

        $trimesters = Trimester::where('school_year_id', $this->student->school_year_id)->get();

        $annualAverages = [];

        foreach ($classmates as $classmate) {
            $trimesterAvgs = [];

            foreach ($trimesters as $trimester) {
                $grades = Grade::with('subject')
                    ->where('student_id', $classmate->id)
                    ->where('trimester_id', $trimester->id)
                    ->get();

                $totalWeighted = 0;
                $totalCoef = 0;

                foreach ($grades as $grade) {
                    if ($grade->average !== null) {
                        $coef = $grade->subject->classes()
                            ->where('classes.id', $this->student->class_id)
                            ->first()?->pivot?->coefficient ?? $grade->subject->coefficient;
                        $totalWeighted += $grade->average * $coef;
                        $totalCoef += $coef;
                    }
                }

                if ($totalCoef > 0) {
                    $trimesterAvgs[] = round($totalWeighted / $totalCoef, 2);
                }
            }

            $annualAverages[$classmate->id] = !empty($trimesterAvgs) 
                ? round(array_sum($trimesterAvgs) / count($trimesterAvgs), 2) 
                : null;
        }

        $validAverages = array_filter($annualAverages, fn($v) => $v !== null);
        arsort($validAverages);

        $rank = 1;
        $lastAvg = null;
        $studentRank = null;

        foreach ($validAverages as $studentId => $avg) {
            if ($avg !== $lastAvg) {
                $rank = array_search($studentId, array_keys($validAverages)) + 1;
                $lastAvg = $avg;
            }
            if ($studentId === $this->student->id) {
                $studentRank = $rank;
                break;
            }
        }

        return [
            'rank' => $studentRank,
            'total' => count($validAverages),
        ];
    }

    /**
     * Get mention based on average
     */
    public function getMention(?float $average): ?string
    {
        if ($average === null) return null;

        $config = GradingConfig::instance();

        if ($average >= $config->excellent_threshold) return __('Excellent');
        if ($average >= $config->very_good_threshold) return __('Very Good');
        if ($average >= $config->good_threshold) return __('Good');
        if ($average >= $config->fairly_good_threshold) return __('Fairly Good');
        if ($average >= $config->pass_threshold) return __('Passable');
        
        return __('Insufficient');
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

        $grades = collect();
        $trimesterAverage = null;
        
        if ($this->selectedTrimesterId) {
            $grades = Grade::with('subject')
                ->where('student_id', $this->student->id)
                ->where('trimester_id', $this->selectedTrimesterId)
                ->get()
                ->keyBy('subject_id');

            $trimesterAverage = $this->calculateTrimesterAverage($this->selectedTrimesterId);
        }

        $subjects = $this->student->class?->subjects()
            ->orderBy('name_fr')
            ->get() ?? collect();

        $rankInfo = $this->getStudentRankForTrimester($this->selectedTrimesterId);
        $mention = $this->getMention($trimesterAverage);
        
        $trimesterAverages = $this->getTrimesterAverages();
        $annualAverage = $this->getAnnualAverage();
        $annualRank = $this->getAnnualRank();
        $annualMention = $this->getMention($annualAverage);

        return view('livewire.admin.student-show', [
            'trimesters' => $trimesters,
            'grades' => $grades,
            'subjects' => $subjects,
            'trimesterAverage' => $trimesterAverage,
            'rankInfo' => $rankInfo,
            'mention' => $mention,
            'trimesterAverages' => $trimesterAverages,
            'annualAverage' => $annualAverage,
            'annualRank' => $annualRank,
            'annualMention' => $annualMention,
        ]);
    }
}

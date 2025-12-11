<?php

namespace App\Livewire\Admin;

use App\Models\Grade;
use App\Models\SchoolClass;
use App\Models\SchoolYear;
use App\Models\Student;
use App\Models\Trimester;
use App\Services\BulletinService;
use App\Services\PdfService;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use ZipArchive;

#[Layout('layouts.app')]
#[Title('Bulletins - Ecoly')]
class Bulletins extends Component
{
    public ?int $selectedClassId = null;
    public ?int $selectedTrimesterId = null;
    public array $selectedStudents = [];
    public bool $selectAll = false;

    public function mount(): void
    {
        $schoolYear = SchoolYear::where('is_active', true)->first();
        
        if ($schoolYear) {
            $trimester = Trimester::where('school_year_id', $schoolYear->id)
                ->where('status', 'open')
                ->first() 
                ?? Trimester::where('school_year_id', $schoolYear->id)->first();
            $this->selectedTrimesterId = $trimester?->id;
        }
    }

    public function updatedSelectAll(): void
    {
        if ($this->selectAll && $this->selectedClassId) {
            $studentIds = Student::where('class_id', $this->selectedClassId)
                ->where('status', 'active')
                ->pluck('id')
                ->toArray();
            
            $this->selectedStudents = [];
            foreach ($studentIds as $id) {
                if ($this->hasGrades($id)) {
                    $this->selectedStudents[] = $id;
                }
            }
        } else {
            $this->selectedStudents = [];
        }
    }

    public function updatedSelectedClassId(): void
    {
        $this->selectedStudents = [];
        $this->selectAll = false;
    }

    public function generateBulletin(int $studentId)
    {
        $student = Student::with(['class', 'schoolYear'])->find($studentId);
        $trimester = Trimester::find($this->selectedTrimesterId);

        if (!$student || !$trimester) {
            $this->dispatch('toast', message: __('Student or trimester not found.'), type: 'error');
            return;
        }

        $bulletinService = new BulletinService();
        $data = $bulletinService->getBulletinData($student, $trimester);

        $pdfService = new PdfService();
        $pdfContent = $pdfService->generateBulletin($data);

        $filename = 'bulletin_' . $student->matricule . '_' . str_replace(' ', '_', $trimester->name_fr) . '.pdf';

        return response()->streamDownload(function () use ($pdfContent) {
            echo $pdfContent;
        }, $filename, ['Content-Type' => 'application/pdf']);
    }

    public function generateSelectedBulletins()
    {
        if (empty($this->selectedStudents)) {
            $this->dispatch('toast', message: __('Please select at least one student.'), type: 'error');
            return;
        }

        $trimester = Trimester::find($this->selectedTrimesterId);
        if (!$trimester) {
            $this->dispatch('toast', message: __('Trimester not found.'), type: 'error');
            return;
        }

        if (count($this->selectedStudents) === 1) {
            return $this->generateBulletin($this->selectedStudents[0]);
        }

        $bulletinService = new BulletinService();
        $pdfService = new PdfService();
        $tempDir = storage_path('app/temp');
        
        if (!file_exists($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        $pdfFiles = [];

        foreach ($this->selectedStudents as $studentId) {
            $student = Student::with(['class', 'schoolYear'])->find($studentId);
            if (!$student) continue;

            $data = $bulletinService->getBulletinData($student, $trimester);
            $pdfContent = $pdfService->generateBulletin($data);

            $pdfPath = $tempDir . '/bulletin_' . $student->matricule . '.pdf';
            file_put_contents($pdfPath, $pdfContent);
            $pdfFiles[] = $pdfPath;
        }

        if (empty($pdfFiles)) {
            $this->dispatch('toast', message: __('No bulletins generated.'), type: 'error');
            return;
        }

        $class = SchoolClass::find($this->selectedClassId);
        $zipName = 'bulletins_' . str_replace(' ', '_', $class->name) . '_' . str_replace(' ', '_', $trimester->name_fr) . '.zip';
        $zipPath = $tempDir . '/' . $zipName;

        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
            foreach ($pdfFiles as $file) {
                $zip->addFile($file, basename($file));
            }
            $zip->close();

            foreach ($pdfFiles as $file) {
                unlink($file);
            }

            return response()->download($zipPath, $zipName)->deleteFileAfterSend(true);
        }

        $this->dispatch('toast', message: __('Error creating ZIP file.'), type: 'error');
    }

    public function hasGrades(int $studentId): bool
    {
        return Grade::where('student_id', $studentId)
            ->where('trimester_id', $this->selectedTrimesterId)
            ->exists();
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

        $students = collect();
        if ($this->selectedClassId) {
            $students = Student::where('class_id', $this->selectedClassId)
                ->where('status', 'active')
                ->orderBy('last_name')
                ->orderBy('first_name')
                ->get();
        }

        return view('livewire.admin.bulletins', [
            'classes' => $classes,
            'trimesters' => $trimesters,
            'students' => $students,
        ]);
    }
}

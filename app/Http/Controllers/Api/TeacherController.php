<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\Student;
use App\Models\Grade;
use App\Models\Trimester;
use App\Services\GradeCalculationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TeacherController extends ApiController
{
    protected GradeCalculationService $gradeCalc;

    public function __construct(GradeCalculationService $gradeCalc)
    {
        $this->gradeCalc = $gradeCalc;
    }

    /**
     * Get teacher's classes.
     */
    public function classes(Request $request): JsonResponse
    {
        $teacherId = $request->user()->id;
        $yearId = $request->query('school_year_id');

        // Get classes where teacher teaches at least one subject
        $classes = SchoolClass::whereHas('subjects', function ($query) use ($teacherId) {
            $query->where('teacher_id', $teacherId);
        })
            ->when($yearId, fn($q) => $q->where('school_year_id', $yearId))
            ->with(['schoolYear', 'serie'])
            ->where('is_active', true)
            ->with(['subjects' => function ($q) use ($teacherId) {
                $q->where('teacher_id', $teacherId);
            }])
            ->orderBy('level')
            ->orderBy('section')
            ->get();

        return $this->success($classes);
    }

    /**
     * Get students for a specific class and subject.
     */
    public function classStudents(Request $request, string $classId): JsonResponse
    {
        $teacherId = $request->user()->id;

        // Verify teacher teaches this class
        $teaches = Subject::whereHas('classes', function ($query) use ($classId) {
            $query->where('class_id', $classId);
        })->where('teacher_id', $teacherId)->exists();

        if (!$teaches) {
            return $this->error('Vous n\'enseignez pas dans cette classe.', 403);
        }

        $trimesterId = $request->query('trimester_id');
        $subjectId = $request->query('subject_id');

        if (!$trimesterId) {
            return $this->error('Le paramètre trimester_id est requis.', 400);
        }

        if (!$subjectId) {
            return $this->error('Le paramètre subject_id est requis.', 400);
        }

        // Get students
        $students = Student::where('class_id', $classId)
            ->where('status', 'active')
            ->with(['grades' => function ($q) use ($trimesterId, $subjectId) {
                $q->where('trimester_id', $trimesterId)
                    ->where('subject_id', $subjectId);
            }])
            ->get()
            ->map(function ($student) use ($subjectId) {
                $grade = $student->grades->firstWhere('subject_id', $subjectId);

                return [
                    'id' => $student->id,
                    'matricule' => $student->matricule,
                    'first_name' => $student->first_name,
                    'last_name' => $student->last_name,
                    'full_name' => $student->full_name,
                    'gender' => $student->gender,
                    'photo_url' => $student->photo_url,
                    'grade_id' => $grade?->id,
                    'control_grade' => $grade?->control_grade,
                    'exam_grade' => $grade?->exam_grade,
                    'average' => $grade?->average,
                    'appreciation' => $grade?->appreciation,
                    'is_validated' => $grade?->is_validated ?? false,
                ];
            });

        return $this->success($students);
    }

    /**
     * Store or update grades.
     */
    public function storeGrades(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'class_id' => 'required|exists:classes,id',
            'subject_id' => 'required|exists:subjects,id',
            'trimester_id' => 'required|exists:trimesters,id',
            'grades' => 'required|array',
            'grades.*.student_id' => 'required|exists:students,id',
            'grades.*.control_grade' => 'nullable|numeric|min:0|max:40',
            'grades.*.exam_grade' => 'nullable|numeric|min:0|max:40',
            'grades.*.appreciation' => 'nullable|string|max:255',
        ]);

        $teacherId = $request->user()->id;

        // Verify teacher teaches this subject in this class
        $teaches = Subject::where('id', $validated['subject_id'])
            ->where('teacher_id', $teacherId)
            ->whereHas('classes', fn($q) => $q->where('class_id', $validated['class_id']))
            ->exists();

        if (!$teaches) {
            return $this->error('Vous n\'êtes pas autorisé à saisir des notes pour cette matière.', 403);
        }

        $results = [];

        foreach ($validated['grades'] as $gradeData) {
            $student = Student::where('id', $gradeData['student_id'])
                ->where('class_id', $validated['class_id'])
                ->first();

            if (!$student) {
                continue;
            }

            // Calculate average
            $average = null;
            if ($gradeData['control_grade'] !== null || $gradeData['exam_grade'] !== null) {
                $average = $this->gradeCalc->calculateAverage(
                    $gradeData['control_grade'],
                    $gradeData['exam_grade'],
                    20 // Default grade base
                );
            }

            $grade = Grade::updateOrCreate(
                [
                    'student_id' => $student->id,
                    'subject_id' => $validated['subject_id'],
                    'trimester_id' => $validated['trimester_id'],
                ],
                [
                    'class_id' => $validated['class_id'],
                    'control_grade' => $gradeData['control_grade'],
                    'exam_grade' => $gradeData['exam_grade'],
                    'average' => $average,
                    'appreciation' => $gradeData['appreciation'] ?? null,
                    'entered_by' => $teacherId,
                    'entered_at' => now(),
                ]
            );

            $results[] = [
                'student_id' => $student->id,
                'grade_id' => $grade->id,
                'average' => $average,
            ];
        }

        return $this->success($results, 'Notes enregistrées avec succès.');
    }

    /**
     * Get teacher's subjects.
     */
    public function subjects(Request $request): JsonResponse
    {
        $teacherId = $request->user()->id;

        $subjects = Subject::where('teacher_id', $teacherId)
            ->with(['classes' => function ($q) {
                $q->where('is_active', true)->with('schoolYear');
            }])
            ->get();

        return $this->success($subjects);
    }

    /**
     * Get timetable.
     */
    public function timetable(Request $request): JsonResponse
    {
        $teacherId = $request->user()->id;
        $classId = $request->query('class_id');

        // TODO: Implement timetable retrieval
        return $this->success([
            'entries' => [],
        ]);
    }
}

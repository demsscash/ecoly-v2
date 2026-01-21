<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Grade;
use App\Models\Trimester;
use App\Services\BulletinService;
use App\Services\GradeCalculationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ParentController extends ApiController
{
    protected BulletinService $bulletinService;
    protected GradeCalculationService $gradeCalc;

    public function __construct(BulletinService $bulletinService, GradeCalculationService $gradeCalc)
    {
        $this->bulletinService = $bulletinService;
        $this->gradeCalc = $gradeCalc;
    }

    /**
     * Get parent's children.
     */
    public function children(Request $request): JsonResponse
    {
        $children = $request->user()->children()
            ->with(['class', 'schoolYear'])
            ->where('status', 'active')
            ->get();

        return $this->success($children);
    }

    /**
     * Get a specific child details.
     */
    public function showChild(Request $request, string $id): JsonResponse
    {
        $child = $request->user()->children()
            ->with(['class', 'schoolYear'])
            ->findOrFail($id);

        return $this->success($child);
    }

    /**
     * Get child bulletins.
     */
    public function childBulletins(Request $request, string $id): JsonResponse
    {
        $child = $request->user()->children()->findOrFail($id);

        $bulletins = [];

        foreach (Trimester::orderBy('order')->get() as $trimester) {
            $bulletinData = $this->bulletinService->getBulletinData($child, $trimester);
            $bulletins[] = [
                'trimester_id' => $trimester->id,
                'trimester_name' => $trimester->name_fr,
                'trimester_name_ar' => $trimester->name_ar,
                'average' => $bulletinData['summary']['average'],
                'rank' => $bulletinData['summary']['rank'],
                'total_students' => $bulletinData['summary']['total'],
                'mention' => $bulletinData['summary']['mention'],
            ];
        }

        return $this->success($bulletins);
    }

    /**
     * Get child bulletin for a specific trimester.
     */
    public function childBulletin(Request $request, string $id, string $trimesterId): JsonResponse
    {
        $child = $request->user()->children()->findOrFail($id);
        $trimester = Trimester::findOrFail($trimesterId);

        $bulletin = $this->bulletinService->getBulletinData($child, $trimester);

        return $this->success($bulletin);
    }

    /**
     * Get child grades by subject.
     */
    public function childGrades(Request $request, string $id): JsonResponse
    {
        $child = $request->user()->children()->findOrFail($id);

        $trimesterId = $request->query('trimester_id');

        if (!$trimesterId) {
            return $this->error('Le paramètre trimester_id est requis.', 400);
        }

        $grades = $this->gradeCalc->getStudentGradesBySubject($child, $trimesterId);

        // Load subjects with grades
        $subjects = $child->class->subjects()->get()->map(function ($subject) use ($grades, $child) {
            $grade = $grades->get($subject->id);
            $coefficient = $this->gradeCalc->getSubjectCoefficient($subject, $child->class_id);

            return [
                'id' => $subject->id,
                'name' => $subject->name_fr,
                'name_ar' => $subject->name_ar,
                'code' => $subject->code,
                'coefficient' => $coefficient,
                'control_grade' => $grade?->control_grade,
                'exam_grade' => $grade?->exam_grade,
                'average' => $grade?->average,
                'appreciation' => $grade?->appreciation,
            ];
        });

        return $this->success($subjects);
    }

    /**
     * Get child attendance.
     */
    public function childAttendance(Request $request, string $id): JsonResponse
    {
        $child = $request->user()->children()->findOrFail($id);

        // TODO: Implement attendance retrieval when attendance system is ready
        // For now, return empty array
        return $this->success([
            'total_days' => 0,
            'absences' => 0,
            'lates' => 0,
            'records' => [],
        ]);
    }

    /**
     * Get child payments.
     */
    public function childPayments(Request $request, string $id): JsonResponse
    {
        $child = $request->user()->children()->findOrFail($id);

        $payments = $child->payments()
            ->where('school_year_id', $child->school_year_id)
            ->get();

        $summary = $child->getPaymentsSummary();

        return $this->success([
            'summary' => $summary,
            'payments' => $payments,
        ]);
    }
}

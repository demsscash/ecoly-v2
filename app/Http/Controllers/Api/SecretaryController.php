<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\SchoolClass;
use App\Models\SchoolYear;
use App\Models\Payment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SecretaryController extends ApiController
{
    /**
     * Get dashboard statistics.
     */
    public function dashboard(Request $request): JsonResponse
    {
        $yearId = $request->query('year_id');

        $totalStudents = Student::when($yearId, fn($q) => $q->where('school_year_id', $yearId))
            ->where('status', 'active')
            ->count();

        $pendingRegistrations = Student::when($yearId, fn($q) => $q->where('school_year_id', $yearId))
            ->where('status', 'inactive')
            ->count();

        $totalClasses = SchoolClass::when($yearId, fn($q) => $q->where('school_year_id', $yearId))
            ->where('is_active', true)
            ->count();

        return $this->success([
            'total_students' => $totalStudents,
            'pending_registrations' => $pendingRegistrations,
            'total_classes' => $totalClasses,
        ]);
    }

    /**
     * Get all students (read-only).
     */
    public function students(Request $request): JsonResponse
    {
        $query = Student::with(['class', 'schoolYear', 'parent']);

        if ($request->query('class_id')) {
            $query->where('class_id', $request->query('class_id'));
        }

        if ($request->query('school_year_id')) {
            $query->where('school_year_id', $request->query('school_year_id'));
        }

        if ($request->query('status')) {
            $query->where('status', $request->query('status'));
        }

        if ($request->query('search')) {
            $search = $request->query('search');
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('matricule', 'like', "%{$search}%");
            });
        }

        $perPage = $request->query('per_page', 20);
        $students = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return $this->paginated($students);
    }

    /**
     * Get a specific student (read-only).
     */
    public function showStudent(string $id): JsonResponse
    {
        $student = Student::with(['class', 'schoolYear', 'parent', 'payments'])
            ->findOrFail($id);

        return $this->success($student);
    }

    /**
     * Get all classes.
     */
    public function classes(Request $request): JsonResponse
    {
        $query = SchoolClass::with(['schoolYear', 'serie', 'mainTeacher']);

        if ($request->query('school_year_id')) {
            $query->where('school_year_id', $request->query('school_year_id'));
        }

        if ($request->query('level_type')) {
            $query->where('level_type', $request->query('level_type'));
        }

        $query->where('is_active', true);

        $classes = $query->orderBy('level')->orderBy('section')->get();

        return $this->success($classes);
    }

    /**
     * Get student payments.
     */
    public function studentPayments(string $id): JsonResponse
    {
        $student = Student::findOrFail($id);

        $payments = $student->payments()
            ->where('school_year_id', $student->school_year_id)
            ->get();

        $summary = $student->getPaymentsSummary();

        return $this->success([
            'summary' => $summary,
            'payments' => $payments,
        ]);
    }

    /**
     * Record a payment.
     */
    public function storePayment(Request $request, string $id): JsonResponse
    {
        $student = Student::findOrFail($id);

        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
            'amount_paid' => 'required|numeric|min:0',
            'payment_date' => 'required|date',
            'payment_method' => 'required|in:cash,check,bank_transfer',
            'reference' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $payment = Payment::create([
            'student_id' => $student->id,
            'school_year_id' => $student->school_year_id,
            'amount' => $validated['amount'],
            'amount_paid' => $validated['amount_paid'],
            'payment_date' => $validated['payment_date'],
            'payment_method' => $validated['payment_method'],
            'reference' => $validated['reference'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'status' => $validated['amount_paid'] >= $validated['amount'] ? 'paid' : 'partial',
        ]);

        return $this->success($payment, 'Paiement enregistré avec succès.', 201);
    }

    /**
     * Update a payment.
     */
    public function updatePayment(Request $request, string $id): JsonResponse
    {
        $payment = Payment::findOrFail($id);

        $validated = $request->validate([
            'amount_paid' => 'sometimes|required|numeric|min:0',
            'payment_date' => 'sometimes|required|date',
            'payment_method' => 'sometimes|required|in:cash,check,bank_transfer',
            'reference' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $payment->update($validated);

        // Update status
        if ($payment->amount_paid >= $payment->amount) {
            $payment->status = 'paid';
        } elseif ($payment->amount_paid > 0) {
            $payment->status = 'partial';
        } else {
            $payment->status = 'pending';
        }
        $payment->save();

        return $this->success($payment, 'Paiement mis à jour avec succès.');
    }

    /**
     * Get school years.
     */
    public function schoolYears(): JsonResponse
    {
        $years = SchoolYear::orderByDesc('start_date')->get();

        return $this->success($years);
    }

    /**
     * Search students.
     */
    public function searchStudents(Request $request): JsonResponse
    {
        $search = $request->query('q');

        if (!$search || strlen($search) < 2) {
            return $this->success([]);
        }

        $students = Student::where('status', 'active')
            ->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('matricule', 'like', "%{$search}%");
            })
            ->with(['class', 'schoolYear'])
            ->limit(20)
            ->get();

        return $this->success($students);
    }
}

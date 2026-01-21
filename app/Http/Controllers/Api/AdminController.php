<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\SchoolClass;
use App\Models\SchoolYear;
use App\Models\User;
use App\Models\Trimester;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AdminController extends ApiController
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

        $totalClasses = SchoolClass::when($yearId, fn($q) => $q->where('school_year_id', $yearId))
            ->where('is_active', true)
            ->count();

        $totalTeachers = User::where('role', 'teacher')
            ->where('is_active', true)
            ->count();

        $totalParents = User::where('role', 'parent')
            ->where('is_active', true)
            ->count();

        return $this->success([
            'total_students' => $totalStudents,
            'total_classes' => $totalClasses,
            'total_teachers' => $totalTeachers,
            'total_parents' => $totalParents,
        ]);
    }

    /**
     * Get all students.
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
     * Get a specific student.
     */
    public function showStudent(string $id): JsonResponse
    {
        $student = Student::with(['class', 'schoolYear', 'parent', 'grades'])
            ->findOrFail($id);

        return $this->success($student);
    }

    /**
     * Create a student.
     */
    public function storeStudent(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'first_name_ar' => 'nullable|string|max:255',
            'last_name_ar' => 'nullable|string|max:255',
            'birth_date' => 'required|date',
            'birth_place' => 'nullable|string|max:255',
            'birth_place_ar' => 'nullable|string|max:255',
            'gender' => 'required|in:male,female',
            'nationality' => 'nullable|string|max:255',
            'guardian_name' => 'required|string|max:255',
            'guardian_name_ar' => 'nullable|string|max:255',
            'guardian_phone' => 'required|string|max:255',
            'guardian_phone_2' => 'nullable|string|max:255',
            'guardian_email' => 'nullable|email|max:255',
            'guardian_profession' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'address_ar' => 'nullable|string',
            'class_id' => 'nullable|exists:classes,id',
            'school_year_id' => 'required|exists:school_years,id',
            'parent_id' => 'nullable|exists:users,id',
            'enrollment_date' => 'required|date',
            'previous_school' => 'nullable|string|max:255',
            'status' => 'nullable|in:active,inactive,transferred,graduated',
        ]);

        $validated['matricule'] = Student::generateMatricule($validated['school_year_id'] ?? null);

        $student = Student::create($validated);

        return $this->success($student->load(['class', 'schoolYear']), 'Élève créé avec succès.', 201);
    }

    /**
     * Update a student.
     */
    public function updateStudent(Request $request, string $id): JsonResponse
    {
        $student = Student::findOrFail($id);

        $validated = $request->validate([
            'first_name' => 'sometimes|string|max:255',
            'last_name' => 'sometimes|string|max:255',
            'first_name_ar' => 'nullable|string|max:255',
            'last_name_ar' => 'nullable|string|max:255',
            'birth_date' => 'sometimes|date',
            'birth_place' => 'nullable|string|max:255',
            'birth_place_ar' => 'nullable|string|max:255',
            'gender' => 'sometimes|in:male,female',
            'nationality' => 'nullable|string|max:255',
            'guardian_name' => 'sometimes|string|max:255',
            'guardian_name_ar' => 'nullable|string|max:255',
            'guardian_phone' => 'sometimes|string|max:255',
            'guardian_phone_2' => 'nullable|string|max:255',
            'guardian_email' => 'nullable|email|max:255',
            'guardian_profession' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'address_ar' => 'nullable|string',
            'class_id' => 'nullable|exists:classes,id',
            'school_year_id' => 'nullable|exists:school_years,id',
            'parent_id' => 'nullable|exists:users,id',
            'enrollment_date' => 'sometimes|date',
            'previous_school' => 'nullable|string|max:255',
            'status' => 'nullable|in:active,inactive,transferred,graduated',
        ]);

        $student->update($validated);

        return $this->success($student->load(['class', 'schoolYear']), 'Élève mis à jour avec succès.');
    }

    /**
     * Delete a student.
     */
    public function deleteStudent(string $id): JsonResponse
    {
        $student = Student::findOrFail($id);
        $student->delete();

        return $this->success(null, 'Élève supprimé avec succès.');
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
     * Get all teachers.
     */
    public function teachers(): JsonResponse
    {
        $teachers = User::where('role', 'teacher')
            ->where('is_active', true)
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();

        return $this->success($teachers);
    }

    /**
     * Get all parents.
     */
    public function parents(): JsonResponse
    {
        $parents = User::where('role', 'parent')
            ->where('is_active', true)
            ->with('children')
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();

        return $this->success($parents);
    }

    /**
     * Create a parent user.
     */
    public function storeParent(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:255',
            'password' => 'required|string|min:8',
        ]);

        $user = User::create([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'password' => bcrypt($validated['password']),
            'role' => 'parent',
            'is_active' => true,
        ]);

        return $this->success($user, 'Parent créé avec succès.', 201);
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
     * Get trimesters.
     */
    public function trimesters(): JsonResponse
    {
        $trimesters = Trimester::orderBy('order')->get();

        return $this->success($trimesters);
    }
}

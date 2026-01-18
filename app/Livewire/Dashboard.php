<?php

namespace App\Livewire;

use App\Models\Student;
use App\Models\SchoolClass;
use App\Models\User;
use App\Models\Payment;
use App\Models\SchoolYear;
use App\Models\Trimester;
use App\Models\Grade;
use App\Models\Subject;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\DB;

#[Layout('layouts.app')]
#[Title('Tableau de bord - Ecoly')]
class Dashboard extends Component
{
    public function render()
    {
        $user = auth()->user();
        $schoolYear = SchoolYear::where('is_active', true)->first();
        
        $data = [
            'schoolYear' => $schoolYear,
        ];

        // Admin & Secretary stats
        if ($user->hasRole(['admin', 'secretary'])) {
            $data['studentsStats'] = $this->getStudentsStats($schoolYear);
            $data['classesStats'] = $this->getClassesStats($schoolYear);
            $data['financialStats'] = $this->getFinancialStats($schoolYear);
            $data['recentStudents'] = $this->getRecentStudents($schoolYear);
            $data['unpaidStudents'] = $this->getUnpaidStudents($schoolYear);
        }

        // Admin only stats
        if ($user->hasRole('admin')) {
            $data['usersStats'] = $this->getUsersStats();
            $data['trimesterStats'] = $this->getTrimesterStats($schoolYear);
        }

        // Teacher stats
        if ($user->hasRole('teacher')) {
            $data['teacherStats'] = $this->getTeacherStats($user, $schoolYear);
            $data['teacherClasses'] = $this->getTeacherClasses($user, $schoolYear);
        }

        return view('livewire.dashboard', $data);
    }

    /**
     * Get students statistics
     */
    private function getStudentsStats(?SchoolYear $schoolYear): array
    {
        if (!$schoolYear) {
            return ['total' => 0, 'active' => 0, 'inactive' => 0, 'boys' => 0, 'girls' => 0];
        }

        $students = Student::where('school_year_id', $schoolYear->id);

        return [
            'total' => $students->count(),
            'active' => (clone $students)->where('status', 'active')->count(),
            'inactive' => (clone $students)->where('status', '!=', 'active')->count(),
            'boys' => (clone $students)->where('gender', 'male')->count(),
            'girls' => (clone $students)->where('gender', 'female')->count(),
        ];
    }

    /**
     * Get classes statistics
     */
    private function getClassesStats(?SchoolYear $schoolYear): array
    {
        if (!$schoolYear) {
            return ['total' => 0, 'with_students' => 0, 'average_size' => 0];
        }

        $classes = SchoolClass::withCount([
            'students' => fn($q) => $q->where('school_year_id', $schoolYear->id)
                ->where('status', 'active')
        ])->get();

        return [
            'total' => $classes->count(),
            'with_students' => $classes->filter(fn($c) => $c->students_count > 0)->count(),
            'average_size' => $classes->avg('students_count') ?: 0,
        ];
    }

    /**
     * Get financial statistics
     */
    private function getFinancialStats(?SchoolYear $schoolYear): array
    {
        if (!$schoolYear) {
            return [
                'total_due' => 0,
                'total_paid' => 0,
                'balance' => 0,
                'collection_rate' => 0,
                'paid_count' => 0,
                'pending_count' => 0,
            ];
        }

        $payments = Payment::where('school_year_id', $schoolYear->id);
        $totalDue = (clone $payments)->sum('amount');
        $totalPaid = (clone $payments)->sum('amount_paid');
        $balance = $totalDue - $totalPaid;
        $collectionRate = $totalDue > 0 ? ($totalPaid / $totalDue) * 100 : 0;

        return [
            'total_due' => $totalDue,
            'total_paid' => $totalPaid,
            'balance' => $balance,
            'collection_rate' => round($collectionRate, 1),
            'paid_count' => (clone $payments)->where('status', 'paid')->count(),
            'pending_count' => (clone $payments)->where('status', 'pending')->count(),
        ];
    }

    /**
     * Get users statistics
     */
    private function getUsersStats(): array
    {
        return [
            'total' => User::count(),
            'active' => User::where('is_active', true)->count(),
            'admins' => User::where('role', 'admin')->count(),
            'teachers' => User::where('role', 'teacher')->count(),
            'secretaries' => User::where('role', 'secretary')->count(),
        ];
    }

    /**
     * Get trimester statistics
     */
    private function getTrimesterStats(?SchoolYear $schoolYear): ?array
    {
        if (!$schoolYear) return null;

        $currentTrimester = Trimester::where('school_year_id', $schoolYear->id)
            ->where('status', 'open')
            ->first();

        if (!$currentTrimester) return null;

        $totalGrades = Grade::where('trimester_id', $currentTrimester->id)->count();
        $completedGrades = Grade::where('trimester_id', $currentTrimester->id)
            ->whereNotNull('average')
            ->count();

        return [
            'current' => $currentTrimester,
            'total_grades' => $totalGrades,
            'completed_grades' => $completedGrades,
            'completion_rate' => $totalGrades > 0 ? round(($completedGrades / $totalGrades) * 100, 1) : 0,
        ];
    }

    /**
     * Get recent students
     */
    private function getRecentStudents(?SchoolYear $schoolYear): \Illuminate\Support\Collection
    {
        if (!$schoolYear) {
            return collect();
        }

        return Student::with('class')
            ->where('school_year_id', $schoolYear->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
    }

    /**
     * Get students with unpaid balances
     */
    private function getUnpaidStudents(?SchoolYear $schoolYear): \Illuminate\Support\Collection
    {
        if (!$schoolYear) {
            return collect();
        }

        return Student::with('class')
            ->where('school_year_id', $schoolYear->id)
            ->where('status', 'active')
            ->whereHas('payments', function($q) use ($schoolYear) {
                $q->where('school_year_id', $schoolYear->id)
                  ->whereRaw('amount > amount_paid');
            })
            ->limit(5)
            ->get();
    }

    /**
     * Get teacher statistics
     */
    private function getTeacherStats(User $user, ?SchoolYear $schoolYear): array
    {
        if (!$schoolYear) {
            return ['classes' => 0, 'subjects' => 0, 'students' => 0, 'grades_to_enter' => 0];
        }

        $currentTrimester = Trimester::where('school_year_id', $schoolYear->id)
            ->where('status', 'open')
            ->first();

        // Get assignments from class_subject table
        $assignments = DB::table('class_subject')
            ->where('teacher_id', $user->id)
            ->get();

        $classIds = $assignments->pluck('class_id')->unique();
        $subjectIds = $assignments->pluck('subject_id')->unique();

        $studentsCount = Student::where('school_year_id', $schoolYear->id)
            ->where('status', 'active')
            ->whereIn('class_id', $classIds)
            ->count();

        $gradesToEnter = 0;
        if ($currentTrimester) {
            foreach ($assignments as $assignment) {
                $studentsInClass = Student::where('class_id', $assignment->class_id)
                    ->where('school_year_id', $schoolYear->id)
                    ->where('status', 'active')
                    ->count();

                $enteredGrades = Grade::where('trimester_id', $currentTrimester->id)
                    ->where('subject_id', $assignment->subject_id)
                    ->whereHas('student', fn($q) => $q->where('class_id', $assignment->class_id))
                    ->whereNotNull('average')
                    ->count();

                $gradesToEnter += max(0, $studentsInClass - $enteredGrades);
            }
        }

        return [
            'classes' => $classIds->count(),
            'subjects' => $subjectIds->count(),
            'students' => $studentsCount,
            'grades_to_enter' => $gradesToEnter,
        ];
    }

    /**
     * Get teacher classes
     */
    private function getTeacherClasses(User $user, ?SchoolYear $schoolYear): \Illuminate\Support\Collection
    {
        if (!$schoolYear) {
            return collect();
        }

        // Get assignments from class_subject table
        $assignments = DB::table('class_subject')
            ->where('teacher_id', $user->id)
            ->get();

        $classIds = $assignments->pluck('class_id')->unique();
        
        return SchoolClass::withCount([
            'students' => fn($q) => $q->where('school_year_id', $schoolYear->id)
                ->where('status', 'active')
        ])
        ->whereIn('id', $classIds)
        ->get()
        ->map(function($class) use ($assignments) {
            $class->subjects_count = $assignments->where('class_id', $class->id)->count();
            return $class;
        });
    }
}

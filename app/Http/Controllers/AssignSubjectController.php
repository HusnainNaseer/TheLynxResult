<?php

namespace App\Http\Controllers;

use App\Models\TeacherSubjectAssignment;
use App\Models\User;
use App\Models\Branch;
use App\Models\Classes;
use App\Models\ClassSection;
use App\Models\ClassSubject;
use App\Models\Session;
use App\Models\SubjectWiseMarks;
use App\Support\BranchScope;

use Illuminate\Http\Request;

class AssignSubjectController extends Controller
{
    // ─────────────────────────────────────────────
    // Teacher listing (only users with role = Teacher)
    // ─────────────────────────────────────────────
    public function index()
    {
        $teachers = User::role('Teacher')
            ->when(BranchScope::coordinatorBranchId(), fn($query, $branchId) => $query->where('branch_id', $branchId))
            ->select('id', 'name', 'email', 'branch_id', 'branch_name', 'branch_email', 'branch_phone', 'branch_address')
            ->paginate(15);

        return view('teachers.assign_subjects_list', compact('teachers'));
    }

    // ─────────────────────────────────────────────
    // Show the assign-subject form for a teacher
    // ─────────────────────────────────────────────
    public function create(User $teacher)
    {
        BranchScope::abortIfCoordinatorOutside($teacher->branch_id);

        $activeSession = Session::active()->first();

        // Fetch the already-assigned subjects for this teacher
        $assignments = TeacherSubjectAssignment::where('teacher_id', $teacher->id)
            ->when($activeSession, fn($query) => $query->where('session_id', $activeSession->id))
            ->orderBy('branch_name')
            ->orderBy('class_name')
            ->orderBy('section_name')
            ->get();

        $assignmentGroups = $assignments
            ->groupBy(fn($assignment) => $assignment->branch_id . '|' . $assignment->class_id . '|' . $assignment->section_id)
            ->map(function ($rows, $key) {
                $first = $rows->first();

                return (object) [
                    'key' => $key,
                    'ids' => $rows->pluck('id')->values(),
                    'branch_id' => $first->branch_id,
                    'branch_name' => $first->branch_name,
                    'class_id' => $first->class_id,
                    'class_name' => $first->class_name,
                    'section_id' => $first->section_id,
                    'section_name' => $first->section_name,
                    'subject_ids' => $rows->pluck('subject_id')->map(fn($id) => (string) $id)->values(),
                    'subject_names' => $rows->pluck('subject_name')->filter()->values(),
                ];
            })
            ->values();

        $branches = $this->assignmentBranches();

        return view('teachers.assign_subject', compact('teacher', 'assignments', 'assignmentGroups', 'branches'));
    }

    // ─────────────────────────────────────────────
    // Store a new subject assignment
    // ─────────────────────────────────────────────
    public function store(Request $request)
    {
        $validated = $request->validate([
            'teacher_id'   => 'required|exists:users,id',
            'branch_id'    => 'required|integer',
            'branch_name'  => 'nullable|string|max:255',
            'class_id'     => 'required|integer',
            'class_name'   => 'nullable|string|max:255',
            'section_id'   => 'required|integer',
            'section_name' => 'nullable|string|max:255',
            'subject_ids'   => 'required|array',
            'subject_ids.*' => 'required|integer',
            'assign_all' => 'nullable|boolean',
        ]);
        $activeSession = Session::active()->first();

        if (!$activeSession) {
            return response()->json([
                'success' => false,
                'message' => 'Please activate a session before assigning subjects.',
            ], 422);
        }

        BranchScope::abortIfCoordinatorOutside($validated['branch_id']);

        $teacher = User::role('Teacher')
            ->where('id', $validated['teacher_id'])
            ->when(BranchScope::coordinatorBranchId(), fn($query, $branchId) => $query->where('branch_id', $branchId))
            ->first();

        if (!$teacher) {
            return response()->json([
                'success' => false,
                'message' => 'Selected teacher does not belong to your branch.',
            ], 403);
        }

        $classExistsInBranch = Classes::where('id', $validated['class_id'])
            ->where('erp_branch_id', $validated['branch_id'])
            ->where('session_id', $activeSession->id)
            ->exists();

        if (!$classExistsInBranch) {
            return response()->json([
                'success' => false,
                'message' => 'Selected class does not belong to the selected branch.',
            ], 422);
        }

        $sectionExistsInClass = ClassSection::where('class_id', $validated['class_id'])
            ->where('section_id', $validated['section_id'])
            ->where('session_id', $activeSession->id)
            ->exists();

        if (!$sectionExistsInClass) {
            return response()->json([
                'success' => false,
                'message' => 'Selected section does not belong to the selected class.',
            ], 422);
        }

        $allowedSubjectIds = ClassSubject::where('class_id', $validated['class_id'])
            ->where('session_id', $activeSession->id)
            ->where('branch_id', $validated['branch_id'])
            ->pluck('subject_id')
            ->map(fn($id) => (int) $id);

        $requestedSubjectIds = collect($validated['subject_ids'])
            ->map(fn($id) => (int) $id)
            ->unique()
            ->values();
        $isClassTeacherAssignment = (bool) ($validated['assign_all'] ?? false);

        $invalidSubjectIds = $requestedSubjectIds->diff($allowedSubjectIds);

        if ($invalidSubjectIds->isNotEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'One or more selected subjects are not assigned to this class.',
            ], 422);
        }

        $allSectionAssignments = TeacherSubjectAssignment::where([
            'branch_id'  => $validated['branch_id'],
            'class_id'   => $validated['class_id'],
            'section_id' => $validated['section_id'],
            'session_id' => $activeSession->id,
        ])
            ->get();

        $classTeacherAssignment = $this->findClassTeacherAssignment($allSectionAssignments, $allowedSubjectIds);

        if ($isClassTeacherAssignment && $classTeacherAssignment) {
            $teacherName = User::find($classTeacherAssignment->teacher_id)?->name ?? 'this teacher';

            return response()->json([
                'success' => false,
                'message' => 'This section is assigned to ' . $teacherName . ' as class teacher. All subjects are already assigned.',
            ], 422);
        }

        if ($isClassTeacherAssignment) {
            if ($allSectionAssignments->isNotEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Some subjects are already assigned to subject teachers. Assign remaining subjects individually.',
                ], 422);
            }

            $requestedSubjectIds = $allowedSubjectIds->values();
        }

        $subjectTeacherAssignments = $classTeacherAssignment
            ? $allSectionAssignments->where('teacher_id', '!=', $classTeacherAssignment->teacher_id)->values()
            : $allSectionAssignments;

        $existingAssignments = $subjectTeacherAssignments
            ->whereIn('subject_id', $requestedSubjectIds)
            ->values();

        if ($existingAssignments->isNotEmpty()) {
            $firstAssignment = $existingAssignments->first();
            $teacherName = User::find($firstAssignment->teacher_id)?->name ?? 'another teacher';

            return response()->json([
                'success' => false,
                'message' => 'This subject of this section is already assigned to ' . $teacherName . '.',
            ], 422);
        }

        $newSubjectIds = $requestedSubjectIds;

        $subjects = SubjectWiseMarks::whereIn('id', $newSubjectIds)
            ->where('session_id', $activeSession->id)
            ->get()
            ->keyBy('id');

        $assignments = $newSubjectIds->map(function ($subjectId) use ($validated, $subjects, $activeSession) {
            return TeacherSubjectAssignment::create([
                'teacher_id' => $validated['teacher_id'],
                'session_id' => $activeSession->id,
                'erp_session_id' => $activeSession->erp_session_id ?: (string) $activeSession->id,
                'branch_id' => $validated['branch_id'],
                'branch_name' => $validated['branch_name'] ?? null,
                'class_id' => $validated['class_id'],
                'class_name' => $validated['class_name'] ?? null,
                'section_id' => $validated['section_id'],
                'section_name' => $validated['section_name'] ?? null,
                'subject_id' => $subjectId,
                'subject_name' => $subjects->get($subjectId)?->subject_name,
                'assigned_by' => auth()->id(),
            ]);
        })->values();

        return response()->json([
            'success'    => true,
            'message'    => $assignments->count() . ' subject(s) assigned successfully.',
            'assignments' => $assignments,
            'group' => [
                'key' => $validated['branch_id'] . '|' . $validated['class_id'] . '|' . $validated['section_id'],
                'ids' => $assignments->pluck('id')->values(),
                'branch_id' => $validated['branch_id'],
                'branch_name' => $validated['branch_name'] ?? null,
                'class_id' => $validated['class_id'],
                'class_name' => $validated['class_name'] ?? null,
                'section_id' => $validated['section_id'],
                'section_name' => $validated['section_name'] ?? null,
                'subject_ids' => $assignments->pluck('subject_id')->map(fn($id) => (string) $id)->values(),
                'subject_names' => $assignments->pluck('subject_name')->filter()->values(),
            ],
        ]);
    }

    // ─────────────────────────────────────────────
    // Delete an assignment
    // ─────────────────────────────────────────────
    public function destroy(TeacherSubjectAssignment $assignment)
    {
        $activeSession = Session::active()->first();

        if ($activeSession && (int) $assignment->session_id !== (int) $activeSession->id) {
            return response()->json([
                'success' => false,
                'message' => 'This assignment does not belong to the active session.',
            ], 403);
        }

        BranchScope::abortIfCoordinatorOutside($assignment->branch_id);

        $assignment->delete();

        return response()->json([
            'success' => true,
            'message' => 'Assignment removed successfully.',
        ]);
    }

    // ─────────────────────────────────────────────
    // Local DB helpers used by the assignment form.
    // ─────────────────────────────────────────────

    /** GET /assign-subjects/api/branches */
    public function apiBranches()
    {
        return response()->json($this->assignmentBranches());
    }

    private function assignmentBranches()
    {
        if ($branchId = BranchScope::coordinatorBranchId()) {
            $branch = Branch::where('erp_branch_id', $branchId)->first();
            $user = auth()->user();

            return collect([
                [
                    'id' => $branch?->erp_branch_id ?? $branchId,
                    'name' => $branch?->name ?? $user?->branch_name ?? ('Branch #' . $branchId),
                ],
            ]);
        }

        $branchQuery = Branch::query()
            ->orderBy('name');

        $branches = $branchQuery
            ->get()
            ->map(fn($branch) => [
                'id' => $branch->erp_branch_id,
                'name' => $branch->name,
            ])
            ->values();

        return $branches;
    }

    /** GET /assign-subjects/api/classes?branch_id=X */
    public function apiClasses(Request $request)
    {
        $branchId = $request->query('branch_id');
        BranchScope::abortIfCoordinatorOutside($branchId);

        $activeSession = Session::active()->first();

        $classes = Classes::where('erp_branch_id', $branchId)
            ->when($activeSession, fn($query) => $query->where('session_id', $activeSession->id))
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        return response()->json($classes);
    }

    /** GET /assign-subjects/api/sections?class_id=X */
    public function apiSections(Request $request)
    {
        $classId = $request->query('class_id');
        $activeSession = Session::active()->first();

        if (!$classId) {
            return response()->json([]);
        }

        $classQuery = Classes::where('id', $classId)
            ->when($activeSession, fn($query) => $query->where('session_id', $activeSession->id));
        BranchScope::apply($classQuery);

        if (!$classQuery->exists()) {
            return response()->json([]);
        }

        $sections = ClassSection::where('classsections.class_id', $classId)
            ->when($activeSession, fn($query) => $query->where('classsections.session_id', $activeSession->id))
            ->join('sections', 'classsections.section_id', '=', 'sections.id')
            ->select('sections.id', 'sections.name')
            ->orderBy('sections.name')
            ->get();

        return response()->json($sections);
    }

    /** GET /assign-subjects/api/subjects?branch_id=X&class_id=X&section_id=X */
    public function apiSubjects(Request $request)
    {
        $branchId = $request->query('branch_id');
        $classId = $request->query('class_id');
        $sectionId = $request->query('section_id');
        $activeSession = Session::active()->first();

        if (!$branchId || !$classId || !$sectionId || !$activeSession) {
            return response()->json([
                'data' => [],
                'total' => 0,
                'assigned' => 0,
            ]);
        }

        BranchScope::abortIfCoordinatorOutside($branchId);

        $classExistsInBranch = Classes::where('id', $classId)
            ->where('erp_branch_id', $branchId)
            ->where('session_id', $activeSession->id)
            ->exists();

        if (!$classExistsInBranch) {
            return response()->json([
                'data' => [],
                'total' => 0,
                'assigned' => 0,
            ]);
        }

        $subjects = ClassSubject::where('class_subjects.class_id', $classId)
            ->where('class_subjects.session_id', $activeSession->id)
            ->where('class_subjects.branch_id', $branchId)
            ->join('subject_wise_marks', 'class_subjects.subject_id', '=', 'subject_wise_marks.id')
            ->where('subject_wise_marks.session_id', $activeSession->id)
            ->select('subject_wise_marks.id', 'subject_wise_marks.subject_name as name')
            ->orderBy('subject_wise_marks.subject_name')
            ->get();

        $sectionAssignments = TeacherSubjectAssignment::where([
            'branch_id' => $branchId,
            'class_id' => $classId,
            'section_id' => $sectionId,
            'session_id' => $activeSession->id,
        ])
            ->get();

        $classTeacherAssignment = $this->findClassTeacherAssignment($sectionAssignments, $subjects->pluck('id')->map(fn($id) => (int) $id));
        $classTeacherName = null;

        if ($classTeacherAssignment) {
            $classTeacherName = User::find($classTeacherAssignment->teacher_id)?->name ?? 'this teacher';
        }

        $subjectTeacherAssignments = $classTeacherAssignment
            ? $sectionAssignments->where('teacher_id', '!=', $classTeacherAssignment->teacher_id)->values()
            : $sectionAssignments;

        $assignedBySubject = $subjectTeacherAssignments
            ->keyBy(fn($assignment) => (string) $assignment->subject_id);

        $teacherNames = User::whereIn('id', $subjectTeacherAssignments->pluck('teacher_id')->unique())
            ->pluck('name', 'id');

        $subjectsWithAssignmentState = $subjects
            ->map(function ($subject) use ($assignedBySubject, $teacherNames) {
                $assignment = $assignedBySubject->get((string) $subject->id);

                return [
                    'id' => $subject->id,
                    'name' => $subject->name,
                    'disabled' => (bool) $assignment,
                    'assigned_to' => $assignment
                        ? ($teacherNames[$assignment->teacher_id] ?? 'another teacher')
                        : null,
                ];
            })
            ->values();

        return response()->json([
            'data' => $subjectsWithAssignmentState,
            'total' => $subjects->count(),
            'assigned' => $subjectTeacherAssignments->pluck('subject_id')->unique()->count(),
            'locked' => false,
            'class_teacher_assigned' => (bool) $classTeacherAssignment,
            'class_teacher_name' => $classTeacherName,
            'all_option_message' => $classTeacherAssignment
                ? 'This section is assigned to ' . $classTeacherName . ' as class teacher. All subjects are already assigned.'
                : null,
        ]);
    }

    private function findClassTeacherAssignment($assignments, $subjectIds): ?TeacherSubjectAssignment
    {
        $subjectIds = collect($subjectIds)->map(fn($id) => (int) $id)->unique()->values();

        if ($subjectIds->isEmpty() || $assignments->isEmpty()) {
            return null;
        }

        foreach ($assignments->groupBy('teacher_id') as $teacherAssignments) {
            $teacherSubjectIds = $teacherAssignments
                ->pluck('subject_id')
                ->map(fn($id) => (int) $id)
                ->unique()
                ->values();

            if ($teacherSubjectIds->count() === $subjectIds->count() && $subjectIds->diff($teacherSubjectIds)->isEmpty()) {
                return $teacherAssignments->first();
            }
        }

        return null;
    }
}

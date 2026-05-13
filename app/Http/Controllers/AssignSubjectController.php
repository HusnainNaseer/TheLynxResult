<?php

namespace App\Http\Controllers;

use App\Models\TeacherSubjectAssignment;
use App\Models\User;
use App\Models\Classes;
use App\Models\Section;
use App\Models\ClassSection;
use App\Models\SubjectWiseMarks;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AssignSubjectController extends Controller
{
    // ─────────────────────────────────────────────
    // Teacher listing (only users with role = Teacher)
    // ─────────────────────────────────────────────
    public function index()
    {
        $teachers = User::role('Teacher')
            ->select('id', 'name', 'email', 'branch_name', 'branch_email', 'branch_phone', 'branch_address')
            ->paginate(15);

        return view('teachers.assign_subjects_list', compact('teachers'));
    }

    // ─────────────────────────────────────────────
    // Show the assign-subject form for a teacher
    // ─────────────────────────────────────────────
    public function create(User $teacher)
    {
        // Fetch the already-assigned subjects for this teacher
        $assignments = TeacherSubjectAssignment::where('teacher_id', $teacher->id)
            ->orderBy('branch_name')
            ->orderBy('class_name')
            ->orderBy('section_name')
            ->get();


        return view('teachers.assign_subject', compact('teacher', 'assignments'));
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
            'subject_id'   => 'required|integer',
            'subject_name' => 'nullable|string|max:255',
        ]);

        // Check for duplicate
        $exists = TeacherSubjectAssignment::where([
            'teacher_id' => $validated['teacher_id'],
            'branch_id'  => $validated['branch_id'],
            'class_id'   => $validated['class_id'],
            'section_id' => $validated['section_id'],
            'subject_id' => $validated['subject_id'],
        ])
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'This subject is already assigned to the teacher for the selected section.',
            ], 422);
        }

        $assignment = TeacherSubjectAssignment::create($validated);

        return response()->json([
            'success'    => true,
            'message'    => 'Subject assigned successfully.',
            'assignment' => $assignment,
        ]);
    }

    // ─────────────────────────────────────────────
    // Delete an assignment
    // ─────────────────────────────────────────────
    public function destroy(TeacherSubjectAssignment $assignment)
    {
        $assignment->delete();

        return response()->json([
            'success' => true,
            'message' => 'Assignment removed successfully.',
        ]);
    }

    // ─────────────────────────────────────────────
    // ERP API proxy helpers (called via AJAX)
    // These keep the ERP base URL server-side only.
    // ─────────────────────────────────────────────

    /** GET /assign-subjects/api/branches */
    public function apiBranches()
    {
        $branches = User::selectRaw('DISTINCT branch_id as id, branch_name as name')
            ->whereNotNull('branch_id')
            ->whereNotNull('branch_name')
            ->orderBy('branch_name')
            ->get();

        if ($branches->isEmpty()) {
            $branches = Classes::selectRaw('DISTINCT erp_branch_id as id, erp_branch_id as name')
                ->whereNotNull('erp_branch_id')
                ->orderBy('erp_branch_id')
                ->get();
        }

        return response()->json($branches);
    }

    /** GET /assign-subjects/api/classes?branch_id=X */
    public function apiClasses(Request $request)
    {
        $branchId = $request->query('branch_id');

        $classes = Classes::where('erp_branch_id', $branchId)->select('erp_class_id as id', 'name')->get();

        return response()->json($classes);
    }

    /** GET /assign-subjects/api/sections?class_id=X */
    public function apiSections(Request $request)
    {
        $classId = $request->query('class_id');

        if (!$classId) {
            return response()->json([]);
        }

        $sections = ClassSection::where('erp_class_id', $classId)
            ->join('sections', 'classsections.section_id', '=', 'sections.id')
            ->select('classsections.erp_section_id as id', 'sections.name')
            ->orderBy('sections.name')
            ->get();

        return response()->json($sections);
    }

    /** GET /assign-subjects/api/subjects */
    public function apiSubjects()
    {
        $subjects = SubjectWiseMarks::select('id', 'subject_name as name')->get();
        return response()->json($subjects);
    }
}

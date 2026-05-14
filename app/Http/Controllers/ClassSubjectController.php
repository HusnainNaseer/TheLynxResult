<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Classes;
use App\Models\ClassSubject;
use App\Models\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClassSubjectController extends Controller
{
    public function index()
    {
        $activeSession = Session::active()->first();
        $classQuery = Classes::query()
            ->when($activeSession, fn($query) => $query->where('session_id', $activeSession->id));

        $usedBranchIds = (clone $classQuery)->select('erp_branch_id')
            ->distinct()
            ->whereNotNull('erp_branch_id')
            ->pluck('erp_branch_id');

        $branches = Branch::whereIn('erp_branch_id', $usedBranchIds)
            ->orderBy('name')
            ->get()
            ->map(fn($branch) => [
                'id' => $branch->erp_branch_id,
                'name' => $branch->name,
            ]);

        $classes = (clone $classQuery)->orderBy('name')->get();

        $subjects = DB::table('subject_wise_marks')
            ->select('id', 'subject_name')
            ->distinct()
            ->orderBy('subject_name')
            ->get();

        $assignedSubjects = ClassSubject::with('class')
            ->when($activeSession, fn($query) => $query->where('session_id', $activeSession->id))
            ->get()
            ->groupBy('class_id');

        $classOptions = $classes->map(fn($c) => [
            'id'     => (string) $c->id,
            'name'   => $c->name,
            'branch' => (string) $c->erp_branch_id,
        ])->values();

        return view(
            'class-subjects.index',
            compact(
                'branches',
                'classes',
                'subjects',
                'assignedSubjects',
                'classOptions'
            )
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'branch_id' => 'required',
            'class_id' => 'required',
            'subjects' => 'required|array',
            'subjects.*' => 'required|integer',
        ]);

        $subjectIds = collect($validated['subjects'])
            ->map(fn($subjectId) => (int) $subjectId)
            ->unique()
            ->values();
        $activeSession = Session::active()->first();

        if (!$activeSession) {
            return back()
                ->withInput()
                ->with('error', 'Please activate a session before assigning class subjects.');
        }

        $alreadyAssigned = ClassSubject::where('class_id', $validated['class_id'])
            ->where('session_id', $activeSession->id)
            ->whereIn('subject_id', $subjectIds)
            ->pluck('subject_id')
            ->map(fn($subjectId) => (int) $subjectId);

        $newSubjectIds = $subjectIds->diff($alreadyAssigned);

        if ($newSubjectIds->isEmpty()) {
            return back()
                ->withInput()
                ->with('error', 'Selected subject(s) are already assigned to this class.');
        }

        foreach ($newSubjectIds as $subjectId) {

            ClassSubject::create([
                'branch_id' => $validated['branch_id'],
                'session_id' => $activeSession->id,
                'erp_session_id' => $activeSession->erp_session_id ?: (string) $activeSession->id,
                'class_id' => $validated['class_id'],
                'subject_id' => $subjectId,
            ]);
        }

        return back()->with('success', 'Subjects assigned successfully.');
    }

    public function destroy($classId)
    {
        $activeSession = Session::active()->first();
        $deleted = ClassSubject::where('class_id', $classId)
            ->when($activeSession, fn($query) => $query->where('session_id', $activeSession->id))
            ->delete();

        if (!$deleted) {
            return back()->with('error', 'No subjects were found for this class.');
        }

        return back()->with('success', 'Class subjects removed successfully.');
    }
}

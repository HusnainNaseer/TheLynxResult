<?php

namespace App\Http\Controllers;

use App\Models\Session;
use App\Models\StudentMarks;
use App\Models\StudentResult;
use App\Models\Student;
use App\Models\User;
use App\Models\Branch;
use App\Models\Classes;
use App\Models\ClassSection;
use App\Models\ClassSubject;
use App\Models\Section;
use App\Models\TeacherSubjectAssignment;
use App\Services\ErpAuthService;
use App\Services\StudentSyncService;
// use App\Models\http\Resources\BranchResource;
// use Illuminate\Http\Request;
use App\Models\SubjectWiseMarks;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TheLynxResultController extends Controller
{
    public function syncStudents(
        Request $request,
        StudentSyncService $studentSyncService,
        ErpAuthService $erpAuthService
    )
    {
        try {
            $user = auth()->user();
            $branchId = $user?->hasRole('Coordinator') ? (string) $user->branch_id : null;

            if ($user?->hasRole('Coordinator') && !$branchId) {
                return redirect()
                    ->route('students.result', $request->except('_token'))
                    ->with('error', 'Please set your branch before syncing students.');
            }

            $erpLogin = $erpAuthService->login($user);

            if (!$erpLogin['ok']) {
                return redirect()
                    ->route('students.result', $request->except('_token'))
                    ->with('error', $erpLogin['message']);
            }

            $sync = $studentSyncService->syncForActiveSession($branchId);
        } catch (\Throwable $e) {
            Log::error('Student sync failed from result list: ' . $e->getMessage());

            return redirect()
                ->route('students.result', $request->except('_token'))
                ->with('error', 'Student sync failed. Please try again.');
        }

        $type = ($sync['synced'] ?? 0) > 0 ? 'success' : 'error';
        $message = $sync['message'] ?? 'Student sync completed.';

        if (($sync['synced'] ?? 0) > 0 || ($sync['skipped'] ?? 0) > 0) {
            $message .= ' Synced: ' . ($sync['synced'] ?? 0) . ', skipped: ' . ($sync['skipped'] ?? 0) . '.';
        }

        return redirect()
            ->route('students.result', $request->except('_token'))
            ->with($type, $message);
    }

    public function results(Request $request)
    {
        $user = auth()->user();

        if (
            !$user->branch_name ||
            !$user->branch_address ||
            !$user->branch_email ||
            !$user->branch_phone
        ) {
            return redirect()->route('profile.edit')
                ->with('error', 'Please Fill Branch Information to access results.');
        }

        $isAdmin = $user->hasRole('Admin');
        $activeSession = Session::active()->first();
        $selectedBranchId = $request->get('branch_id');
        $selectedClassId = $request->get('class_id');
        $selectedSectionId = $request->get('section_id');
        $selectedStatus = $request->get('status');
        $search = $request->get('search');

        $students = $this->accessibleStudentQuery($user, [
            'branch_id' => $selectedBranchId,
            'class_id' => $selectedClassId,
            'section_id' => $selectedSectionId,
            'search' => $search,
        ])
            ->when($selectedStatus, fn($query) => $this->applyResultStatusFilter($query, $selectedStatus, $activeSession))
            ->with(['result' => fn($query) => $query->where('session_id', $activeSession?->id)->with('session')])
            ->orderBy('stdname')
            ->paginate(15)
            ->appends($request->query());

        $this->decorateStudentRows($students, $user);

        $branches = $this->resultBranches($user);
        $classes = $selectedBranchId
            ? $this->resultClassesFor($user, $selectedBranchId)
            : collect();
        $sections = ($selectedBranchId && $selectedClassId)
            ? $this->resultSectionsFor($user, $selectedBranchId, $selectedClassId)
            : collect();

        $canManageResults = $user->hasRole('Admin') || $user->hasRole('Coordinator');
        $canUseForwardControls = $user->hasRole('Teacher') && !$canManageResults;

        if ($request->ajax()) {
            return response()->json([
                'rows' => view('results.partials.student_rows', compact('students', 'canManageResults'))->render(),
                'pagination' => $students->links()->render(),
                'classes' => $classes->map(fn($class) => [
                    'id' => data_get($class, 'id'),
                    'name' => data_get($class, 'name'),
                ])->values(),
                'sections' => $sections->map(fn($section) => [
                    'id' => data_get($section, 'id'),
                    'name' => data_get($section, 'name'),
                ])->values(),
            ]);
        }

        return view('results.student_result', compact(
            'students',
            'isAdmin',
            'branches',
            'classes',
            'sections',
            'selectedBranchId',
            'selectedClassId',
            'selectedSectionId',
            'selectedStatus',
            'search',
            'canManageResults',
            'canUseForwardControls'
        ));
    }

    public function coordinatorApprovals(Request $request)
    {
        return $this->resultWorkflowList($request, 'forwarded_to_coordinator', 'Coordinator Approval List');
    }

    public function approvedResults(Request $request)
    {
        return $this->resultWorkflowList($request, 'coordinator_approved', 'Approved Results');
    }

    private function resultWorkflowList(Request $request, string $status, string $title)
    {
        $user = auth()->user();
        $activeSession = Session::active()->first();
        $selectedBranchId = $request->get('branch_id');
        $selectedClassId = $request->get('class_id');
        $selectedSectionId = $request->get('section_id');
        $search = $request->get('search');

        $results = StudentResult::with(['student', 'session'])
            ->when($activeSession, fn($query) => $query->where('session_id', $activeSession->id), fn($query) => $query->whereRaw('1 = 0'))
            ->where('workflow_status', $status)
            ->when($user->hasRole('Coordinator') && !$user->hasRole('Admin'), fn($query) => $query->where('branch_id', $user->branch_id))
            ->when($selectedBranchId, fn($query) => $query->where('branch_id', $selectedBranchId))
            ->when($selectedClassId, fn($query) => $query->where('class_id', $selectedClassId))
            ->when($selectedSectionId, fn($query) => $query->where('section_id', $selectedSectionId))
            ->when($search, function ($query) use ($search) {
                $query->where(function ($searchQuery) use ($search) {
                    $searchQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('rollno', 'like', "%{$search}%")
                        ->orWhereHas('student', function ($studentQuery) use ($search) {
                            $studentQuery->where('fathername', 'like', "%{$search}%")
                                ->orWhere('phone_no', 'like', "%{$search}%");
                        });
                });
            })
            ->latest()
            ->paginate(15)
            ->appends($request->query());

        $this->decorateResultRows($results, $user);

        $branches = $this->resultBranches($user);
        $classes = $selectedBranchId ? $this->resultClassesFor($user, $selectedBranchId) : collect();
        $sections = ($selectedBranchId && $selectedClassId) ? $this->resultSectionsFor($user, $selectedBranchId, $selectedClassId) : collect();

        return view('results.workflow_list', compact(
            'results',
            'title',
            'status',
            'branches',
            'classes',
            'sections',
            'selectedBranchId',
            'selectedClassId',
            'selectedSectionId',
            'search'
        ));
    }

    public function result_create(Request $request)
    {
        $user = auth()->user();

        if (
            !$user->branch_name ||
            !$user->branch_address ||
            !$user->branch_email ||
            !$user->branch_phone
        ) {
            return redirect()->route('profile.edit')
                ->with('error', 'Please Fill Branch Information to access results.');
        }

        $activeSession = Session::active()->first();
        $studentId = $request->get('student_id');

        if (!$activeSession) {
            return redirect()->route('students.result')->with('error', 'Please activate a session before creating results.');
        }

        if (!$studentId) {
            return redirect()->route('students.result')->with('error', 'Please select a student from the result list.');
        }

        $student = Student::where('id', $studentId)
            ->where('session_id', $activeSession->id)
            ->firstOrFail();

        $existingResult = StudentResult::where('student_id', $student->id)
            ->where('session_id', $activeSession->id)
            ->latest()
            ->first();

        if ($existingResult) {
            return redirect()->route('results.edit', $existingResult->id);
        }

        $context = $this->buildResultFormContext($student, null, $user);

        if (!$context) {
            abort(403, 'You are not allowed to create result for this student.');
        }

        return view('results.create2', $context);
    }

    public function resultClasses(Request $request)
    {
        $request->validate(['branch_id' => 'required']);

        return response()->json($this->resultClassesFor(auth()->user(), $request->branch_id));
    }

    public function resultSections(Request $request)
    {
        $request->validate([
            'branch_id' => 'required',
            'class_id' => 'required|integer',
        ]);

        return response()->json($this->resultSectionsFor(auth()->user(), $request->branch_id, $request->class_id));
    }

    public function resultStudents(Request $request)
    {
        $request->validate([
            'branch_id' => 'required',
            'class_id' => 'required|integer',
            'section_id' => 'required|integer',
        ]);

        $activeSession = Session::active()->first();

        if (!$activeSession) {
            return response()->json([]);
        }

        $class = Classes::where('id', $request->class_id)
            ->where('session_id', $activeSession->id)
            ->first();
        $section = Section::find($request->section_id);

        if (!$class || !$section) {
            return response()->json([]);
        }

        if (!$this->canAccessResultSection(auth()->user(), $request->branch_id, $class->id, $section->id)) {
            abort(403);
        }

        $studentsQuery = Student::where('session_id', $activeSession->id)
            ->where('owned_by', $request->branch_id)
            ->where('erp_class_id', $class->erp_class_id)
            ->where('erp_section_id', $section->erp_section_id);

        return response()->json(
            $studentsQuery
                ->select('id', 'erp_student_id', 'rollno', 'stdname', 'fathername', 'phone_no', 'erp_class_id', 'erp_section_id')
                ->orderBy('stdname')
                ->get()
        );
    }

    public function resultSubjects(Request $request)
    {
        $request->validate([
            'branch_id' => 'required',
            'class_id' => 'required|integer',
            'section_id' => 'required|integer',
        ]);

        return response()->json($this->resultSubjectsFor(auth()->user(), $request->branch_id, $request->class_id, $request->section_id));
    }

    public function forward(Request $request, $id)
    {
        $request->validate([
            'action' => 'required|in:forward_class_teacher,forward_coordinator',
        ]);

        $result = StudentResult::findOrFail($id);
        $user = auth()->user();

        if (!$this->canAccessResultSection($user, $result->branch_id, $result->class_id, $result->section_id)) {
            abort(403, 'Unauthorized access');
        }

        if ($request->action === 'forward_coordinator') {
            $missingForwardFields = $this->missingCoordinatorForwardResultFields($result);

            if ($missingForwardFields) {
                return back()->with(
                    'error',
                    'Please fill these fields before forwarding to Coordinator: ' . implode(', ', $missingForwardFields) . '.'
                );
            }

            $missingSubjects = $this->missingStoredFinalizeSubjects($result);

            if ($missingSubjects) {
                return back()->with(
                    'error',
                    'Please enter Term One marks for all subjects before forwarding to Coordinator. Missing: ' . implode(', ', $missingSubjects) . '.'
                );
            }
        }

        if (!$this->applyForwardAction($result, $user, $request->action)) {
            return back()->with('error', 'This result cannot be forwarded from your current role or status.');
        }

        return back()->with('success', 'Result forwarded successfully.');
    }

    public function coordinatorApprove($id)
    {
        $result = StudentResult::findOrFail($id);
        $user = auth()->user();

        if (
            (!$user->hasRole('Admin') && !$user->hasRole('Coordinator'))
            || !$this->canAccessResultSection($user, $result->branch_id, $result->class_id, $result->section_id)
        ) {
            abort(403, 'Unauthorized access');
        }

        $missingForwardFields = $this->missingCoordinatorForwardResultFields($result);

        if ($missingForwardFields) {
            return back()->with(
                'error',
                'Please fill these fields before approving: ' . implode(', ', $missingForwardFields) . '.'
            );
        }

        if (!$this->approveCoordinatorResult($result, $user)) {
            return back()->with('error', 'This result cannot be approved from your current role or status.');
        }

        return back()->with('success', 'Result approved successfully.');
    }

    public function bulkForward(Request $request)
    {
        $request->validate([
            'result_ids' => 'required|array',
            'result_ids.*' => 'integer',
            'action' => 'required|in:forward_class_teacher,forward_coordinator',
        ]);

        $user = auth()->user();
        $forwarded = 0;
        $blockedMissingFields = 0;
        $blockedMissingSubjects = 0;

        StudentResult::whereIn('id', $request->result_ids)->get()->each(function ($result) use ($user, $request, &$forwarded, &$blockedMissingFields, &$blockedMissingSubjects) {
            if (!$this->canAccessResultSection($user, $result->branch_id, $result->class_id, $result->section_id)) {
                return;
            }

            if ($request->action === 'forward_coordinator' && $this->missingCoordinatorForwardResultFields($result)) {
                $blockedMissingFields++;
                return;
            }

            if ($request->action === 'forward_coordinator' && $this->missingStoredFinalizeSubjects($result)) {
                $blockedMissingSubjects++;
                return;
            }

            if ($this->applyForwardAction($result, $user, $request->action)) {
                $forwarded++;
            }
        });

        if ($request->action === 'forward_coordinator' && $blockedMissingFields > 0) {
            return back()->with('error', $blockedMissingFields . ' selected result(s) need Promoted To, Term One Working Days, Term Two Working Days, and Remarks before forwarding to Coordinator.');
        }

        if ($request->action === 'forward_coordinator' && $blockedMissingSubjects > 0) {
            return back()->with('error', $blockedMissingSubjects . ' selected result(s) need Term One marks for all subjects before forwarding to Coordinator.');
        }

        return back()->with($forwarded ? 'success' : 'error', $forwarded
            ? $forwarded . ' result(s) forwarded successfully.'
            : 'No selected results could be forwarded.');
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $user = auth()->user();
            $activeSession = Session::active()->first();

            if (!$activeSession) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please activate a session before generating results.',
                ], 422);
            }

            $student = Student::where('id', $request->student_id)
                ->where('session_id', $activeSession->id)
                ->first();

            if (!$student) {
                return response()->json([
                    'success' => false,
                    'message' => 'Selected student was not found in the active session.',
                ], 422);
            }

            [$class, $section] = $this->resolveStudentClassAndSection($student, $request->class_id, $request->section_id);

            if (!$class || !$section || !$this->canAccessResultSection($user, $student->owned_by, $class->id, $section->id)) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are not allowed to create result for this student.',
                ], 422);
            }

            $resultexist = StudentResult::where('student_id', $student->id)
                ->where('session_id', $activeSession->id)
                ->first();

            if ($resultexist) {
                return response()->json([
                    'success' => false,
                    'message' => 'Student Result already been generated.Edit the result.',
                ], 422);
            }

            $allowedSubjectIds = $this->resultSubjectsFor($user, $student->owned_by, $class->id, $section->id)
                ->pluck('id')
                ->map(fn($id) => (int) $id)
                ->values();
            $submittedSubjectIds = $this->submittedSubjectIds($request->subjects ?? []);

            if ($submittedSubjectIds->diff($allowedSubjectIds)->isNotEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'One or more selected subjects are not allowed for your role.',
                ], 422);
            }

            $marksToSave = $this->prepareSubmittedMarks($request->subjects ?? [], $allowedSubjectIds, $activeSession->id);

            if ($marksToSave->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please add at least one subject with marks.',
                ], 422);
            }

            $canEditDetails = $this->canEditResultDetails($user, $student->owned_by, $class->id, $section->id);
            $submitAction = $request->input('submit_action', 'save');

            if ($submitAction === 'forward_coordinator') {
                $missingSubjects = $this->missingSubmittedFinalizeSubjects(
                    $request,
                    $this->finalizationSubjectsFor($activeSession->id, $class->id)
                );

                if ($missingSubjects) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Please enter Term One marks for all subjects before forwarding to Coordinator. Missing: ' . implode(', ', $missingSubjects) . '.',
                    ], 422);
                }
            }

            $t1Working = $canEditDetails ? floatval($request->input('working_days.term_one', 0)) : 0;
            $t2Working = $canEditDetails ? floatval($request->input('working_days.term_two', 0)) : 0;

            $studentResult = StudentResult::create([
                'student_id' => $student->id,
                'name' => $student->stdname,
                'class' => $class->name,
                'section' => $section->name,
                'rollno' => $student->rollno,
                'session_id' => $activeSession->id,
                'erp_session_id' => $activeSession->erp_session_id ?: (string) $activeSession->id,
                'branch_id' => $student->owned_by,
                'class_id' => $class->id,
                'section_id' => $section->id,
                'erp_student_id' => $student->erp_student_id,
                'erp_class_id' => $student->erp_class_id,
                'erp_section_id' => $student->erp_section_id,
                'attendance' => $t1Working + $t2Working,
                't1_working_days' => $t1Working,
                't2_working_days' => $t2Working,
                'grand_term_one' => 0,
                'grand_term_two' => 0,
                'remarks' => $canEditDetails ? ($request->remarks ?? null) : null,
                'promoted_class' => $canEditDetails ? ($request->promoted_class ?? null) : null,
                'created_by' => auth()->id(),
                'workflow_status' => 'draft',
            ]);

            $this->saveSubmittedMarks($studentResult, $marksToSave);
            $this->recalculateStudentResult($studentResult);

            if (in_array($submitAction, ['forward_class_teacher', 'forward_coordinator'], true)) {
                if (!$this->applyForwardAction($studentResult, $user, $submitAction)) {
                    DB::rollBack();

                    return response()->json([
                        'success' => false,
                        'message' => $this->forwardFailureMessage($submitAction),
                    ], 422);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Student result generated successfully',
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error($e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();

        try {
            $studentResult = StudentResult::with('student')->findOrFail($id);
            $user = auth()->user();
            $student = $studentResult->student;

            if (!$student || !$this->canAccessResultSection($user, $studentResult->branch_id, $studentResult->class_id, $studentResult->section_id)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access',
                ], 403);
            }

            if (!$this->canEditResult($user, $studentResult)) {
                return response()->json([
                    'success' => false,
                    'message' => 'This result has already been forwarded and is locked for your role.',
                ], 403);
            }

            $allowedSubjectIds = $this->resultSubjectsFor($user, $studentResult->branch_id, $studentResult->class_id, $studentResult->section_id)
                ->pluck('id')
                ->map(fn($subjectId) => (int) $subjectId)
                ->values();
            $submittedSubjectIds = $this->submittedSubjectIds($request->subjects ?? []);

            if ($submittedSubjectIds->diff($allowedSubjectIds)->isNotEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'One or more selected subjects are not allowed for your role.',
                ], 422);
            }

            $marksToSave = $this->prepareSubmittedMarks($request->subjects ?? [], $allowedSubjectIds, $studentResult->session_id);

            if ($marksToSave->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please add at least one subject with marks.',
                ], 422);
            }

            $submitAction = $request->input('submit_action', 'save');

            if (in_array($submitAction, ['forward_coordinator', 'coordinator_approve'], true)) {
                $missingForwardFields = $this->missingCoordinatorForwardFields($request);

                if ($missingForwardFields) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Please fill these fields before ' . ($submitAction === 'coordinator_approve' ? 'approving' : 'forwarding to Coordinator') . ': ' . implode(', ', $missingForwardFields) . '.',
                    ], 422);
                }
            }

            if ($submitAction === 'forward_coordinator') {
                $missingSubjects = $this->missingSubmittedFinalizeSubjects(
                    $request,
                    $this->finalizationSubjectsFor($studentResult->session_id, $studentResult->class_id)
                );

                if ($missingSubjects) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Please enter Term One marks for all subjects before forwarding to Coordinator. Missing: ' . implode(', ', $missingSubjects) . '.',
                    ], 422);
                }
            }

            if ($this->canEditResultDetails($user, $studentResult->branch_id, $studentResult->class_id, $studentResult->section_id)) {
                $t1Working = floatval($request->input('working_days.term_one', 0));
                $t2Working = floatval($request->input('working_days.term_two', 0));

                $studentResult->update([
                    'attendance' => $t1Working + $t2Working,
                    't1_working_days' => $t1Working,
                    't2_working_days' => $t2Working,
                    'remarks' => $request->remarks ?? null,
                    'promoted_class' => $request->promoted_class ?? null,
                ]);
            }

            $this->saveSubmittedMarks($studentResult, $marksToSave);
            $this->recalculateStudentResult($studentResult);
            $studentResult->forceFill(['edit_by' => $user->id])->save();

            if (in_array($submitAction, ['forward_class_teacher', 'forward_coordinator', 'coordinator_approve'], true)) {
                $workflowUpdated = $submitAction === 'coordinator_approve'
                    ? $this->approveCoordinatorResult($studentResult, $user)
                    : $this->applyForwardAction($studentResult, $user, $submitAction);

                if (!$workflowUpdated) {
                    DB::rollBack();

                    return response()->json([
                        'success' => false,
                        'message' => $submitAction === 'coordinator_approve'
                            ? 'Result was saved, but could not be approved. Only Coordinator can approve a result forwarded to Coordinator.'
                            : $this->forwardFailureMessage($submitAction),
                    ], 422);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Student result updated successfully',
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function destroy($id)
    {
        $student = StudentResult::findOrFail($id);
        $user = auth()->user();

        if (!$user->hasRole('Admin')) {
            abort(403, 'Unauthorized access');
        }

        if ($student->workflow_status === 'coordinator_approved') {
            abort(403, 'Approved results cannot be deleted.');
        }

        StudentMarks::where('result_id', $student->id)->delete();
        $student->delete();

        return redirect()->back();
    }

    public function show($id)
    {
        $student = StudentResult::with('student', 'marks.subject', 'session')->findOrFail($id);
        $user = auth()->user();

        if (!$user->hasRole('Admin') && !$user->hasRole('Coordinator')) {
            abort(403, 'Unauthorized access');
        }

        if ($user->hasRole('Coordinator') && !$this->canAccessResultSection($user, $student->branch_id, $student->class_id, $student->section_id)) {
            abort(403, 'Unauthorized access');
        }

        return view('results.final_result_card', $this->resultCardViewData($student));
    }

    public function publicResult(string $encodedId)
    {
        $resultId = $this->decodeResultId($encodedId);

        if (!$resultId) {
            abort(404);
        }

        $student = StudentResult::with('student', 'marks.subject', 'session')->findOrFail($resultId);

        return view('results.public_result_card', $this->resultCardViewData($student));
    }

    private function resultCardViewData(StudentResult $student): array
    {
        $creator = User::findOrFail($student->created_by);
        $branch = Branch::where('erp_branch_id', $student->branch_id)->first();
        $subjects = ClassSubject::where('class_subjects.class_id', $student->class_id)
            ->when($student->session_id, fn($query) => $query->where('class_subjects.session_id', $student->session_id))
            ->join('subject_wise_marks', 'class_subjects.subject_id', '=', 'subject_wise_marks.id')
            ->select(
                'subject_wise_marks.id',
                'subject_wise_marks.subject_name',
                'subject_wise_marks.term_one_marks',
                'subject_wise_marks.term_two_marks'
            )
            ->orderBy('subject_wise_marks.subject_name')
            ->get();

        if ($subjects->isEmpty()) {
            $subjects = $student->marks
                ->pluck('subject')
                ->filter()
                ->unique('id')
                ->sortBy('subject_name')
                ->values();
        }

        $highestPercentages = $this->highestClassPercentagesForReport($student, $subjects);
        $classTeacher = $this->classTeacherForReport($student, $subjects);
        $classTeacherName = $classTeacher?->name ?? '';
        $encodedResultId = $this->encodeResultId($student->id);
        $publicResultUrl = route('public.result', $encodedResultId);

        return compact(
            'student',
            'creator',
            'branch',
            'subjects',
            'classTeacherName',
            'highestPercentages',
            'encodedResultId',
            'publicResultUrl'
        );
    }

    public function edit($id)
    {
        $student = StudentResult::with('student', 'marks.subject', 'session')->findOrFail($id);
        $user = auth()->user();

        if (!$student->student || !$this->canAccessResultSection($user, $student->branch_id, $student->class_id, $student->section_id)) {
            abort(403, 'Unauthorized access');
        }

        if (!$this->canEditResult($user, $student)) {
            abort(403, 'This result has already been forwarded and is locked for your role.');
        }

        $context = $this->buildResultFormContext($student->student, $student, $user);

        return view('results.create2', $context);
    }

    public function search(Request $request)
    {
        $user = auth()->user();

        $students = $this->accessibleStudentQuery($user, [
            'branch_id' => $request->branch_id,
            'class_id' => $request->class_id,
            'section_id' => $request->section_id,
            'search' => $request->search,
        ])
            ->with('result.session')
            ->orderBy('stdname')
            ->limit(50)
            ->get();

        return response()->json($students);
    }

    private function calculatePercentage($obtained, $total)
    {
        return $total == 0 ? 0 : ($obtained / $total) * 100;
    }

    private function calculateGrade($percent)
    {
        if ($percent >= 90) return 'A+';
        if ($percent >= 80) return 'A';
        if ($percent >= 70) return 'B';
        if ($percent >= 60) return 'C';
        if ($percent >= 50) return 'D';

        return 'F';
    }

    private function generateRemarks($grade)
    {
        return [
            'A+' => 'Outstanding',
            'A'  => 'Excellent',
            'B'  => 'Very Good',
            'C'  => 'Good',
            'D'  => 'Satisfactory',
            'F'  => 'Needs Improvement',
        ][$grade] ?? 'N/A';
    }

    private function resultBranches(User $user)
    {
        $activeSession = Session::active()->first();

        if ($user->hasRole('Admin')) {
            return Branch::orderBy('name')->get()->map(fn($branch) => [
                'id' => (string) $branch->erp_branch_id,
                'name' => $branch->name,
            ])->values();
        }

        if ($user->hasRole('Coordinator')) {
            return Branch::where('erp_branch_id', $user->branch_id)
                ->orderBy('name')
                ->get()
                ->map(fn($branch) => [
                    'id' => (string) $branch->erp_branch_id,
                    'name' => $branch->name,
                ])
                ->values();
        }

        return TeacherSubjectAssignment::query()
            ->where('teacher_subject_assignments.teacher_id', $user->id)
            ->when($activeSession, fn($query) => $query->where('teacher_subject_assignments.session_id', $activeSession->id))
            ->join('classes', 'teacher_subject_assignments.class_id', '=', 'classes.id')
            ->select('classes.erp_branch_id as branch_id')
            ->groupBy('classes.erp_branch_id')
            ->orderBy('classes.erp_branch_id')
            ->get()
            ->map(function ($assignment) {
                $branch = Branch::where('erp_branch_id', $assignment->branch_id)->first();

                return [
                    'id' => (string) $assignment->branch_id,
                    'name' => $branch?->name ?? ('Branch #' . $assignment->branch_id),
                ];
            })
            ->values();
    }

    private function resultClassesFor(User $user, $branchId)
    {
        $activeSession = Session::active()->first();

        if (!$activeSession || !$this->canAccessResultBranch($user, $branchId)) {
            return collect();
        }

        if ($user->hasRole('Teacher') && !$user->hasRole('Admin') && !$user->hasRole('Coordinator')) {
            return TeacherSubjectAssignment::where('teacher_subject_assignments.teacher_id', $user->id)
                ->join('classes', 'teacher_subject_assignments.class_id', '=', 'classes.id')
                ->where('teacher_subject_assignments.session_id', $activeSession->id)
                ->where('classes.erp_branch_id', $branchId)
                ->select('classes.id', 'classes.name')
                ->distinct()
                ->orderBy('classes.name')
                ->get()
                ->values();
        }

        return Classes::where('session_id', $activeSession->id)
            ->where('erp_branch_id', $branchId)
            ->select('id', 'name')
            ->orderBy('name')
            ->get();
    }

    private function resultSectionsFor(User $user, $branchId, $classId)
    {
        $activeSession = Session::active()->first();

        if (!$activeSession || !$this->canAccessResultClass($user, $branchId, $classId)) {
            return collect();
        }

        if ($user->hasRole('Teacher') && !$user->hasRole('Admin') && !$user->hasRole('Coordinator')) {
            return TeacherSubjectAssignment::where('teacher_id', $user->id)
                ->where('session_id', $activeSession->id)
                ->where('class_id', $classId)
                ->select('section_id', DB::raw('MAX(section_name) as section_name'))
                ->groupBy('section_id')
                ->orderBy('section_name')
                ->get()
                ->map(fn($row) => [
                    'id' => (int) $row->section_id,
                    'name' => $row->section_name ?: (Section::find($row->section_id)?->name ?? 'Section #' . $row->section_id),
                ])
                ->values();
        }

        return ClassSection::where('classsections.session_id', $activeSession->id)
            ->where('classsections.class_id', $classId)
            ->join('sections', 'classsections.section_id', '=', 'sections.id')
            ->select('sections.id', 'sections.name')
            ->orderBy('sections.name')
            ->get();
    }

    private function resultSubjectsFor(User $user, $branchId, $classId, $sectionId)
    {
        $activeSession = Session::active()->first();

        if (!$activeSession || !$this->canAccessResultSection($user, $branchId, $classId, $sectionId)) {
            return collect();
        }

        if ($user->hasRole('Teacher') && !$user->hasRole('Admin') && !$user->hasRole('Coordinator')) {
            return TeacherSubjectAssignment::where('teacher_subject_assignments.teacher_id', $user->id)
                ->where('teacher_subject_assignments.session_id', $activeSession->id)
                ->where('teacher_subject_assignments.class_id', $classId)
                ->where('teacher_subject_assignments.section_id', $sectionId)
                ->join('subject_wise_marks', 'teacher_subject_assignments.subject_id', '=', 'subject_wise_marks.id')
                ->select('subject_wise_marks.id', 'subject_wise_marks.subject_name', 'subject_wise_marks.term_one_marks', 'subject_wise_marks.term_two_marks')
                ->distinct()
                ->orderBy('subject_wise_marks.subject_name')
                ->get();
        }

        return ClassSubject::where('class_subjects.session_id', $activeSession->id)
            ->where('class_subjects.class_id', $classId)
            ->join('subject_wise_marks', 'class_subjects.subject_id', '=', 'subject_wise_marks.id')
            ->select('subject_wise_marks.id', 'subject_wise_marks.subject_name', 'subject_wise_marks.term_one_marks', 'subject_wise_marks.term_two_marks')
            ->distinct()
            ->orderBy('subject_wise_marks.subject_name')
            ->get();
    }

    private function canAccessResultBranch(User $user, $branchId): bool
    {
        if ($user->hasRole('Admin')) {
            return true;
        }

        if ($user->hasRole('Coordinator')) {
            return (string) $user->branch_id === (string) $branchId;
        }

        $activeSession = Session::active()->first();

        return TeacherSubjectAssignment::where('teacher_id', $user->id)
            ->join('classes', 'teacher_subject_assignments.class_id', '=', 'classes.id')
            ->when($activeSession, fn($query) => $query->where('teacher_subject_assignments.session_id', $activeSession->id))
            ->where('classes.erp_branch_id', $branchId)
            ->exists();
    }

    private function canAccessResultClass(User $user, $branchId, $classId): bool
    {
        if (!$this->canAccessResultBranch($user, $branchId)) {
            return false;
        }

        if ($user->hasRole('Admin') || $user->hasRole('Coordinator')) {
            $activeSession = Session::active()->first();

            return Classes::where('id', $classId)
                ->when($activeSession, fn($query) => $query->where('session_id', $activeSession->id))
                ->where('erp_branch_id', $branchId)
                ->exists();
        }

        $activeSession = Session::active()->first();

        return TeacherSubjectAssignment::where('teacher_id', $user->id)
            ->when($activeSession, fn($query) => $query->where('session_id', $activeSession->id))
            ->where('class_id', $classId)
            ->exists();
    }

    private function canAccessResultSection(User $user, $branchId, $classId, $sectionId): bool
    {
        if (!$this->canAccessResultClass($user, $branchId, $classId)) {
            return false;
        }

        if ($user->hasRole('Admin') || $user->hasRole('Coordinator')) {
            $activeSession = Session::active()->first();

            return ClassSection::where('session_id', $activeSession?->id)
                ->where('class_id', $classId)
                ->where('section_id', $sectionId)
                ->exists();
        }

        $activeSession = Session::active()->first();

        return TeacherSubjectAssignment::where('teacher_id', $user->id)
            ->when($activeSession, fn($query) => $query->where('session_id', $activeSession->id))
            ->where('class_id', $classId)
            ->where('section_id', $sectionId)
            ->exists();
    }

    private function applyResultStatusFilter($query, ?string $status, ?Session $activeSession)
    {
        if (!$status || $status === 'all' || !$activeSession) {
            return $query;
        }

        if ($status === 'pending') {
            return $query->whereNotIn('students.id', StudentResult::select('student_id')
                ->where('session_id', $activeSession->id));
        }

        $statuses = match ($status) {
            'forwarded' => ['forwarded_to_class_teacher', 'forwarded_to_coordinator'],
            'approved' => ['coordinator_approved'],
            default => [$status],
        };

        return $query->whereIn('students.id', StudentResult::select('student_id')
            ->where('session_id', $activeSession->id)
            ->whereIn('workflow_status', $statuses));
    }

    private function accessibleStudentQuery(User $user, array $filters = [])
    {
        $activeSession = Session::active()->first();
        $query = Student::query();
        if (!$activeSession) {
            return $query->whereRaw('1 = 0');
        }

        $query->where('students.session_id', $activeSession->id);

        if ($user->hasRole('Coordinator') && !$user->hasRole('Admin')) {
            $query->where('students.owned_by', $user->branch_id);
        } elseif ($user->hasRole('Teacher') && !$user->hasRole('Admin') && !$user->hasRole('Coordinator')) {
            $scopes = $this->teacherStudentScopes($user);

            if ($scopes->isEmpty()) {
                $query->whereRaw('1 = 0');
            } else {
                $query->where(function ($scopeQuery) use ($scopes) {
                    foreach ($scopes as $scope) {
                        $scopeQuery->orWhere(function ($rowQuery) use ($scope) {
                            $rowQuery->where('students.owned_by', $scope['branch_id'])
                                ->where('students.erp_class_id', $scope['erp_class_id']);

                            $rowQuery->where('students.erp_section_id', $scope['erp_section_id']);
                        });
                    }
                });
            }
        }
                            // dd($query->get(),$scopes);

        if (!empty($filters['branch_id'])) {
            $query->where('students.owned_by', $filters['branch_id']);
        }

        if (!empty($filters['class_id'])) {
            $class = Classes::where('id', $filters['class_id'])
                ->where('session_id', $activeSession->id)
                ->first();

            $query->when($class, fn($q) => $q->where('students.erp_class_id', $class->erp_class_id));
        }

        if (!empty($filters['section_id'])) {
            $section = Section::where('id', $filters['section_id'])
                ->where('session_id', $activeSession->id)
                ->first();

            $query->where('students.erp_section_id', $section?->erp_section_id);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($searchQuery) use ($search) {
                $searchQuery->where('students.stdname', 'LIKE', "%{$search}%")
                    ->orWhere('students.rollno', 'LIKE', "%{$search}%")
                    ->orWhere('students.fathername', 'LIKE', "%{$search}%")
                    ->orWhere('students.phone_no', 'LIKE', "%{$search}%");
            });
        }

        return $query;
    }

    private function teacherStudentScopes(User $user)
    {
        $activeSession = Session::active()->first();

        if (!$activeSession) {
            return collect();
        }

        $assignments = TeacherSubjectAssignment::where('teacher_id', $user->id)
            ->where('session_id', $activeSession->id)
            ->select('branch_id', 'class_id', 'section_id')
            ->distinct()
            ->get();

        $classes = Classes::whereIn('id', $assignments->pluck('class_id')->filter()->unique())
            ->get()
            ->keyBy('id');
        $sections = Section::whereIn('id', $assignments->pluck('section_id')->filter()->unique())
            ->get()
            ->keyBy('id');

        return $assignments
            ->map(function ($assignment) use ($classes, $sections) {
                $class = $classes->get($assignment->class_id);
                $section = $sections->get($assignment->section_id);

                if (!$class || !$section?->erp_section_id) {
                    return null;
                }

                return [
                    'branch_id' => (string) $class->erp_branch_id,
                    'class_id' => (int) $assignment->class_id,
                    'section_id' => (int) $assignment->section_id,
                    'erp_class_id' => (string) $class->erp_class_id,
                    'erp_section_id' => $section?->erp_section_id,
                ];
            })
            ->filter()
            ->values();
    }

    private function decorateStudentRows($students, User $user): void
    {
        $activeSession = Session::active()->first();
        $collection = $students->getCollection();

        $branchMap = Branch::whereIn('erp_branch_id', $collection->pluck('owned_by')->filter()->unique())
            ->pluck('name', 'erp_branch_id');
        $classMap = Classes::where('session_id', $activeSession?->id)
            ->whereIn('erp_class_id', $collection->pluck('erp_class_id')->filter()->unique())
            ->get()
            ->keyBy(fn($class) => (string) $class->erp_class_id);
        $sectionMap = Section::where('session_id', $activeSession?->id)
            ->whereIn('erp_section_id', $collection->pluck('erp_section_id')->filter()->unique())
            ->get()
            ->keyBy(fn($section) => (string) $section->erp_section_id);

        $students->setCollection($collection->map(function ($student) use ($branchMap, $classMap, $sectionMap, $user) {
            $class = $classMap->get((string) $student->erp_class_id);
            $section = $student->erp_section_id ? $sectionMap->get((string) $student->erp_section_id) : null;
            $isClassTeacher = $student->result && $class && $section
                ? $this->isClassTeacherForSection($user, $student->owned_by, $class->id, $section->id)
                : false;

            $student->branch_name = $branchMap[$student->owned_by] ?? ('Branch #' . $student->owned_by);
            $student->class_name = $class?->name ?? ('Class #' . $student->erp_class_id);
            $student->section_display = $section?->name ?? $student->section_name ?? 'N/A';
            $student->local_class_id = $class?->id;
            $student->local_section_id = $section?->id;
            $isTeacherOnly = $user->hasRole('Teacher') && !$user->hasRole('Admin') && !$user->hasRole('Coordinator');
            $student->can_forward_to_class_teacher = $isTeacherOnly
                && $student->result
                && !$isClassTeacher
                && $student->result->workflow_status === 'draft';
            $student->can_forward_to_coordinator = $isTeacherOnly
                && $student->result
                && $isClassTeacher
                && in_array($student->result->workflow_status, ['draft', 'forwarded_to_class_teacher'], true);
            $student->can_edit_result = $student->result
                ? $this->canEditResult($user, $student->result)
                : true;
            $student->can_delete_result = $student->result
                && $student->result->workflow_status !== 'coordinator_approved'
                && $user->hasRole('Admin');
            $student->can_approve_result = $student->result
                && ($user->hasRole('Admin') || $user->hasRole('Coordinator'))
                && $student->result->workflow_status !== 'coordinator_approved'
                && $this->canAccessResultSection($user, $student->result->branch_id, $student->result->class_id, $student->result->section_id);
            $student->workflow_status_label = $this->workflowStatusLabel($student->result);

            return $student;
        }));
    }

    private function decorateResultRows($results, User $user): void
    {
        $collection = $results->getCollection();

        $branchMap = Branch::whereIn('erp_branch_id', $collection->pluck('branch_id')->filter()->unique())
            ->pluck('name', 'erp_branch_id');
        $classMap = Classes::whereIn('id', $collection->pluck('class_id')->filter()->unique())
            ->pluck('name', 'id');
        $sectionMap = Section::whereIn('id', $collection->pluck('section_id')->filter()->unique())
            ->pluck('name', 'id');

        $results->setCollection($collection->map(function ($result) use ($branchMap, $classMap, $sectionMap, $user) {
            $result->branch_name = $branchMap[$result->branch_id] ?? ('Branch #' . $result->branch_id);
            $result->class_name = $classMap[$result->class_id] ?? ($result->class ?: 'N/A');
            $result->section_display = $sectionMap[$result->section_id] ?? ($result->section ?: 'N/A');
            $result->workflow_status_label = $this->workflowStatusLabel($result);
            $result->can_approve_result = ($user->hasRole('Admin') || $user->hasRole('Coordinator'))
                && $result->workflow_status !== 'coordinator_approved'
                && $this->canAccessResultSection($user, $result->branch_id, $result->class_id, $result->section_id);
            $result->can_edit_result = $this->canEditResult($user, $result);
            $result->can_delete_result = $result->workflow_status !== 'coordinator_approved'
                && $user->hasRole('Admin');

            return $result;
        }));
    }

    private function resolveStudentClassAndSection(Student $student, $classId = null, $sectionId = null): array
    {
        $class = $classId
            ? Classes::where('id', $classId)->where('session_id', $student->session_id)->first()
            : Classes::where('session_id', $student->session_id)->where('erp_class_id', $student->erp_class_id)->first();

        $section = $sectionId
            ? Section::where('id', $sectionId)->where('session_id', $student->session_id)->first()
            : ($student->erp_section_id
                ? Section::where('session_id', $student->session_id)->where('erp_section_id', $student->erp_section_id)->first()
                : null);

        return [$class, $section];
    }

    private function buildResultFormContext(Student $student, ?StudentResult $result, User $user): ?array
    {
        [$class, $section] = $this->resolveStudentClassAndSection($student, $result?->class_id, $result?->section_id);

        if (!$class || !$section || !$this->canAccessResultSection($user, $student->owned_by, $class->id, $section->id)) {
            return null;
        }

        $subjects = $this->resultSubjectsFor($user, $student->owned_by, $class->id, $section->id);
        $marks = $result
            ? $result->marks()->get()->keyBy('subject_id')
            : collect();
        $isClassTeacher = $this->isClassTeacherForSection($user, $student->owned_by, $class->id, $section->id);
        $isTeacherOnly = $user->hasRole('Teacher') && !$user->hasRole('Admin') && !$user->hasRole('Coordinator');
        $canEditForm = !$result || $this->canEditResult($user, $result);
        $canEditDetails = $canEditForm && $this->canEditResultDetails($user, $student->owned_by, $class->id, $section->id);

        $subjectRows = $subjects->map(function ($subject) use ($marks) {
            $mark = $marks->get($subject->id);

            return (object) [
                'id' => $subject->id,
                'subject_name' => $subject->subject_name,
                'term_one_total' => $subject->term_one_marks ?? 100,
                'term_two_total' => $subject->term_two_marks ?? 100,
                'term_one_mark' => $mark?->term_one_mark,
                'term_two_mark' => $mark?->term_two_mark,
            ];
        });

        return [
            'mode' => $result ? 'edit' : 'create',
            'syncedStudent' => $student,
            'studentResult' => $result,
            'activeSession' => Session::active()->first(),
            'branchName' => Branch::where('erp_branch_id', $student->owned_by)->value('name') ?? ('Branch #' . $student->owned_by),
            'classRecord' => $class,
            'sectionRecord' => $section,
            'subjectRows' => $subjectRows,
            'canEditDetails' => $canEditDetails,
            'canEditForm' => $canEditForm,
            'canForwardToClassTeacher' => $isTeacherOnly && $canEditForm && !$isClassTeacher && (!$result || $result->workflow_status === 'draft'),
            'canForwardToCoordinator' => $isTeacherOnly && $canEditForm && $isClassTeacher && (!$result || in_array($result->workflow_status, ['draft', 'forwarded_to_class_teacher'], true)),
            'canCoordinatorApprove' => $result
                && ($user->hasRole('Admin') || $user->hasRole('Coordinator'))
                && $result->workflow_status !== 'coordinator_approved'
                && $this->canAccessResultSection($user, $result->branch_id, $result->class_id, $result->section_id),
            'workflowStatusLabel' => $this->workflowStatusLabel($result),
            'formAction' => $result ? route('results.update', $result->id) : route('student_result.store'),
        ];
    }

    private function canEditResult(User $user, StudentResult $result): bool
    {
        if ($result->workflow_status === 'coordinator_approved') {
            return $user->hasRole('Admin');
        }

        if ($user->hasRole('Admin') || $user->hasRole('Coordinator')) {
            return true;
        }

        $isClassTeacher = $this->isClassTeacherForSection($user, $result->branch_id, $result->class_id, $result->section_id);

        if ($isClassTeacher) {
            return $result->workflow_status !== 'forwarded_to_coordinator';
        }

        return $result->workflow_status === 'draft';
    }

    private function applyForwardAction(StudentResult $result, User $user, string $action): bool
    {
        if (!$user->hasRole('Teacher') || $user->hasRole('Admin') || $user->hasRole('Coordinator')) {
            return false;
        }

        $isClassTeacher = $this->isClassTeacherForSection($user, $result->branch_id, $result->class_id, $result->section_id);

        if ($action === 'forward_class_teacher') {
            if ($isClassTeacher || $result->workflow_status !== 'draft') {
                return false;
            }

            $result->update([
                'workflow_status' => 'forwarded_to_class_teacher',
                'subject_finalized_by' => $user->id,
                'subject_finalized_at' => now(),
                'edit_by' => $user->id,
            ]);

            return true;
        }

        if ($action === 'forward_coordinator') {
            if (!$isClassTeacher || !in_array($result->workflow_status, ['draft', 'forwarded_to_class_teacher'], true)) {
                return false;
            }

            if ($this->missingStoredFinalizeSubjects($result)) {
                return false;
            }

            $result->update([
                'workflow_status' => 'forwarded_to_coordinator',
                'class_teacher_finalized_by' => $user->id,
                'class_teacher_finalized_at' => now(),
                'edit_by' => $user->id,
            ]);

            return true;
        }

        return false;
    }

    private function approveCoordinatorResult(StudentResult $result, User $user): bool
    {
        if (!$user->hasRole('Admin') && !$user->hasRole('Coordinator')) {
            return false;
        }

        if ($result->workflow_status === 'coordinator_approved') {
            return false;
        }

        if (!$this->canAccessResultSection($user, $result->branch_id, $result->class_id, $result->section_id)) {
            return false;
        }

        $this->recalculateStudentResult($result);

        $result->update([
            'workflow_status' => 'coordinator_approved',
            'coordinator_approved_at' => now(),
            'edit_by' => $user->id,
        ]);

        return true;
    }

    private function workflowStatusLabel(?StudentResult $result): array
    {
        return match ($result?->workflow_status) {
            'forwarded_to_class_teacher' => ['text' => 'Forwarded to Class Teacher', 'class' => 'bg-info'],
            'forwarded_to_coordinator' => ['text' => 'Forwarded to Coordinator', 'class' => 'bg-primary'],
            'coordinator_approved' => ['text' => 'Coordinator Approved', 'class' => 'bg-success'],
            default => ['text' => $result ? 'Draft' : 'Not Created', 'class' => $result ? 'bg-secondary' : 'bg-warning text-dark'],
        };
    }

    private function forwardFailureMessage(string $action): string
    {
        return $action === 'forward_coordinator'
            ? 'Result was saved, but could not be forwarded. Only the all-subject class teacher can forward this result to coordinator, and it must not already be finalized.'
            : 'Result was saved, but could not be forwarded. Only a subject teacher can forward a draft result to the class teacher.';
    }

    private function missingCoordinatorForwardFields(Request $request): array
    {
        $fields = [];

        if (blank($request->promoted_class)) {
            $fields[] = 'Promoted To';
        }

        if (blank($request->input('working_days.term_one'))) {
            $fields[] = 'Term One Working Days';
        }

        if (blank($request->input('working_days.term_two'))) {
            $fields[] = 'Term Two Working Days';
        }

        if (blank($request->remarks)) {
            $fields[] = 'Remarks';
        }

        return $fields;
    }

    private function missingCoordinatorForwardResultFields(StudentResult $result): array
    {
        $fields = [];

        if (blank($result->promoted_class)) {
            $fields[] = 'Promoted To';
        }

        if (blank($result->t1_working_days)) {
            $fields[] = 'Term One Working Days';
        }

        if (blank($result->t2_working_days)) {
            $fields[] = 'Term Two Working Days';
        }

        if (blank($result->remarks)) {
            $fields[] = 'Remarks';
        }

        return $fields;
    }

    private function finalizationSubjectsFor(?int $sessionId, $classId)
    {
        return ClassSubject::where('class_subjects.class_id', $classId)
            ->when($sessionId, fn($query) => $query->where('class_subjects.session_id', $sessionId))
            ->join('subject_wise_marks', 'class_subjects.subject_id', '=', 'subject_wise_marks.id')
            ->select('subject_wise_marks.id', 'subject_wise_marks.subject_name')
            ->distinct()
            ->orderBy('subject_wise_marks.subject_name')
            ->get();
    }

    private function missingSubmittedFinalizeSubjects(Request $request, $requiredSubjects): array
    {
        $submitted = collect($request->subjects ?? [])
            ->filter(fn($row) => !empty($row['subject_id']))
            ->keyBy(fn($row) => (int) $row['subject_id']);

        return collect($requiredSubjects)
            ->filter(function ($subject) use ($submitted) {
                $row = $submitted->get((int) $subject->id);

                return !$row
                    || blank($row['term_one_mark'] ?? null);
            })
            ->pluck('subject_name')
            ->values()
            ->all();
    }

    private function missingStoredFinalizeSubjects(StudentResult $result): array
    {
        $requiredSubjects = $this->finalizationSubjectsFor($result->session_id, $result->class_id);
        $marks = $result->marks()->get()->keyBy(fn($mark) => (int) $mark->subject_id);

        return collect($requiredSubjects)
            ->filter(function ($subject) use ($marks) {
                $mark = $marks->get((int) $subject->id);

                return !$mark
                    || $mark->term_one_mark === null;
            })
            ->pluck('subject_name')
            ->values()
            ->all();
    }

    private function canEditResultDetails(User $user, $branchId, $classId, $sectionId): bool
    {
        if ($user->hasRole('Admin') || $user->hasRole('Coordinator')) {
            return true;
        }

        return $this->isClassTeacherForSection($user, $branchId, $classId, $sectionId);
    }

    private function isClassTeacherForSection(User $user, $branchId, $classId, $sectionId): bool
    {
        $activeSession = Session::active()->first();

        if (!$activeSession) {
            return false;
        }

        $classSubjectIds = ClassSubject::where('session_id', $activeSession->id)
            ->where('class_id', $classId)
            ->pluck('subject_id')
            ->map(fn($id) => (int) $id)
            ->unique()
            ->values();

        if ($classSubjectIds->isEmpty()) {
            $classSubjectIds = ClassSubject::where('class_id', $classId)
                ->pluck('subject_id')
                ->map(fn($id) => (int) $id)
                ->unique()
                ->values();
        }

        if ($classSubjectIds->isEmpty()) {
            return false;
        }

        $teacherSubjectIds = TeacherSubjectAssignment::where('teacher_id', $user->id)
            ->where('session_id', $activeSession->id)
            ->where('branch_id', $branchId)
            ->where('class_id', $classId)
            ->where('section_id', $sectionId)
            ->pluck('subject_id')
            ->map(fn($id) => (int) $id)
            ->unique()
            ->values();

        return $teacherSubjectIds->count() >= $classSubjectIds->count()
            && $classSubjectIds->diff($teacherSubjectIds)->isEmpty();
    }

    private function classTeacherForReport(StudentResult $result, $subjects): ?User
    {
        $subjectIds = collect($subjects)
            ->pluck('id')
            ->map(fn($id) => (int) $id)
            ->filter()
            ->unique()
            ->values();

        if ($subjectIds->isEmpty()) {
            return null;
        }

        $assignments = TeacherSubjectAssignment::with('teacher')
            ->where('branch_id', $result->branch_id)
            ->where('class_id', $result->class_id)
            ->where('section_id', $result->section_id)
            ->when($result->session_id, fn($query) => $query->where('session_id', $result->session_id))
            ->get();

        if ($assignments->isEmpty() && $result->session_id) {
            $assignments = TeacherSubjectAssignment::with('teacher')
                ->where('branch_id', $result->branch_id)
                ->where('class_id', $result->class_id)
                ->where('section_id', $result->section_id)
                ->get();
        }

        foreach ($assignments->groupBy('teacher_id') as $teacherAssignments) {
            $teacherSubjectIds = $teacherAssignments
                ->pluck('subject_id')
                ->map(fn($id) => (int) $id)
                ->unique()
                ->values();

            if ($teacherSubjectIds->count() >= $subjectIds->count() && $subjectIds->diff($teacherSubjectIds)->isEmpty()) {
                return $teacherAssignments->first()->teacher;
            }
        }

        return null;
    }

    private function encodeResultId(int $id): string
    {
        return rtrim(strtr(base64_encode((string) $id), '+/', '-_'), '=');
    }

    private function decodeResultId(string $encodedId): ?int
    {
        $encodedId = strtr($encodedId, '-_', '+/');
        $padding = strlen($encodedId) % 4;

        if ($padding) {
            $encodedId .= str_repeat('=', 4 - $padding);
        }

        $decoded = base64_decode($encodedId, true);

        return $decoded !== false && ctype_digit($decoded) ? (int) $decoded : null;
    }

    private function highestClassPercentagesForReport(StudentResult $result, $subjects): array
    {
        $subjectTotals = collect($subjects)
            ->mapWithKeys(fn($subject) => [
                (int) $subject->id => [
                    'term_one' => (float) ($subject->term_one_marks ?? 0),
                    'term_two' => (float) ($subject->term_two_marks ?? 0),
                ],
            ]);

        $results = StudentResult::with('marks')
            ->where('branch_id', $result->branch_id)
            ->where('class_id', $result->class_id)
            ->where('section_id', $result->section_id)
            ->when($result->session_id, fn($query) => $query->where('session_id', $result->session_id))
            ->get();

        $termOneHighest = 0;
        $termTwoHighest = 0;

        foreach ($results as $classResult) {
            $termOneTotal = 0;
            $termOneObtained = 0;
            $termTwoTotal = 0;
            $termTwoObtained = 0;

            foreach ($classResult->marks as $mark) {
                $totals = $subjectTotals->get((int) $mark->subject_id, []);
                $termOneMarkTotal = (float) ($mark->term_one_total ?? $totals['term_one'] ?? 0);
                $termTwoMarkTotal = (float) ($mark->term_two_total ?? $totals['term_two'] ?? 0);

                $termOneTotal += $termOneMarkTotal;
                $termOneObtained += (float) ($mark->term_one_mark ?? 0);
                $termTwoTotal += $termTwoMarkTotal;
                $termTwoObtained += (float) ($mark->term_two_mark ?? 0);
            }

            if ($termOneTotal > 0) {
                $termOneHighest = max($termOneHighest, round(($termOneObtained / $termOneTotal) * 100, 2));
            }

            if ($termTwoTotal > 0) {
                $termTwoHighest = max($termTwoHighest, round(($termTwoObtained / $termTwoTotal) * 100, 2));
            }
        }

        return [
            'term_one' => $termOneHighest,
            'term_two' => $termTwoHighest,
        ];
    }

    private function submittedSubjectIds(array $subjects)
    {
        return collect($subjects)
            ->pluck('subject_id')
            ->filter()
            ->map(fn($id) => (int) $id)
            ->unique()
            ->values();
    }

    private function prepareSubmittedMarks(array $subjects, $allowedSubjectIds, ?int $sessionId)
    {
        $allowed = collect($allowedSubjectIds)->map(fn($id) => (int) $id);

        return collect($subjects)
            ->filter(fn($row) => !empty($row['subject_id']) && $allowed->contains((int) $row['subject_id']))
            ->filter(fn($row) => ($row['term_one_mark'] ?? '') !== '' || ($row['term_two_mark'] ?? '') !== '')
            ->map(fn($row) => $this->buildMarkPayload($row, $sessionId))
            ->values();
    }

    private function buildMarkPayload(array $row, ?int $sessionId): array
    {
        $subject = SubjectWiseMarks::where('id', $row['subject_id'])
            ->when($sessionId, fn($query) => $query->where('session_id', $sessionId))
            ->firstOrFail();
        $termOneMark = floatval($row['term_one_mark'] ?? 0);
        $termTwoMark = floatval($row['term_two_mark'] ?? 0);
        $termOneTotal = floatval($subject->term_one_marks ?? 100);
        $termTwoTotal = floatval($subject->term_two_marks ?? 100);
        $termOnePercent = $this->calculatePercentage($termOneMark, $termOneTotal);
        $termTwoPercent = $this->calculatePercentage($termTwoMark, $termTwoTotal);
        $subjectPercentage = $this->calculatePercentage($termOneMark + $termTwoMark, $termOneTotal + $termTwoTotal);
        $subjectGrade = $this->calculateGrade($subjectPercentage);

        return [
            'subject_id' => $subject->id,
            'term_one_mark' => $termOneMark,
            'term_one_total' => $termOneTotal,
            'term_one_percent' => round($termOnePercent, 2),
            'term_one_grade' => $this->calculateGrade($termOnePercent),
            'term_two_mark' => $termTwoMark,
            'term_two_total' => $termTwoTotal,
            'term_two_percent' => round($termTwoPercent, 2),
            'term_two_grade' => $this->calculateGrade($termTwoPercent),
            'remarks' => $row['remarks'] ?? $this->generateRemarks($subjectGrade),
        ];
    }

    private function saveSubmittedMarks(StudentResult $result, $marksToSave): void
    {
        foreach ($marksToSave as $markData) {
            StudentMarks::updateOrCreate(
                [
                    'result_id' => $result->id,
                    'subject_id' => $markData['subject_id'],
                ],
                array_merge($markData, [
                    'session_id' => $result->session_id,
                    'erp_session_id' => $result->erp_session_id,
                ])
            );
        }
    }

    private function recalculateStudentResult(StudentResult $result): void
    {
        $marks = $result->marks()->get();
        $grandTermOne = $marks->sum(fn($mark) => (float) $mark->term_one_mark);
        $grandTermTwo = $marks->sum(fn($mark) => (float) $mark->term_two_mark);
        $termOneTotal = $marks->sum(fn($mark) => (float) $mark->term_one_total);
        $termTwoTotal = $marks->sum(fn($mark) => (float) $mark->term_two_total);
        $termOnePercentage = $this->calculatePercentage($grandTermOne, $termOneTotal);
        $termTwoPercentage = $this->calculatePercentage($grandTermTwo, $termTwoTotal);
        $totalPercentage = 0;

        foreach ($marks as $mark) {
            $totalPercentage += $this->calculatePercentage(
                (float) $mark->term_one_mark + (float) $mark->term_two_mark,
                (float) $mark->term_one_total + (float) $mark->term_two_total
            );
        }

        $overallPercentage = $marks->count() > 0 ? $totalPercentage / $marks->count() : 0;

        $result->update([
            'grand_term_one' => $grandTermOne,
            'percentage_term_one' => round($termOnePercentage, 2),
            'grand_term_two' => $grandTermTwo,
            'percentage_term_two' => round($termTwoPercentage, 2),
            'grand_total' => $grandTermOne + $grandTermTwo,
            'overall_percentage' => round($overallPercentage, 2),
            'overall_grade' => $this->calculateGrade($overallPercentage),
        ]);
    }

    private function validateResultCreateAccess(Request $request, $selectedSubjectIds): ?string
    {
        $user = auth()->user();
        $branchId = $request->branch_id;
        $classId = $request->class_id;
        $sectionId = $request->section_id;
        $studentId = $request->student_id;

        if (!$branchId || !$classId || !$sectionId || !$studentId) {
            return 'Please select branch, class, section and student.';
        }

        if (!$this->canAccessResultSection($user, $branchId, $classId, $sectionId)) {
            return 'You are not allowed to create result for this branch, class or section.';
        }

        $activeSession = Session::active()->first();
        $class = Classes::find($classId);
        $section = Section::find($sectionId);
        $student = Student::where('id', $studentId)
            ->where('session_id', $activeSession?->id)
            ->where('owned_by', $branchId)
            ->where('erp_class_id', $class?->erp_class_id)
            ->first();

        if (!$student) {
            return 'Selected student does not belong to the selected branch and class.';
        }

        if ($student->erp_section_id && $section && (string) $student->erp_section_id !== (string) $section->erp_section_id) {
            return 'Selected student does not belong to the selected section.';
        }

        $allowedSubjectIds = $this->resultSubjectsFor($user, $branchId, $classId, $sectionId)
            ->pluck('id')
            ->map(fn($id) => (int) $id)
            ->values();

        if ($selectedSubjectIds->isEmpty() || $selectedSubjectIds->diff($allowedSubjectIds)->isNotEmpty()) {
            return 'One or more selected subjects are not allowed for your role.';
        }

        return null;
    }
}

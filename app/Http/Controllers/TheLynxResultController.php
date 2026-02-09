<?php

namespace App\Http\Controllers;

use App\Models\Session;
use App\Models\StudentMarks;
use App\Models\StudentResult;
use App\Models\user;
use App\Models\SubjectWiseMarks;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


    class TheLynxResultController extends Controller
{
    public function results(Request $request)
    {
        $user = auth()->user();

        if (!$user->branch_name || !$user->branch_address || !$user->branch_email || !$user->branch_phone) {
            return redirect()->route('profile.edit')
                ->with('error', 'Please Fill Branch Information to access results.');
        }

        $isAdmin = $user->hasRole('Admin');
        $filterUserId = $request->get('user_id');

        // 🔑 MAIN QUERY
        $subjects = StudentResult::with('session')
            ->when(!$isAdmin, function ($q) use ($user) {
                $q->where('created_by', $user->id);
            })
            ->when($isAdmin && $filterUserId, function ($q) use ($filterUserId) {
                $q->where('created_by', $filterUserId);
            })
            ->orderByDesc('id')
            ->paginate('10');

        // 🔑 USERS LIST FOR ADMIN FILTER
        $users = $isAdmin
            ? User::whereHas('roles', function ($q) {
                $q->where('name', 'Teacher');
            })
            ->where('branch_name','!=','')->get()
            : collect();

        return view('results.student_result', compact('subjects', 'users', 'isAdmin'));
    }

    public function result_create()
    {
        $user = auth()->user();
        $isAdmin = $user->hasRole('Admin');
         if ($isAdmin) {
            // Redirect to profile edit page with a message
            return redirect()->route('profile.edit')->with('error', 'Only Teachers can create results');
        }

        // Check if any required profile field is missing
        if (!$user->branch_name || !$user->branch_address || !$user->branch_email ||!$user->branch_phone) {
            // Redirect to profile edit page with a message
            return redirect()->route('profile.edit')->with('error', 'Please Fill Branch Information to access results.');
        }
        
        $subjects = SubjectWiseMarks::select('subject_name', 'id')
            ->where('created_by', auth()->user()->id)
            ->distinct()
            ->get();

        $wdays = Session::orderByDesc('id')->get();

        return view('results.create2', compact('subjects', 'wdays'));
    }

    public function store(Request $request)
    {
        Log::info('Promoted Class:', [$request->promoted_class]);

        DB::beginTransaction();

        try {
            // Validate student details
            if (! $request->student_name || ! $request->roll_no || ! $request->class || ! $request->section) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please fill all student details (Name, Roll No, Class, Section).',
                ], 422);
            }
            $resultexist = StudentResult::where('rollno', $request->roll_no)->where('session_id', $request->session_id)->first();

            if ($resultexist) {
                return response()->json([
                    'success' => false,
                    'message' => 'Student Result already been generated.Edit the result.',
                ], 422);
            }

            if (empty($request->subjects) || ! is_array($request->subjects)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please add at least one subject with marks.',
                ], 422);
            }

            $t1Working = floatval($request->input('working_days.term_one', 0));
            $t2Working = floatval($request->input('working_days.term_two', 0));
            $totalAttendance = $t1Working + $t2Working;

            $grand_term_one = 0;
            $grand_term_two = 0;
            $totalSubjectPercentage = 0;
            $subjectCount = 0;
            $subjectsToSave = [];

            foreach ($request->subjects as $sub) {
                if (empty($sub['subject_id'])) continue;

                $subject = SubjectWiseMarks::findOrFail($sub['subject_id']);

                $termOneMark = floatval($sub['term_one_mark'] ?? 0);
                $termTwoMark = floatval($sub['term_two_mark'] ?? 0);

                $termOneTotal = floatval($subject->term_one_marks ?? 100);
                $termTwoTotal = floatval($subject->term_two_marks ?? 100);

                $termOnePercent = $this->calculatePercentage($termOneMark, $termOneTotal);
                $termTwoPercent = $this->calculatePercentage($termTwoMark, $termTwoTotal);

                $termOneGrade = $this->calculateGrade($termOnePercent);
                $termTwoGrade = $this->calculateGrade($termTwoPercent);

                $subjectObtained = $termOneMark + $termTwoMark;
                $subjectTotal = $termOneTotal + $termTwoTotal;
                $subjectPercentage = $this->calculatePercentage($subjectObtained, $subjectTotal);
                $subjectGrade = $this->calculateGrade($subjectPercentage);

                $grand_term_one += $termOneMark;
                $grand_term_two += $termTwoMark;
                $totalSubjectPercentage += $subjectPercentage;
                $subjectCount++;

                $subjectsToSave[] = [
                    'subject_id' => $subject->id,
                    'term_one_mark' => $termOneMark,
                    'term_one_total' => $termOneTotal,
                    'term_one_percent' => round($termOnePercent, 2),
                    'term_one_grade' => $termOneGrade,
                    'term_two_mark' => $termTwoMark,
                    'term_two_total' => $termTwoTotal,
                    'term_two_percent' => round($termTwoPercent, 2),
                    'term_two_grade' => $termTwoGrade,
                    'subject_grade' => $subjectGrade,
                    'remarks' => $sub['remarks'] ?? null,
                ];
            }

            // Save student result
            $student = StudentResult::create([
                'name' => $request->student_name,
                'class' => $request->class,
                'section' => $request->section,
                'rollno' => $request->roll_no,
                'session_id' => $request->session_id,
                'attendance' => $totalAttendance,
                't1_working_days' => $t1Working,
                't2_working_days' => $t2Working,
                'grand_term_one' => $grand_term_one,
                'grand_term_two' => $grand_term_two,
                'remarks' => $request->remarks ?? null,
                'promoted_class' => $request->promoted_class ?? null,
                'created_by' => auth()->id(),
            ]);

            // Save each subject's marks and remarks
            foreach ($subjectsToSave as $markData) {
                StudentMarks::create([
                    'result_id' => $student->id,
                    'subject_id' => $markData['subject_id'],
                    'term_one_mark' => $markData['term_one_mark'],
                    'term_one_total' => $markData['term_one_total'],
                    'term_one_percent' => $markData['term_one_percent'],
                    'term_one_grade' => $markData['term_one_grade'],
                    'term_two_mark' => $markData['term_two_mark'],
                    'term_two_total' => $markData['term_two_total'],
                    'term_two_percent' => $markData['term_two_percent'],
                    'term_two_grade' => $markData['term_two_grade'],
                    'remarks' => $markData['remarks'] ?? $this->generateRemarks($markData['subject_grade']),
                ]);
            }

            $overallPercentage = $subjectCount > 0 ? ($totalSubjectPercentage / $subjectCount) : 0;
            $overallGrade = $this->calculateGrade($overallPercentage);

            $student->update([
                'overall_percentage' => round($overallPercentage, 2),
                'overall_grade' => $overallGrade,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Student result generated successfully',
                'data' => [
                    'student_id' => $student->id,
                    'overall_percentage' => round($overallPercentage, 2),
                    'overall_grade' => $overallGrade,
                ],
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Error storing student result', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

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
            $student = StudentResult::findOrFail($id);

            if ($student->created_by !== auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access',
                ], 403);
            }

            $t1Working = floatval($request->input('working_days.term_one', 0));
            $t2Working = floatval($request->input('working_days.term_two', 0));
            $totalAttendance = $t1Working + $t2Working;

            StudentMarks::where('result_id', $student->id)->delete();

            $grand_term_one = 0;
            $grand_term_two = 0;
            $totalSubjectPercentage = 0;
            $subjectCount = 0;

            foreach ($request->subjects as $sub) {
                if (empty($sub['subject_id'])) continue;

                $subject = SubjectWiseMarks::findOrFail($sub['subject_id']);

                $termOneMark = floatval($sub['term_one_mark'] ?? 0);
                $termTwoMark = floatval($sub['term_two_mark'] ?? 0);

                $termOneTotal = floatval($subject->term_one_marks ?? 100);
                $termTwoTotal = floatval($subject->term_two_marks ?? 100);

                $termOnePercent = $this->calculatePercentage($termOneMark, $termOneTotal);
                $termTwoPercent = $this->calculatePercentage($termTwoMark, $termTwoTotal);

                $termOneGrade = $this->calculateGrade($termOnePercent);
                $termTwoGrade = $this->calculateGrade($termTwoPercent);

                $subjectObtained = $termOneMark + $termTwoMark;
                $subjectTotal = $termOneTotal + $termTwoTotal;
                $subjectPercentage = $this->calculatePercentage($subjectObtained, $subjectTotal);
                $subjectGrade = $this->calculateGrade($subjectPercentage);

                $grand_term_one += $termOneMark;
                $grand_term_two += $termTwoMark;
                $totalSubjectPercentage += $subjectPercentage;
                $subjectCount++;

                StudentMarks::create([
                    'result_id' => $student->id,
                    'subject_id' => $subject->id,
                    'term_one_mark' => $termOneMark,
                    'term_one_total' => $termOneTotal,
                    'term_one_percent' => round($termOnePercent, 2),
                    'term_one_grade' => $termOneGrade,
                    'term_two_mark' => $termTwoMark,
                    'term_two_total' => $termTwoTotal,
                    'term_two_percent' => round($termTwoPercent, 2),
                    'term_two_grade' => $termTwoGrade,
                    'remarks' => $sub['remarks'] ?? $this->generateRemarks($subjectGrade),
                ]);
            }
            // dd($request->all());
            $overallPercentage = $subjectCount > 0 ? ($totalSubjectPercentage / $subjectCount) : 0;
            $overallGrade = $this->calculateGrade($overallPercentage);

            $student->update([
                'name' => $request->student_name,
                'class' => $request->class,
                'section' => $request->section,
                'rollno' => $request->roll_no,
                'session_id' => $request->session_id,
                'attendance' => $totalAttendance,
                't1_working_days' => $t1Working,
                't2_working_days' => $t2Working,
                'grand_term_one' => $grand_term_one,
                'grand_term_two' => $grand_term_two,
                'grand_total' => $grand_term_one + $grand_term_two,
                'overall_percentage' => round($overallPercentage, 2),
                'overall_grade' => $overallGrade,
                'remarks' => $request->remarks ?? null,
                'promoted_class' => $request->promoted_class ?? null,
            ]);

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

        if ($student->created_by === auth()->id()) {
            StudentMarks::where('result_id', $student->id)->delete();
            $student->delete();
        }

        return redirect()->back();
    }

    public function show($id)
    {
        $student = StudentResult::with('marks.subject', 'session')->findOrFail($id);
        if (!$student) {
            return redirect()->back()->with('message', 'Result Not Found');
        }
        if ($student->created_by !== auth()->id()) {
            abort(403, 'Unauthorized access');
        }

        return view('results.final_result_card', compact('student'));
    }

    private function calculatePercentage($obtained, $total)
    {
        if ($total == 0) return 0;
        return ($obtained / $total) * 100;
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
        $remarks = [
            'A+' => 'Outstanding',
            'A' => 'Excellent',
            'B' => 'Very Good',
            'C' => 'Good',
            'D' => 'Satisfactory',
            'F' => 'Needs Improvement',
        ];

        return $remarks[$grade] ?? 'N/A';
    }

    public function edit($id)
    {
        $student = StudentResult::with('marks.subject', 'session')->findOrFail($id);

        if ($student->created_by !== auth()->id()) {
            abort(403, 'Unauthorized access');
        }

        $subjects = SubjectWiseMarks::select('subject_name', 'id')
            ->where('created_by', auth()->id())
            ->distinct()
            ->get();

        $wdays = Session::orderByDesc('id')->get();

        return view('results.edit', compact('student', 'subjects', 'wdays'));
    }

    public function search(Request $request)
    {
        $user = auth()->user();
        $isAdmin = $user->hasRole('Admin');
        $search = $request->search;
        $filterUserId = $request->user_id;

        $results = StudentResult::with('session')
            ->when(!$isAdmin, function ($q) use ($user) {
                $q->where('created_by', $user->id);
            })
            ->when($isAdmin && $filterUserId, function ($q) use ($filterUserId) {
                $q->where('created_by', $filterUserId);
            })
            ->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('rollno', 'LIKE', "%{$search}%")
                    ->orWhere('overall_grade', 'LIKE', "%{$search}%")
                    ->orWhere('class', 'LIKE', "%{$search}%")
                    ->orWhere('section', 'LIKE', "%{$search}%")
                    ->orWhereHas('session', function ($q) use ($search) {
                        $q->where('title', 'LIKE', "%{$search}%");
                    });
            })
            ->orderByDesc('id')
            ->get();

        return response()->json($results);
    }
}

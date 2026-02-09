<?php

namespace App\Http\Controllers;

use App\Models\SubjectWiseMarks;
use Illuminate\Http\Request;

class SubjectWiseMarksController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {        $user = auth()->user();

        // Check if any required profile field is missing
        if (!$user->branch_name || !$user->branch_address || !$user->branch_email ||!$user->branch_phone) {
            // Redirect to profile edit page with a message
            return redirect()->route('profile.edit')->with('error', 'Please Fill Branch Information to access results.');
        }
        // dd(auth()->user()->id);
        $subject_marks = SubjectWiseMarks::where('created_by', auth()->user()->id)->get();

        //    dd($subject_marks);
        return view('subject_marks.index', ['subject_marks' => $subject_marks]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $subjects = SubjectWiseMarks::select('subject_name', 'id')
            ->where('created_by', auth()->user()->id)
            ->distinct()
            ->get();

        return view('subject_marks.create', compact('subjects'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
    'subject_name' => 'required|string',
    'term_one_marks' => 'required|numeric',
    'term_two_marks' => 'required|numeric',
]);
        $submark = new SubjectWiseMarks;
        $submark->subject_name = $request->subject_name;
        $submark->term_one_marks = $request->term_one_marks;
        $submark->term_two_marks = $request->term_two_marks;
        $submark->created_by = auth()->user()->id;
        $submark->save();

        return redirect()->route('subject-marks.index')
            ->with('success', 'Subject saved successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $submark = SubjectWiseMarks::findOrFail($id);

        return view('subject_marks.edit', compact('submark'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $submark = SubjectWiseMarks::find($id);
        $submark->subject_name = $request->subject_name;
        $submark->term_one_marks = $request->term_one_marks;
        $submark->term_two_marks = $request->term_two_marks;
        $submark->created_by = auth()->user()->id;
        $submark->save();

        return redirect()->route('subject-marks.index')
            ->with('success', 'changes successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {

        $submark = SubjectWiseMarks::findOrFail($id);
        $submark->delete();
        return redirect()->route('subject-marks.index')
            ->with('success', 'Record deleted successfully');
    }
    public function subject_total_marks(Request $request)
    {
        try {
            $subjectId = $request->subject_id;

            if (! $subjectId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Subject ID is required',
                ], 400);
            }

            // Fetch subject from database
            $subject = \App\Models\SubjectWiseMarks::find($subjectId);

            if (! $subject) {
                return response()->json([
                    'success' => false,
                    'message' => 'Subject not found',
                ], 404);
            }

            // Return the total marks for each term
            // Assuming your subjects table has these columns
            // Adjust column names according to your actual database structure
            return response()->json([
                'success' => true,
                'data' => [
                    'subject_id' => $subject->id,
                    'subject_name' => $subject->subject_name,
                    'term_one_total_mark' => $subject->term_one_marks ?? 100,
                    'term_two_total_mark' => $subject->term_two_marks ?? 100,
                ],
            ], 200);

        } catch (\Exception $e) {
            dd($e);

            return response()->json([
                'success' => false,
                'message' => 'Error fetching subject marks: '.$e->getMessage(),
            ], 500);
        }
    }
    public function search_subject(Request $request){
        $search = $request->search;
        $show = SubjectWiseMarks::get()->where('created_by' , auth()->id)
        ->where(function($show) use($search){
            $show->where('subject_name', 'LIKE' , "%{$search}%")
            ->where('term_one_mark' , 'LIKE', "%{$search}%")
            ->where('term_two_mark' , 'LIKE', "%{$search}%");


        })
        ->get();
        return response()->json($show);
    }
}

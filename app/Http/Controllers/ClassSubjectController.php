<?php

namespace App\Http\Controllers;

use App\Models\Classes;
use App\Models\ClassSubject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ClassSubjectController extends Controller
{
    public function index()
    {
        $branches = collect();

        try {

            $response = Http::timeout(10)->get(env('API_URL') . 'get-branches');

            if ($response->successful()) {

                $data = $response->json();

                $allBranches = $data['data'] ?? $data;

                $usedBranchIds = Classes::select('erp_branch_id')
                    ->distinct()
                    ->whereNotNull('erp_branch_id')
                    ->pluck('erp_branch_id')
                    ->toArray();

                $branches = collect($allBranches)
                    ->filter(fn($b) => in_array($b['id'], $usedBranchIds))
                    ->map(fn($b) => [
                        'id'   => $b['id'],
                        'name' => $b['name'] ?? $b['branch_name'] ?? 'Branch #' . $b['id'],
                    ])
                    ->values();
            }
        } catch (\Exception $e) {

            Log::error($e->getMessage());
        }

        $classes = Classes::orderBy('name')->get();

        $subjects = DB::table('subject_wise_marks')
            ->select('id', 'subject_name')
            ->distinct()
            ->orderBy('subject_name')
            ->get();

        $assignedSubjects = ClassSubject::with('class')
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
        $request->validate([
            'branch_id' => 'required',
            'class_id' => 'required',
            'subjects' => 'required|array',
        ]);

        // delete old subjects of class
        ClassSubject::where('class_id', $request->class_id)->delete();

        foreach ($request->subjects as $subjectId) {

            ClassSubject::create([
                'branch_id' => $request->branch_id,
                'class_id' => $request->class_id,
                'subject_id' => $subjectId,
            ]);
        }

        return back()->with('success', 'Subjects assigned successfully.');
    }
}

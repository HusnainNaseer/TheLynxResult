<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Models\Classes;
use App\Models\Section;

class FetchApiController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | STUDENTS
    |--------------------------------------------------------------------------
    */
    public function getstudents()
    {
        try {
            $students = Cache::remember('students', 60, function () {
                $response = Http::timeout(10)->get(env('API_URL') . 'get-students');
                if ($response->successful()) {
                    return $response->json();
                }
                Log::error('Failed to fetch students: ' . $response->body());
                return [];
            });

            return response()->json($students);
        } catch (\Exception $e) {
            Log::error('Error fetching students: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch students'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | BRANCHES  –  only ACTIVE branches returned
    |--------------------------------------------------------------------------
    */
    public function getbranches()
    {
        try {
            $branches = Cache::remember('branches_raw', 60, function () {
                $response = Http::timeout(10)->get(env('API_URL') . 'get-branches');
                if ($response->successful()) {
                    return $response->json();
                }
                Log::error('Failed to fetch branches: ' . $response->body());
                return null;
            });

            if (!$branches) {
                Cache::forget('branches_raw'); // clear bad cache immediately
                return response()->json(['error' => 'Failed to fetch branches'], 500);
            }

            $allBranches = $branches['data'] ?? $branches;

            $activeBranches = collect($allBranches)->filter(function ($branch) {
                if (isset($branch['is_active'])) {
                    return (bool) $branch['is_active'];
                }
                if (isset($branch['status'])) {
                    return in_array(strtolower((string) $branch['status']), ['active', '1', 'true']);
                }
                return true;
            })->values()->all();

            return response()->json(['success' => true, 'data' => $activeBranches]);
        } catch (\Exception $e) {
            Cache::forget('branches_raw'); // don't keep bad cache on exception
            Log::error('Error fetching branches: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch branches'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | CLASSES
    |--------------------------------------------------------------------------
    */
    public function getclasses(Request $request)
{
    $branchId = $request->query('branch_id');

    try {
        $response = Http::timeout(10)->get(env('API_URL') . 'get-classes');

        if (!$response->successful()) {
            return response()->json(['error' => 'Failed to fetch classes'], 500);
        }

        $data = $response->json()['data'] ?? [];

        foreach ($data as $class) {
            Classes::updateOrCreate(
                ['erp_class_id' => $class['id']],
                [
                    'name'          => $class['name']      ?? null,
                    'erp_branch_id' => $class['owned_by']  ?? null,  // ← was missing
                    'owned_by'      => $class['owned_by']  ?? null,
                ]
            );
        }

        if ($branchId) {
            $data = collect($data)->where('owned_by', $branchId)->values()->all();
        }

        return response()->json($data);
    } catch (\Exception $e) {
        Log::error('Error fetching classes: ' . $e->getMessage());
        return response()->json(['error' => 'Failed to fetch classes'], 500);
    }
}

    /*
    |--------------------------------------------------------------------------
    | EMPLOYEES  –  filter by branch, never mix branch caches
    |--------------------------------------------------------------------------
    */
    public function getbranchemployee(Request $request)
    {
        $branchId = $request->query('branch_id');

        if (!$branchId) {
            return response()->json(['success' => false, 'error' => 'branch_id is required'], 400);
        }

        try {
            $response = Http::timeout(10)->get(env('API_URL') . "get-employees-by-branch/{$branchId}");

            if (!$response->successful()) {
                Log::error('Failed to fetch employees: ' . $response->body());
                return response()->json(['success' => false, 'error' => 'Failed to fetch employees'], 500);
            }

            $data = $response->json();
            $employees = $data['data'] ?? [];

            return response()->json(['success' => true, 'data' => $employees]);
        } catch (\Exception $e) {
            Log::error('Error fetching employees: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => 'Failed to fetch employees'], 500);
        }
    }
    /*
    |--------------------------------------------------------------------------
    | SINGLE EMPLOYEE DETAILS
    |--------------------------------------------------------------------------
    */
    public function getemployeedetails($employeeId)
    {
        try {
            $response = Http::timeout(10)->get(env('API_URL') . "get-employees-by-branch/0");
            // fallback: just return not found since we don't have a single-employee endpoint
            return response()->json(['success' => false, 'message' => 'Employee not found'], 404);
        } catch (\Exception $e) {
            Log::error('Error fetching employee details: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => 'Failed to fetch employee details'], 500);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | SECTIONS
    |--------------------------------------------------------------------------
    */
    public function getsections(Request $request)
    {
        $classId = $request->query('class_id');

        try {
            $response = Http::timeout(10)->get(env('API_URL') . 'get-sections');

            if (!$response->successful()) {
                return response()->json(['error' => 'Failed to fetch sections'], 500);
            }

            $data = $response->json()['data'] ?? [];

            foreach ($data as $section) {
                Section::updateOrCreate(
                    ['erp_section_id' => $section['id']],
                    [
                        'class_id' => $section['class_id'] ?? null,
                        'name'     => $section['name'] ?? null,
                        'owned_by' => $section['owned_by'] ?? null,
                    ]
                );
            }

            if ($classId) {
                $data = collect($data)->where('class_id', $classId)->values()->all();
            }

            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch sections'], 500);
        }
    }
    
}

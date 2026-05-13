<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules\Password;
use Spatie\Permission\Models\Role;

class TeachersController extends Controller
{
    /**
     * Fetch active branches from the ERP API.
     */
    private function fetchBranchesFromERP(): array
    {
        try {
            $erpUrl = rtrim(env('ERP_API_URL', ''), '/');

            if (empty($erpUrl)) {
                Log::warning('ERP_API_URL is not configured in .env');
                return [];
            }

            $response = Http::timeout(10)->get("{$erpUrl}/api/get-branches");

            if ($response->successful()) {
                $body = $response->json();
                if (!empty($body['status']) && !empty($body['data'])) {
                    return $body['data'];
                }
            }
        } catch (\Exception $e) {
            Log::error('ERP branch fetch failed: ' . $e->getMessage());
        }

        return [];
    }

    public function index()
    {
        $users = User::whereHas('roles', function ($q) {
            $q->whereIn('name', ['Teacher', 'User','Coordinator']);
        })
            ->orderBy('created_at', 'desc')
            ->get();
        return view('teachers.index', compact('users'));
    }

    public function grantTeacherRole($id)
    {
        $user = User::findOrFail($id);
        $user->syncRoles('Teacher');
        return back()->with('success', $user->name . ' is now a teacher.');
    }

    public function revokeTeacherRole($id)
    {
        $user = User::findOrFail($id);
        $user->syncRoles('User');
        return back()->with('success', $user->name . ' is now a user.');
    }

    public function search_teacher(Request $request)
    {
        $search = $request->search;
        $teacher = User::where('created_by', auth()->user()->id)
            ->where(function ($teacher) use ($search) {
                $teacher->where('name', 'LIKE', "%{$search}%")
                    ->where('email', 'LIKE', "%{$search}$")
                    ->where('role', 'LIKE', "%{$search}$");
            })
            ->get();
        return response()->json($teacher);
    }

    public function teacher_edit(Request $request, $id)
    {
        $user = User::find($id);
        if ($user) {
            $branches = $this->fetchBranchesFromERP();
            return view('teachers.teachers-edit', compact('user', 'branches'));
        } else {
            return redirect()->back()->with('error', 'User Not found in record!');
        }
    }

    public function create()
    {
        $branches = $this->fetchBranchesFromERP();
        return view('teachers.create', compact('branches'));
    }

    public function store(Request $request)
    {
        // dd($request->all());
        $validated = $request->validate([
            'branch_id'      => ['required', 'integer'],
            'role'           => ['required', 'string', 'in:Teacher,Coordinator'],
            'employee_id'    => ['required', 'integer'],
            'employee_email' => ['required', 'email'],
            'password'       => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        try {
            // Fetch employee from source API
            $response = Http::timeout(10)
                ->get(env('API_URL') . "get-employees-by-branch/{$validated['branch_id']}");

            $employeeData = null;

            if ($response->successful()) {
                $employees    = $response->json()['data'] ?? [];
                $employeeData = collect($employees)
                    ->firstWhere('id', (int) $validated['employee_id']);
            }

            // Use API data if available, fall back to form submission
            $name  = $employeeData['name']  ?? 'Unknown';
            $email = $employeeData['email'] ?? $validated['employee_email'];

            if (empty($email)) {
                return back()
                    ->withErrors(['employee_id' => 'Employee record is missing an email address.'])
                    ->withInput();
            }

            // Build the data array — only include columns that exist
            $userData = [
                'name'     => $name,
                'email'    => $email,
                'password' => Hash::make($validated['password']),
                // 'is_active' => 1,
            ];
            // dd($employeeData['employee']['profile_img']);
            // Safely add optional columns
            $optionalColumns = [
                'branch_id'       => $validated['branch_id'],
                'erp_employee_id' => $validated['employee_id'],
                'erp_picture' => $employeeData['employee']['profile_img'],
                'branch_name' => $employeeData['employee']['branchdetail']['name'],
                'branch_email' => $employeeData['employee']['userbranch']['email'],
                'branch_address' => $employeeData['employee']['branchdetail']['address'],
                'branch_phone' => $employeeData['employee']['branchdetail']['phone_no'],
            ];

            foreach ($optionalColumns as $column => $value) {
                try {
                    if (\Schema::hasColumn('users', $column)) {
                        $userData[$column] = $value;
                        $updateData[$column] = $value;
                    }
                } catch (\Exception $e) {
                    Log::warning("Column check failed for {$column}: " . $e->getMessage());
                }
            }

            $user = User::where('email', $email)->first();

            if ($user) {
                $updateData['password'] = Hash::make($validated['password']);
    // dd($updateData);
                if (\Schema::hasColumn('users', 'branch_id')) {
                    $updateData['branch_id'] = $validated['branch_id'];
                }
                if (\Schema::hasColumn('users', 'erp_employee_id')) {
                    $updateData['erp_employee_id'] = $validated['employee_id'];
                }

                $user->update($updateData);
            } else {
                $user = User::create($userData);
            }

            $user->syncRoles([$validated['role']]);

            return redirect()
                ->route('teachers.index')
                ->with('success', $user->name . ' has been assigned as ' . $validated['role'] . ' successfully!');
        } catch (\Exception $e) {
            Log::error('Error creating employee: ' . $e->getMessage() . ' | Line: ' . $e->getLine() . ' | File: ' . $e->getFile());

            return back()
                ->withErrors(['employee_id' => 'Error: ' . $e->getMessage()])
                ->withInput();
        }
    }
}

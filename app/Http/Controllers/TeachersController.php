<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Session;
use App\Models\Branch;
use App\Models\Classes;
use App\Support\BranchScope;
use App\Support\ErpHttp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules\Password;
use Spatie\Permission\Models\Role;

class TeachersController extends Controller
{
    private function localBranches()
    {
        $query = Branch::query()
            ->orderBy('name');
        BranchScope::apply($query, 'erp_branch_id');

        return $query
            ->get()
            ->map(fn($branch) => [
                'id' => $branch->erp_branch_id,
                'name' => $branch->name,
                'email' => $branch->email,
                'phone' => $branch->phone,
                'address' => $branch->address,
            ])
            ->values();
    }

    public function index()
    {
        $visibleRoles = auth()->user()?->hasRole('Coordinator')
            ? ['Teacher']
            : ['Teacher', 'User', 'Coordinator'];

        $users = User::whereHas('roles', function ($q) use ($visibleRoles) {
            $q->whereIn('name', $visibleRoles);
        })
            ->when(BranchScope::coordinatorBranchId(), fn($query, $branchId) => $query->where('branch_id', $branchId))
            ->orderBy('created_at', 'desc')
            ->get();
        return view('teachers.index', compact('users', 'visibleRoles'));
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
            BranchScope::abortIfCoordinatorOutside($user->branch_id);

            if (auth()->user()?->hasRole('Coordinator') && !$user->hasRole('Teacher')) {
                abort(403, 'Coordinator can edit teachers only.');
            }

            $branches = $this->localBranches();
            $branchesSelect = collect($branches)
                ->mapWithKeys(function ($branch) {
                    $id = $branch['id'] ?? $branch['erp_branch_id'] ?? null;
                    $name = $branch['name'] ?? $branch['branch_name'] ?? $branch['title'] ?? null;

                    return $id ? [$id => ($name ?: 'Branch #' . $id)] : [];
                });

            if ($branchesSelect->isEmpty()) {
                $branchesSelectQuery = Branch::orderBy('name');
                BranchScope::apply($branchesSelectQuery, 'erp_branch_id');
                $branchesSelect = $branchesSelectQuery->pluck('name', 'erp_branch_id');
            }

            $activeSession = Session::active()->first();
            $classesSelect = Classes::query()
                ->when($activeSession, fn($query) => $query->where('session_id', $activeSession->id))
                ->when($user->branch_id, fn($query) => $query->where('erp_branch_id', $user->branch_id))
                ->orderBy('name')
                ->pluck('name', 'id');

            $classesByBranch = Classes::query()
                ->when($activeSession, fn($query) => $query->where('session_id', $activeSession->id))
                ->when(BranchScope::coordinatorBranchId(), fn($query, $branchId) => $query->where('erp_branch_id', $branchId))
                ->orderBy('name')
                ->get(['id', 'name', 'erp_branch_id'])
                ->groupBy(fn($class) => (string) $class->erp_branch_id)
                ->map(fn($classes) => $classes->map(fn($class) => [
                    'id' => $class->id,
                    'name' => $class->name,
                ])->values())
                ->toArray();

            return view('teachers.teachers-edit', compact('user', 'branches', 'branchesSelect', 'classesSelect', 'classesByBranch'));
        } else {
            return redirect()->back()->with('error', 'User Not found in record!');
        }
    }

    public function create()
    {
        $branches = $this->localBranches();
        return view('teachers.create', compact('branches'));
    }

    public function store(Request $request)
    {
        // dd($request->all());
        $roleRule = auth()->user()?->hasRole('Coordinator')
            ? 'in:Teacher'
            : 'in:Teacher,Coordinator';

        $validated = $request->validate([
            'branch_id'      => ['required', 'integer'],
            'role'           => ['required', 'string', $roleRule],
            'employee_id'    => ['required', 'integer'],
            'employee_name'  => ['nullable', 'string', 'max:255'],
            'employee_email' => ['required', 'email'],
            'password'       => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        try {
            BranchScope::abortIfCoordinatorOutside($validated['branch_id']);

            $activeSession = Session::active()->first();

            $employeeData = null;

            try {
                $response = ErpHttp::get("get-employees-by-branch/{$validated['branch_id']}", 30);

                if ($response->successful()) {
                    $employees = $response->json()['data'] ?? [];
                    $employeeData = collect($employees)
                        ->firstWhere('id', (int) $validated['employee_id']);
                }
            } catch (\Throwable $e) {
                Log::warning('ERP employee lookup timed out or failed during user create; using submitted employee data.', [
                    'branch_id' => $validated['branch_id'],
                    'employee_id' => $validated['employee_id'],
                    'error' => $e->getMessage(),
                ]);
            }

            $localBranch = Branch::where('erp_branch_id', $validated['branch_id'])->first();

            // Use API data if available, fall back to the employee selected in the form.
            $fallbackName = $validated['employee_name'] ?: (strtok($validated['employee_email'], '@') ?: 'Unknown');
            $name  = $employeeData['name'] ?? $fallbackName;
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
                'created_by' => auth()->id(),
                // 'is_active' => 1,
            ];
            $updateData = [];
            // dd($employeeData['employee']['profile_img']);
            // Safely add optional columns
            $optionalColumns = [
                'branch_id'       => $validated['branch_id'],
                'erp_employee_id' => $validated['employee_id'],
                'session_id' => $activeSession?->id,
                'erp_session_id' => $activeSession ? ($activeSession->erp_session_id ?: (string) $activeSession->id) : null,
                'erp_picture' => $employeeData['employee']['profile_img'] ?? null,
                'branch_name' => $localBranch?->name ?? $employeeData['employee']['branchdetail']['name'] ?? null,
                'branch_email' => $localBranch?->email ?? $employeeData['employee']['userbranch']['email'] ?? null,
                'branch_address' => $localBranch?->address ?? $employeeData['employee']['branchdetail']['address'] ?? null,
                'branch_phone' => $localBranch?->phone ?? $employeeData['employee']['branchdetail']['phone_no'] ?? null,
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

            if (User::where('email', $email)->exists()) {
                return back()
                    ->withErrors(['employee_email' => 'This email is already registered. Please use another employee.'])
                    ->withInput();
            }

            $user = User::create($userData);
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

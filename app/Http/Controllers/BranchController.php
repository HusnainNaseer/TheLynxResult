<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Support\ErpHttp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BranchController extends Controller
{
    public function index()
    {
        $branches = Branch::orderBy('name')->get();
        $totalInDb = $branches->count();
        $activeCount = $branches->where('is_active', true)->count();

        return view('branches.index', compact('branches', 'totalInDb', 'activeCount'));
    }

    public function sync()
    {
        try {
            $response = ErpHttp::get('get-branches', 15);

            if (!$response->successful()) {
                return back()->with('error', 'Failed to reach ERP API. Status: ' . $response->status());
            }

            $data = $response->json();
            $branches = $data['data'] ?? $data;

            if (empty($branches) || !is_array($branches)) {
                return back()->with('error', 'API returned no branch data.');
            }

            $synced = 0;
            $skipped = 0;

            foreach ($branches as $branch) {
                if (empty($branch['id'])) {
                    $skipped++;
                    continue;
                }

                $localBranch = Branch::firstOrNew(['erp_branch_id' => $branch['id']]);
                $localBranch->name = $branch['name'] ?? $branch['branch_name'] ?? 'Branch #' . $branch['id'];
                $localBranch->email = $localBranch->email ?: ($branch['email'] ?? $branch['branch_email'] ?? null);
                $localBranch->phone = $localBranch->phone ?: ($branch['phone'] ?? $branch['branch_phone'] ?? null);
                $localBranch->address = $localBranch->address ?: ($branch['address'] ?? $branch['branch_address'] ?? null);
                $localBranch->is_active = $this->isActiveBranch($branch);
                $localBranch->save();

                $synced++;
            }

            return back()->with('success', "Sync complete - {$synced} branches saved, {$skipped} skipped.");
        } catch (\Exception $e) {
            Log::error('Branch sync failed: ' . $e->getMessage());
            return back()->with('error', 'Sync failed: ' . $e->getMessage());
        }
    }

    public function resync()
    {
        return $this->sync();
    }

    public function edit(Branch $branch)
    {
        return view('branches.edit', compact('branch'));
    }

    public function update(Request $request, Branch $branch)
    {
        $validated = $request->validate([
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
            'principal_headmistress' => ['nullable', 'string', 'max:255'],
            'executive_director_islamabad' => ['nullable', 'string', 'max:255'],
        ]);

        $branch->update($validated);

        return redirect()
            ->route('branches.index')
            ->with('success', 'Branch information updated successfully.');
    }

    private function isActiveBranch(array $branch): bool
    {
        if (array_key_exists('is_active', $branch)) {
            return (bool) $branch['is_active'];
        }

        if (array_key_exists('status', $branch)) {
            return in_array(strtolower((string) $branch['status']), ['active', '1', 'true'], true);
        }

        return true;
    }
}

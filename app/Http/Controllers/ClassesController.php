<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Classes;
use App\Models\Session;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ClassesController extends Controller
{
    public function index()
    {
        $activeSession = Session::active()->first();
        $classQuery = Classes::query()
            ->when($activeSession, fn($query) => $query->where('session_id', $activeSession->id));

        $totalInDb = (clone $classQuery)->count();

        $branchIds = (clone $classQuery)->select('erp_branch_id')
            ->distinct()
            ->whereNotNull('erp_branch_id')
            ->pluck('erp_branch_id');

        $branches = Branch::whereIn('erp_branch_id', $branchIds)
            ->orderBy('name')
            ->get()
            ->map(fn($branch) => [
                'id' => $branch->erp_branch_id,
                'name' => $branch->name,
            ]);

        return view('classes.index', compact('totalInDb', 'branches'));
    }

    public function sync()
    {
        try {
            $activeSession = Session::active()->first();

            if (!$activeSession) {
                return back()->with('error', 'Please activate a session before syncing classes.');
            }

            $branchStats = $this->syncBranchesFromErp();

            $response = Http::timeout(15)->get(env('API_URL') . 'get-classes');

            if (!$response->successful()) {
                return back()->with('error', 'Failed to reach ERP API. Status: ' . $response->status());
            }

            $data = $response->json();
            $classes = $data['data'] ?? $data;

            if (empty($classes) || !is_array($classes)) {
                return back()->with('error', 'API returned no class data.');
            }

            $synced = 0;
            $skipped = 0;

            foreach ($classes as $cls) {
                if (empty($cls['id'])) {
                    $skipped++;
                    continue;
                }

                Classes::updateOrCreate(
                    [
                        'erp_class_id' => $cls['id'],
                        'session_id' => $activeSession->id,
                    ],
                    [
                        'erp_session_id' => $activeSession->erp_session_id ?: (string) $activeSession->id,
                        'name' => $cls['name'] ?? 'Unknown',
                        'erp_branch_id' => $cls['owned_by'] ?? null,
                        'owned_by' => $cls['owned_by'] ?? null,
                    ]
                );

                $synced++;
            }

            return back()->with('success', "Sync complete - {$branchStats['synced']} branches saved, {$synced} classes saved, {$skipped} skipped.");
        } catch (\Exception $e) {
            Log::error('Class sync failed: ' . $e->getMessage());
            return back()->with('error', 'Sync failed: ' . $e->getMessage());
        }
    }

    public function resync()
    {
        return $this->sync();
    }

    private function syncBranchesFromErp(): array
    {
        $response = Http::timeout(15)->get(env('API_URL') . 'get-branches');

        if (!$response->successful()) {
            Log::error('Branch sync failed: ' . $response->body());
            return ['synced' => 0, 'skipped' => 0];
        }

        $data = $response->json();
        $branches = $data['data'] ?? $data;
        $synced = 0;
        $skipped = 0;

        foreach ($branches as $branch) {
            if (empty($branch['id'])) {
                $skipped++;
                continue;
            }

            Branch::updateOrCreate(
                ['erp_branch_id' => $branch['id']],
                [
                    'name' => $branch['name'] ?? $branch['branch_name'] ?? 'Branch #' . $branch['id'],
                    'email' => $branch['email'] ?? $branch['branch_email'] ?? null,
                    'phone' => $branch['phone'] ?? $branch['branch_phone'] ?? null,
                    'address' => $branch['address'] ?? $branch['branch_address'] ?? null,
                    'is_active' => isset($branch['is_active'])
                        ? (bool) $branch['is_active']
                        : (!isset($branch['status']) || in_array(strtolower((string) $branch['status']), ['active', '1', 'true'])),
                ]
            );

            $synced++;
        }

        return compact('synced', 'skipped');
    }
}

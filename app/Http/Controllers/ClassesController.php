<?php

namespace App\Http\Controllers;

use App\Models\Classes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ClassesController extends Controller
{
    public function index()
{
    $totalInDb = Classes::count();
    $branches  = collect();

    try {
        $response = \Illuminate\Support\Facades\Http::timeout(10)
            ->get(env('API_URL') . 'get-branches');

        if ($response->successful()) {
            $data        = $response->json();
            $allBranches = $data['data'] ?? $data;

            $branchIds = Classes::select('erp_branch_id')
                ->distinct()
                ->whereNotNull('erp_branch_id')
                ->pluck('erp_branch_id')
                ->toArray();

            $branches = collect($allBranches)
                ->filter(fn($b) => in_array($b['id'], $branchIds))
                ->map(fn($b) => [
                    'id'   => $b['id'],
                    'name' => $b['name'] ?? $b['branch_name'] ?? 'Branch #' . $b['id'],
                ])
                ->values();
        }
    } catch (\Exception $e) {
        \Illuminate\Support\Facades\Log::error('Branch load failed: ' . $e->getMessage());
    }

    return view('classes.index', compact('totalInDb', 'branches'));
}

    /**
     * One-time fetch from ERP API and store in local DB
     */
    public function sync()
    {
        try {
            $response = Http::timeout(15)->get(env('API_URL') . 'get-classes');

            if (!$response->successful()) {
                return back()->with('error', 'Failed to reach ERP API. Status: ' . $response->status());
            }

            $data = $response->json();
            $classes = $data['data'] ?? $data;

            if (empty($classes) || !is_array($classes)) {
                return back()->with('error', 'API returned no class data.');
            }

            $synced  = 0;
            $skipped = 0;

            foreach ($classes as $cls) {
                // Skip if no ID
                if (empty($cls['id'])) {
                    $skipped++;
                    continue;
                }

                Classes::updateOrCreate(
                    [
                        'erp_class_id' => $cls['id'],
                    ],
                    [
                        'name'          => $cls['name']     ?? 'Unknown',
                        'erp_branch_id' => $cls['owned_by'] ?? null,
                        'owned_by'      => $cls['owned_by'] ?? null,
                    ]
                );

                $synced++;
            }

            return back()->with('success', "Sync complete — {$synced} classes saved, {$skipped} skipped.");

        } catch (\Exception $e) {
            Log::error('Class sync failed: ' . $e->getMessage());
            return back()->with('error', 'Sync failed: ' . $e->getMessage());
        }
    }

    /**
     * Clear all synced classes and re-sync
     */
    public function resync()
{
    // DO NOT truncate — just re-run sync which uses updateOrCreate
    return $this->sync();
}
}
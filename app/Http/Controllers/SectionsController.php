<?php

namespace App\Http\Controllers;

    use App\Models\Section;
    use App\Models\Classes;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Http;
    use Illuminate\Support\Facades\Log;

    class SectionsController extends Controller
    {
        public function index()
        {
            $totalInDb = Section::count();
            $branches  = collect();
            $classMap  = Classes::pluck('name', 'erp_class_id')->toArray();

            try {
                $response = Http::timeout(10)->get(env('API_URL') . 'get-branches');

                if ($response->successful()) {
                    $data        = $response->json();
                    $allBranches = $data['data'] ?? $data;

                    $branches = collect($allBranches)
                        ->map(fn($b) => [
                            'id'   => $b['id'],
                            'name' => $b['name'] ?? $b['branch_name'] ?? 'Branch #' . $b['id'],
                        ])
                        ->values();
                }
            } catch (\Exception $e) {
                Log::error('Branch load failed: ' . $e->getMessage());
            }

            // Build classes grouped by branch_id for JS
            $classesByBranch = Classes::select('erp_class_id', 'name', 'erp_branch_id')
                ->whereNotNull('erp_branch_id')
                ->orderBy('name')
                ->get()
                ->groupBy('erp_branch_id')
                ->map(fn($group) => $group->map(fn($c) => [
                    'id'   => $c->erp_class_id,
                    'name' => $c->name,
                ])->values())
                ->toArray();

            return view('sections.index', compact('totalInDb', 'branches', 'classMap', 'classesByBranch'));
        }

        /**
         * One-time sync — fetch all sections and store in DB
         */
        public function sync()
        {
            try {
                $response = Http::timeout(15)->get(env('API_URL') . 'get-sections');

                if (!$response->successful()) {
                    return back()->with('error', 'Failed to reach ERP API. Status: ' . $response->status());
                }

                $data     = $response->json();
                $sections = $data['data'] ?? $data;

                if (empty($sections) || !is_array($sections)) {
                    return back()->with('error', 'API returned no section data.');
                }

                $synced  = 0;
                $skipped = 0;

                foreach ($sections as $sec) {
                    if (empty($sec['id'])) {
                        $skipped++;
                        continue;
                    }

                    // resolve erp_branch_id from classes table
                    $erpBranchId = null;
                    if (!empty($sec['class_id'])) {
                        $class = Classes::where('erp_class_id', $sec['class_id'])->first();
                        $erpBranchId = $class?->erp_branch_id;
                    }

                    // fallback: owned_by may be the branch id directly
                    if (!$erpBranchId && !empty($sec['owned_by'])) {
                        $erpBranchId = $sec['owned_by'];
                    }

                    Section::updateOrCreate(
                        ['erp_section_id' => $sec['id']],
                        [
                            'name'          => $sec['name']     ?? 'Unknown',
                            'class_id'      => $sec['class_id'] ?? null,  // this is erp_class_id from API
                            'owned_by'      => $sec['owned_by'] ?? null,
                            'erp_branch_id' => $erpBranchId,
                        ]
                    );

                    $synced++;
                }

                return back()->with('success', "Sync complete — {$synced} sections saved, {$skipped} skipped.");
            } catch (\Exception $e) {
                Log::error('Section sync failed: ' . $e->getMessage());
                return back()->with('error', 'Sync failed: ' . $e->getMessage());
            }
        }

        /**
         * Clear all and re-sync
         */
        public function resync()
        {
            // DO NOT truncate — just re-run sync which uses updateOrCreate
            return $this->sync();
        }
    }

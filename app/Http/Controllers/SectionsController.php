<?php

namespace App\Http\Controllers;

    use App\Models\Branch;
    use App\Models\Section;
    use App\Models\Classes;
    use App\Models\Session;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Http;
    use Illuminate\Support\Facades\Log;

    class SectionsController extends Controller
    {
        public function index()
        {
            $activeSession = Session::active()->first();
            $sectionQuery = Section::query()
                ->when($activeSession, fn($query) => $query->where('session_id', $activeSession->id));
            $classQuery = Classes::query()
                ->when($activeSession, fn($query) => $query->where('session_id', $activeSession->id));

            $totalInDb = (clone $sectionQuery)->count();
            $classMap  = (clone $classQuery)->pluck('name', 'erp_class_id')->toArray();

            $branches = Branch::orderBy('name')
                ->get()
                ->map(fn($branch) => [
                    'id' => $branch->erp_branch_id,
                    'name' => $branch->name,
                ]);

            // Build classes grouped by branch_id for JS
            $classesByBranch = (clone $classQuery)->select('erp_class_id', 'name', 'erp_branch_id')
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
                $activeSession = Session::active()->first();

                if (!$activeSession) {
                    return back()->with('error', 'Please activate a session before syncing sections.');
                }

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
                        $class = Classes::where('erp_class_id', $sec['class_id'])
                            ->where('session_id', $activeSession->id)
                            ->first();
                        $erpBranchId = $class?->erp_branch_id;
                    }

                    // fallback: owned_by may be the branch id directly
                    if (!$erpBranchId && !empty($sec['owned_by'])) {
                        $erpBranchId = $sec['owned_by'];
                    }

                    Section::updateOrCreate(
                        [
                            'erp_section_id' => $sec['id'],
                            'session_id' => $activeSession->id,
                        ],
                        [
                            'erp_session_id' => $activeSession->erp_session_id ?: (string) $activeSession->id,
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

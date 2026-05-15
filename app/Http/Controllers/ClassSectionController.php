<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Classes;
use App\Models\ClassSection;
use App\Models\Section;
use App\Models\Session;
use App\Support\BranchScope;
use App\Support\ErpHttp;
use Illuminate\Support\Facades\Log;

class ClassSectionController extends Controller
{
    public function index()
    {
        $activeSession = Session::active()->first();
        $classSectionQuery = ClassSection::query()
            ->when($activeSession, fn($query) => $query->where('session_id', $activeSession->id));
        $classQuery = Classes::query()
            ->when($activeSession, fn($query) => $query->where('session_id', $activeSession->id));
        BranchScope::apply($classQuery);

        if ($branchId = BranchScope::coordinatorBranchId()) {
            $classSectionQuery->whereHas('class', fn($query) => $query->where('erp_branch_id', $branchId));
        }

        $totalInDb = (clone $classSectionQuery)->count();
        $usedBranchIds = (clone $classQuery)->select('erp_branch_id')
            ->distinct()
            ->whereNotNull('erp_branch_id')
            ->pluck('erp_branch_id');

        $branches = Branch::whereIn('erp_branch_id', $usedBranchIds)
            ->orderBy('name')
            ->get()
            ->map(fn($branch) => [
                'id' => $branch->erp_branch_id,
                'name' => $branch->name,
            ]);

        $grouped = (clone $classSectionQuery)->with(['class', 'section'])
            ->whereNotNull('class_id')
            ->get()
            ->groupBy('class_id');

        return view('class-sections.index', compact('totalInDb', 'branches', 'grouped'));
    }

    public function sync()
    {
        abort_unless(auth()->user()?->hasRole('Admin'), 403);

        try {
            $activeSession = Session::active()->first();

            if (!$activeSession) {
                return back()->with('error', 'Please activate a session before syncing class sections.');
            }

            $response = ErpHttp::get('get-class-section', 15);

            if (!$response->successful()) {
                return back()->with('error', 'Failed to reach ERP API. Status: ' . $response->status());
            }

            $data = $response->json();
            $rows = $data['data'] ?? $data;

            if (empty($rows) || !is_array($rows)) {
                return back()->with('error', 'API returned no data.');
            }

            // Pre-load local maps keyed by ERP id
            $classByErpId   = Classes::whereNotNull('erp_class_id')
                ->where('session_id', $activeSession->id)
                ->get()->keyBy(fn($c) => (string) $c->erp_class_id);

            $sectionByErpId = Section::whereNotNull('erp_section_id')
                ->where('session_id', $activeSession->id)
                ->get()->keyBy(fn($s) => (string) $s->erp_section_id);

            $synced  = 0;
            $skipped = 0;

            foreach ($rows as $row) {
                $erpClassId   = (string) ($row['class_id']   ?? '');
                $erpSectionId = (string) ($row['section_id'] ?? '');

                if ($erpClassId === '' || $erpSectionId === '') {
                    $skipped++;
                    continue;
                }

                $class = $classByErpId->get($erpClassId);

                // If class not synced yet, create it now
                if (!$class) {
                    $class = Classes::updateOrCreate(
                        [
                            'erp_class_id' => $erpClassId,
                            'session_id' => $activeSession->id,
                        ],
                        [
                            'erp_session_id' => $activeSession->erp_session_id ?: (string) $activeSession->id,
                            'name' => $row['class_name'] ?? ('Class #' . $erpClassId),
                        ]
                    );
                    $classByErpId->put($erpClassId, $class);
                }

                $section = $sectionByErpId->get($erpSectionId);

                // If section not synced yet, create from nested section_name object
                if (!$section) {
                    $sectionData = $row['section_name'] ?? [];
                    $section = Section::updateOrCreate(
                        [
                            'erp_section_id' => $erpSectionId,
                            'session_id' => $activeSession->id,
                        ],
                        [
                            'erp_session_id' => $activeSession->erp_session_id ?: (string) $activeSession->id,
                            'name' => $sectionData['name'] ?? ('Section #' . $erpSectionId),
                        ]
                    );
                    $sectionByErpId->put($erpSectionId, $section);
                }

                ClassSection::updateOrCreate(
                    [
                        'class_id'   => $class->id,
                        'section_id' => $section->id,
                        'session_id' => $activeSession->id,
                    ],
                    [
                        'erp_session_id' => $activeSession->erp_session_id ?: (string) $activeSession->id,
                        'erp_class_id'   => $erpClassId,
                        'erp_section_id' => $erpSectionId,
                    ]
                );

                $synced++;
            }

            return back()->with('success', "Sync complete — {$synced} class-section links saved, {$skipped} skipped.");
        } catch (\Exception $e) {
            Log::error('ClassSection sync failed: ' . $e->getMessage());
            return back()->with('error', 'Sync failed: ' . $e->getMessage());
        }
    }

    public function resync()
    {
        return $this->sync();
    }
}

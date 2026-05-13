<?php

namespace App\Http\Controllers;

use App\Models\Classes;
use App\Models\ClassSection;
use App\Models\Section;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ClassSectionController extends Controller
{
    public function index()
    {
        $totalInDb = ClassSection::count();
        $branches  = collect();

        try {
            $response = Http::timeout(10)->get(env('API_URL') . 'get-branches');

            if ($response->successful()) {
                $data        = $response->json();
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
            Log::error('Branch load failed in ClassSectionController: ' . $e->getMessage());
        }

        $grouped = ClassSection::with(['class', 'section'])
            ->whereNotNull('class_id')
            ->get()
            ->groupBy('class_id');

        return view('class-sections.index', compact('totalInDb', 'branches', 'grouped'));
    }

    public function sync()
    {
        try {
            $response = Http::timeout(15)->get(env('API_URL') . 'get-class-section');

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
                ->get()->keyBy(fn($c) => (string) $c->erp_class_id);

            $sectionByErpId = Section::whereNotNull('erp_section_id')
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
                        ['erp_class_id' => $erpClassId],
                        ['name' => $row['class_name'] ?? ('Class #' . $erpClassId)]
                    );
                    $classByErpId->put($erpClassId, $class);
                }

                $section = $sectionByErpId->get($erpSectionId);

                // If section not synced yet, create from nested section_name object
                if (!$section) {
                    $sectionData = $row['section_name'] ?? [];
                    $section = Section::updateOrCreate(
                        ['erp_section_id' => $erpSectionId],
                        ['name' => $sectionData['name'] ?? ('Section #' . $erpSectionId)]
                    );
                    $sectionByErpId->put($erpSectionId, $section);
                }

                ClassSection::updateOrCreate(
                    [
                        'class_id'   => $class->id,
                        'section_id' => $section->id,
                    ],
                    [
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

<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Classes;
use App\Models\Section;
use App\Models\Session;
use App\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ResultApiController extends Controller
{
    public function getStudents(Request $request): JsonResponse
    {
        $branchId = $request->query('branch_id');

        return $this->studentResponse($branchId);
    }

    public function getStudent(string $id): JsonResponse
    {
        return $this->studentResponse($id);
    }

    private function studentResponse(?string $branchId = null): JsonResponse
    {
        $activeSession = Session::active()->first();

        if (!$activeSession) {
            return response()->json([
                'success' => false,
                'message' => 'No active session is selected.',
                'data' => [],
            ], 422);
        }

        $students = Student::query()
            ->where('session_id', $activeSession->id)
            ->when($branchId, fn($query) => $query->where('owned_by', $branchId))
            ->orderBy('stdname')
            ->get();

        $branchMap = Branch::pluck('name', 'erp_branch_id');
        $classMap = Classes::where('session_id', $activeSession->id)->pluck('name', 'erp_class_id');
        $sectionMap = Section::where('session_id', $activeSession->id)->pluck('name', 'erp_section_id');

        return response()->json([
            'success' => true,
            'session' => [
                'id' => $activeSession->id,
                'erp_session_id' => $activeSession->erp_session_id,
                'name' => $activeSession->session_name ?? $activeSession->name ?? null,
            ],
            'branch_id' => $branchId,
            'count' => $students->count(),
            'data' => $students->map(fn($student) => [
                'id' => $student->id,
                'erp_student_id' => $student->erp_student_id,
                'rollno' => $student->rollno,
                'name' => $student->stdname,
                'father_name' => $student->fathername,
                'phone_no' => $student->phone_no,
                'branch_id' => $student->owned_by,
                'branch_name' => $branchMap[$student->owned_by] ?? null,
                'erp_class_id' => $student->erp_class_id,
                'class_name' => $classMap[$student->erp_class_id] ?? null,
                'erp_section_id' => $student->erp_section_id,
                'section_name' => $student->section_name ?: ($sectionMap[$student->erp_section_id] ?? null),
            ])->values(),
        ]);
    }
}

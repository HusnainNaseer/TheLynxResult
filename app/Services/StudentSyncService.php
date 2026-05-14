<?php

namespace App\Services;

use App\Models\Session;
use App\Models\Student;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class StudentSyncService
{
    public function syncForActiveSession(): array
    {
        $session = Session::active()->first();
        // dd('bhjb',$session);
        if (!$session) {
            return ['synced' => 0, 'skipped' => 0, 'message' => 'No active session is selected.'];
        }
        $response = Http::timeout(20)->get(env('API_URL') . 'get-students');
        // dd([
        //     'status' => $response->status(),
        //     'successful' => $response->successful(),
        //     'failed' => $response->failed(),
        //     'body' => $response->body(),
        //     'json' => $response->json(),
        // ]);
        if (!$response->successful()) {
            Log::error('Student sync failed: ' . $response->body());

            return ['synced' => 0, 'skipped' => 0, 'message' => 'Student API request failed.'];
        }

        $payload = $response->json();
        $rows = $payload['data'] ?? $payload;

        if (!is_array($rows)) {
            return ['synced' => 0, 'skipped' => 0, 'message' => 'Student API returned invalid data.'];
        }

        $synced = 0;
        $skipped = 0;

        foreach ($rows as $row) {
            if (!is_array($row)) {
                $skipped++;
                continue;
            }
            $erpStudentId = $this->firstValue($row, ['erp_student_id', 'student_id', 'std_id', 'id']);

            if (!$erpStudentId) {
                $skipped++;
                continue;
            }

            $rowErpSessionId = $this->firstValue($row, [
                'enrollment.session_id',
                'enrollment.adm_session',
                'erp_session_id',
                'session_id',
                'school_session_id',
                'session.id',
            ]);
            $activeErpSessionId = $session->erp_session_id;

            if ($rowErpSessionId && $activeErpSessionId && (string) $rowErpSessionId !== (string) $activeErpSessionId) {
                $skipped++;
                continue;
            }

            $erpSessionId = $rowErpSessionId ?: ($activeErpSessionId ?: (string) $session->id);

            Student::updateOrCreate(
                [
                    'erp_student_id' => (string) $erpStudentId,
                    'erp_session_id' => (string) $erpSessionId,
                ],
                [
                    'session_id' => $session->id,
                    'erp_class_id' => $this->firstValue($row, ['enrollment.class_id', 'erp_class_id', 'class_id', 'class.id']),
                    'erp_section_id' => $this->firstValue($row, ['enrollment.section_id', 'erp_section_id', 'section_id', 'section.id']),
                    'section_name' => $this->firstValue($row, ['section_name', 'section.name', 'section']),
                    'rollno' => $this->firstValue($row, ['rollno', 'roll_no', 'roll_number', 'admission_no']),
                    'stdname' => $this->firstValue($row, ['stdname', 'student_name', 'name']),
                    'fathername' => $this->firstValue($row, ['fathername', 'father_name', 'guardian_name']),
                    'phone_no' => $this->firstValue($row, ['phone_no', 'phone', 'mobile', 'contact_no', 'fathercell']),
                    'owned_by' => $this->firstValue($row, ['enrollment.owned_by', 'enrollment.adm_branch', 'owned_by', 'branch_id', 'erp_branch_id', 'branch.id', 'branch']),
                ]
            );

            $synced++;
        }

        return ['synced' => $synced, 'skipped' => $skipped, 'message' => 'Students synced successfully.'];
    }

    private function firstValue(array $row, array $keys): mixed
    {
        foreach ($keys as $key) {
            $value = data_get($row, $key);

            if ($value !== null && $value !== '') {
                return is_array($value) ? null : $value;
            }
        }

        return null;
    }
}

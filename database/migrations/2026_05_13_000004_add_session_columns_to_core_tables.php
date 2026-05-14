<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $tables = [
        'users',
        'classes',
        'sections',
        'classsections',
        'class_subjects',
        'teacher_subject_assignments',
        'subject_wise_marks',
        'student_results',
    ];

    public function up(): void
    {
        foreach ($this->tables as $tableName) {
            if (!Schema::hasTable($tableName)) {
                continue;
            }

            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (!Schema::hasColumn($tableName, 'session_id')) {
                    $table->unsignedBigInteger('session_id')->nullable()->index();
                }

                if (!Schema::hasColumn($tableName, 'erp_session_id')) {
                    $table->string('erp_session_id')->nullable()->index();
                }
            });
        }

        $activeSession = DB::table('schoolsessions')
            ->where('is_active', true)
            ->orderByDesc('id')
            ->first();

        if (!$activeSession) {
            $activeSession = DB::table('schoolsessions')->orderByDesc('id')->first();
        }

        if ($activeSession) {
            foreach ($this->tables as $tableName) {
                if (!Schema::hasTable($tableName) || !Schema::hasColumn($tableName, 'session_id')) {
                    continue;
                }

                DB::table($tableName)
                    ->whereNull('session_id')
                    ->update([
                        'session_id' => $activeSession->id,
                        'erp_session_id' => $activeSession->erp_session_id ?: (string) $activeSession->id,
                    ]);
            }
        }
    }

    public function down(): void
    {
        foreach (array_reverse($this->tables) as $tableName) {
            if (!Schema::hasTable($tableName)) {
                continue;
            }

            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (Schema::hasColumn($tableName, 'erp_session_id')) {
                    $table->dropColumn('erp_session_id');
                }

                if (Schema::hasColumn($tableName, 'session_id')) {
                    $table->dropColumn('session_id');
                }
            });
        }
    }
};

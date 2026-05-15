<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('student_marks')) {
            return;
        }

        Schema::table('student_marks', function (Blueprint $table) {
            if (!Schema::hasColumn('student_marks', 'session_id')) {
                $table->unsignedBigInteger('session_id')->nullable()->index()->after('result_id');
            }

            if (!Schema::hasColumn('student_marks', 'erp_session_id')) {
                $table->string('erp_session_id')->nullable()->index()->after('session_id');
            }
        });

        if (Schema::hasTable('student_results')) {
            DB::table('student_marks')
                ->join('student_results', 'student_marks.result_id', '=', 'student_results.id')
                ->whereNull('student_marks.session_id')
                ->update([
                    'student_marks.session_id' => DB::raw('student_results.session_id'),
                    'student_marks.erp_session_id' => DB::raw('student_results.erp_session_id'),
                ]);
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('student_marks')) {
            return;
        }

        Schema::table('student_marks', function (Blueprint $table) {
            if (Schema::hasColumn('student_marks', 'erp_session_id')) {
                $table->dropColumn('erp_session_id');
            }

            if (Schema::hasColumn('student_marks', 'session_id')) {
                $table->dropColumn('session_id');
            }
        });
    }
};

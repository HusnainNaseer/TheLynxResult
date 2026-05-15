<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('subject_wise_marks')) {
            return;
        }

        Schema::table('subject_wise_marks', function (Blueprint $table) {
            if (!Schema::hasColumn('subject_wise_marks', 'branch_id')) {
                $table->unsignedBigInteger('branch_id')->nullable()->after('erp_session_id')->index();
            }
        });

        if (Schema::hasTable('users') && Schema::hasColumn('users', 'branch_id')) {
            DB::table('subject_wise_marks')
                ->leftJoin('users', 'subject_wise_marks.created_by', '=', 'users.id')
                ->whereNull('subject_wise_marks.branch_id')
                ->update(['subject_wise_marks.branch_id' => DB::raw('users.branch_id')]);
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('subject_wise_marks') || !Schema::hasColumn('subject_wise_marks', 'branch_id')) {
            return;
        }

        Schema::table('subject_wise_marks', function (Blueprint $table) {
            $table->dropColumn('branch_id');
        });
    }
};

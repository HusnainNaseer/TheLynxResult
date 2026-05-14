<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_results', function (Blueprint $table) {
            if (!Schema::hasColumn('student_results', 'workflow_status')) {
                $table->string('workflow_status')->default('draft')->after('promoted_class')->index();
            }

            if (!Schema::hasColumn('student_results', 'subject_finalized_by')) {
                $table->unsignedBigInteger('subject_finalized_by')->nullable()->after('workflow_status')->index();
            }

            if (!Schema::hasColumn('student_results', 'subject_finalized_at')) {
                $table->timestamp('subject_finalized_at')->nullable()->after('subject_finalized_by');
            }

            if (!Schema::hasColumn('student_results', 'class_teacher_finalized_by')) {
                $table->unsignedBigInteger('class_teacher_finalized_by')->nullable()->after('subject_finalized_at')->index();
            }

            if (!Schema::hasColumn('student_results', 'class_teacher_finalized_at')) {
                $table->timestamp('class_teacher_finalized_at')->nullable()->after('class_teacher_finalized_by');
            }
        });
    }

    public function down(): void
    {
        Schema::table('student_results', function (Blueprint $table) {
            foreach ([
                'class_teacher_finalized_at',
                'class_teacher_finalized_by',
                'subject_finalized_at',
                'subject_finalized_by',
                'workflow_status',
            ] as $column) {
                if (Schema::hasColumn('student_results', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};

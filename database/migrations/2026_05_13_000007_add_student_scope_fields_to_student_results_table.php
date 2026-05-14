<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_results', function (Blueprint $table) {
            if (!Schema::hasColumn('student_results', 'student_id')) {
                $table->unsignedBigInteger('student_id')->nullable()->after('id')->index();
            }

            if (!Schema::hasColumn('student_results', 'branch_id')) {
                $table->string('branch_id')->nullable()->after('erp_session_id')->index();
            }

            if (!Schema::hasColumn('student_results', 'class_id')) {
                $table->unsignedBigInteger('class_id')->nullable()->after('branch_id')->index();
            }

            if (!Schema::hasColumn('student_results', 'section_id')) {
                $table->unsignedBigInteger('section_id')->nullable()->after('class_id')->index();
            }

            if (!Schema::hasColumn('student_results', 'erp_student_id')) {
                $table->string('erp_student_id')->nullable()->after('section_id')->index();
            }

            if (!Schema::hasColumn('student_results', 'erp_class_id')) {
                $table->string('erp_class_id')->nullable()->after('erp_student_id')->index();
            }

            if (!Schema::hasColumn('student_results', 'erp_section_id')) {
                $table->string('erp_section_id')->nullable()->after('erp_class_id')->index();
            }
        });

        if (!Schema::hasTable('students')) {
            return;
        }

        DB::table('student_results')
            ->whereNull('student_id')
            ->orderBy('id')
            ->chunkById(100, function ($results) {
                foreach ($results as $result) {
                    $student = DB::table('students')
                        ->where('session_id', $result->session_id)
                        ->where('rollno', $result->rollno)
                        ->first();

                    if (!$student) {
                        continue;
                    }

                    $class = DB::table('classes')
                        ->where('session_id', $student->session_id)
                        ->where('erp_class_id', $student->erp_class_id)
                        ->first();

                    $section = $student->erp_section_id
                        ? DB::table('sections')
                            ->where('session_id', $student->session_id)
                            ->where('erp_section_id', $student->erp_section_id)
                            ->first()
                        : null;

                    DB::table('student_results')->where('id', $result->id)->update([
                        'student_id' => $student->id,
                        'branch_id' => $student->owned_by,
                        'class_id' => $class?->id,
                        'section_id' => $section?->id,
                        'erp_student_id' => $student->erp_student_id,
                        'erp_class_id' => $student->erp_class_id,
                        'erp_section_id' => $student->erp_section_id,
                    ]);
                }
            });
    }

    public function down(): void
    {
        Schema::table('student_results', function (Blueprint $table) {
            foreach ([
                'erp_section_id',
                'erp_class_id',
                'erp_student_id',
                'section_id',
                'class_id',
                'branch_id',
                'student_id',
            ] as $column) {
                if (Schema::hasColumn('student_results', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};

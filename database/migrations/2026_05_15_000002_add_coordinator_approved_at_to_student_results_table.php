<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_results', function (Blueprint $table) {
            if (!Schema::hasColumn('student_results', 'coordinator_approved_at')) {
                $table->timestamp('coordinator_approved_at')->nullable()->after('class_teacher_finalized_at')->index();
            }
        });
    }

    public function down(): void
    {
        Schema::table('student_results', function (Blueprint $table) {
            if (Schema::hasColumn('student_results', 'coordinator_approved_at')) {
                $table->dropColumn('coordinator_approved_at');
            }
        });
    }
};

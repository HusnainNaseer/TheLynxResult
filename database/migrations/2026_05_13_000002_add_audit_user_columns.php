<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('teacher_subject_assignments', function (Blueprint $table) {
            if (!Schema::hasColumn('teacher_subject_assignments', 'assigned_by')) {
                $table->unsignedBigInteger('assigned_by')->nullable()->after('subject_name')->index();
            }
        });

        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'created_by')) {
                $table->unsignedBigInteger('created_by')->nullable()->after('remember_token')->index();
            }
        });
    }

    public function down(): void
    {
        Schema::table('teacher_subject_assignments', function (Blueprint $table) {
            if (Schema::hasColumn('teacher_subject_assignments', 'assigned_by')) {
                $table->dropColumn('assigned_by');
            }
        });

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'created_by')) {
                $table->dropColumn('created_by');
            }
        });
    }
};

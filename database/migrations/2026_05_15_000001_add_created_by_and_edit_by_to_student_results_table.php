<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_results', function (Blueprint $table) {
            if (!Schema::hasColumn('student_results', 'created_by')) {
                $table->unsignedBigInteger('created_by')->nullable()->index();
            }

            if (!Schema::hasColumn('student_results', 'edit_by')) {
                $table->unsignedBigInteger('edit_by')->nullable()->after('created_by')->index();
            }
        });
    }

    public function down(): void
    {
        Schema::table('student_results', function (Blueprint $table) {
            if (Schema::hasColumn('student_results', 'edit_by')) {
                $table->dropColumn('edit_by');
            }
        });
    }
};

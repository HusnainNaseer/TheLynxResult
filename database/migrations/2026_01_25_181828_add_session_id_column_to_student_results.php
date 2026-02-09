<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('student_results', 'session_id')) {
            Schema::table('student_results', function (Blueprint $table) {
                $table->unsignedBigInteger('session_id')->nullable()->after('rollno');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('student_results', 'session_id')) {
            Schema::table('student_results', function (Blueprint $table) {
                $table->dropColumn('session_id');
            });
        }
    }
};
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up(): void
{
    Schema::table('student_results', function (Blueprint $table) {
        $table->decimal('t1_working_days', 5, 2)->default(0)->after('attendance');
        $table->decimal('t2_working_days', 5, 2)->default(0)->after('t1_working_days');
    });
}

public function down(): void
{
    Schema::table('student_results', function (Blueprint $table) {
        $table->dropColumn(['t1_working_days', 't2_working_days']);
    });
}

};

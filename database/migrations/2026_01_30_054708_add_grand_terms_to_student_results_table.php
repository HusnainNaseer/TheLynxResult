<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('student_results', function (Blueprint $table) {
            // Make sure the column doesn't already exist
            if (!Schema::hasColumn('student_results', 'grand_term_one')) {
                $table->integer('grand_term_one')->default(0)->after('attendance');
            }
            if (!Schema::hasColumn('student_results', 'grand_term_two')) {
                $table->integer('grand_term_two')->default(0)->after('grand_term_one');
            }
            if (!Schema::hasColumn('student_results', 'grand_total')) {
                $table->integer('grand_total')->default(0)->after('grand_term_two');
            }
        });
    }

    public function down()
    {
        Schema::table('student_results', function (Blueprint $table) {
            $table->dropColumn(['grand_term_one', 'grand_term_two', 'grand_total']);
        });
    }
};

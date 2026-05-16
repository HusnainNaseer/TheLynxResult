<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_results', function (Blueprint $table) {
            if (!Schema::hasColumn('student_results', 'percentage_term_one')) {
                $table->decimal('percentage_term_one', 6, 2)->default(0)->after('grand_term_one');
            }

            if (!Schema::hasColumn('student_results', 'percentage_term_two')) {
                $table->decimal('percentage_term_two', 6, 2)->default(0)->after('grand_term_two');
            }
        });
    }

    public function down(): void
    {
        Schema::table('student_results', function (Blueprint $table) {
            if (Schema::hasColumn('student_results', 'percentage_term_two')) {
                $table->dropColumn('percentage_term_two');
            }

            if (Schema::hasColumn('student_results', 'percentage_term_one')) {
                $table->dropColumn('percentage_term_one');
            }
        });
    }
};

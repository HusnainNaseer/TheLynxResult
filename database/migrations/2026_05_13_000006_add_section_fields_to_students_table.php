<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            if (!Schema::hasColumn('students', 'erp_section_id')) {
                $table->string('erp_section_id')->nullable()->after('erp_class_id')->index();
            }

            if (!Schema::hasColumn('students', 'section_name')) {
                $table->string('section_name')->nullable()->after('erp_section_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            if (Schema::hasColumn('students', 'section_name')) {
                $table->dropColumn('section_name');
            }

            if (Schema::hasColumn('students', 'erp_section_id')) {
                $table->dropColumn('erp_section_id');
            }
        });
    }
};

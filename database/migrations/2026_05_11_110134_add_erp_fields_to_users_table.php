<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'branch_id')) {
                $table->unsignedBigInteger('branch_id')->nullable()->after('company_id');
            }
            if (!Schema::hasColumn('users', 'erp_employee_id')) {
                $table->unsignedBigInteger('erp_employee_id')->nullable()->after('branch_id');
            }
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['branch_id', 'erp_employee_id']);
        });
    }
};

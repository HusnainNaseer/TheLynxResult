<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sections', function (Blueprint $table) {
            if (!Schema::hasColumn('sections', 'erp_branch_id')) {
                $table->unsignedBigInteger('erp_branch_id')->nullable()->after('owned_by');
            }
        });
    }

    public function down(): void
    {
        Schema::table('sections', function (Blueprint $table) {
            $table->dropColumn('erp_branch_id');
        });
    }
};
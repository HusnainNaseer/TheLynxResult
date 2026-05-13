<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('classes', function (Blueprint $table) {
            if (!Schema::hasColumn('classes', 'erp_branch_id')) {
                $table->unsignedBigInteger('erp_branch_id')->nullable()->after('name');
            }
        });
    }

    public function down(): void
    {
        Schema::table('classes', function (Blueprint $table) {
            $table->dropColumn('erp_branch_id');
        });
    }
};
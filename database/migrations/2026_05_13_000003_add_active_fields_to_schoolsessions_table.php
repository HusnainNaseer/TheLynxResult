<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('schoolsessions', function (Blueprint $table) {
            if (!Schema::hasColumn('schoolsessions', 'erp_session_id')) {
                $table->string('erp_session_id')->nullable()->after('id')->index();
            }

            if (!Schema::hasColumn('schoolsessions', 'is_active')) {
                $table->boolean('is_active')->default(false)->after('t2_working_days')->index();
            }

            if (!Schema::hasColumn('schoolsessions', 'active_lock')) {
                $table->string('active_lock')->nullable()->after('is_active')->unique();
            }
        });

        $latestId = DB::table('schoolsessions')->orderByDesc('id')->value('id');

        if ($latestId) {
            DB::table('schoolsessions')->where('id', $latestId)->update([
                'is_active' => true,
                'active_lock' => 'active',
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('schoolsessions', function (Blueprint $table) {
            if (Schema::hasColumn('schoolsessions', 'active_lock')) {
                $table->dropUnique(['active_lock']);
                $table->dropColumn('active_lock');
            }

            if (Schema::hasColumn('schoolsessions', 'is_active')) {
                $table->dropColumn('is_active');
            }

            if (Schema::hasColumn('schoolsessions', 'erp_session_id')) {
                $table->dropColumn('erp_session_id');
            }
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('schoolsessions') || !Schema::hasColumn('schoolsessions', 'is_active')) {
            return;
        }

        $activeId = DB::table('schoolsessions')
            ->where('is_active', true)
            ->orderByDesc('id')
            ->value('id');

        $activeId ??= DB::table('schoolsessions')->orderByDesc('id')->value('id');

        DB::table('schoolsessions')->update([
            'is_active' => false,
            'active_lock' => null,
        ]);

        if ($activeId) {
            DB::table('schoolsessions')
                ->where('id', $activeId)
                ->update([
                    'is_active' => true,
                    'active_lock' => 'active',
                ]);
        }
    }

    public function down(): void
    {
        //
    }
};

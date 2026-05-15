<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'erp_access_token')) {
                $table->text('erp_access_token')->nullable()->after('remember_token');
            }

            if (!Schema::hasColumn('users', 'erp_token_expires_at')) {
                $table->timestamp('erp_token_expires_at')->nullable()->after('erp_access_token');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'erp_token_expires_at')) {
                $table->dropColumn('erp_token_expires_at');
            }

            if (Schema::hasColumn('users', 'erp_access_token')) {
                $table->dropColumn('erp_access_token');
            }
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('branches', function (Blueprint $table) {
            if (!Schema::hasColumn('branches', 'principal_headmistress')) {
                $table->string('principal_headmistress')->nullable()->after('address');
            }

            if (!Schema::hasColumn('branches', 'executive_director_islamabad')) {
                $table->string('executive_director_islamabad')->nullable()->after('principal_headmistress');
            }
        });
    }

    public function down(): void
    {
        Schema::table('branches', function (Blueprint $table) {
            if (Schema::hasColumn('branches', 'executive_director_islamabad')) {
                $table->dropColumn('executive_director_islamabad');
            }

            if (Schema::hasColumn('branches', 'principal_headmistress')) {
                $table->dropColumn('principal_headmistress');
            }
        });
    }
};

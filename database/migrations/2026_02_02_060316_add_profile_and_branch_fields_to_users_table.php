<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('profile_picture')->nullable()->after('email');
            $table->string('branch_name')->nullable()->after('profile_picture');
            $table->string('branch_email')->nullable()->after('branch_name');
            $table->string('branch_phone')->nullable()->after('branch_email');
            $table->text('branch_address')->nullable()->after('branch_phone');
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'profile_picture',
                'branch_name',
                'branch_email',
                'branch_phone',
                'branch_address'
            ]);
        });
    }
};
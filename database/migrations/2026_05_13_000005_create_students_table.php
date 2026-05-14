<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('students')) {
            return;
        }

        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('session_id')->nullable()->index();
            $table->string('erp_session_id')->nullable()->index();
            $table->string('erp_student_id')->index();
            $table->string('erp_class_id')->nullable()->index();
            $table->string('rollno')->nullable()->index();
            $table->string('stdname')->nullable();
            $table->string('fathername')->nullable();
            $table->string('phone_no')->nullable();
            $table->string('owned_by')->nullable()->index();
            $table->timestamps();

            $table->unique(['erp_student_id', 'erp_session_id'], 'students_erp_student_session_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};

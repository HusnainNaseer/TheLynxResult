<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teacher_subject_assignments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('teacher_id');  // references users.id (role = teacher)
            $table->unsignedInteger('branch_id');      // from ERP API
            $table->string('branch_name')->nullable(); // stored for display
            $table->unsignedInteger('class_id');       // from ERP API
            $table->string('class_name')->nullable();  // stored for display
            $table->unsignedInteger('section_id');     // from ERP API
            $table->string('section_name')->nullable();// stored for display
            $table->unsignedInteger('subject_id');     // from ERP API / local subjects
            $table->string('subject_name')->nullable();// stored for display
            $table->timestamps();

            $table->foreign('teacher_id')->references('id')->on('users')->onDelete('cascade');

            // prevent duplicate assignment of same subject in same section to same teacher
            $table->unique(['teacher_id', 'branch_id', 'class_id', 'section_id', 'subject_id'], 'unique_teacher_assignment');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teacher_subject_assignments');
    }
};
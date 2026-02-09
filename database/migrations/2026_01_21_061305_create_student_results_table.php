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
        Schema::create('student_results', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('class');
            $table->string('section');
            $table->integer('rollno');
            $table->string('attendance');
            $table->string('overall_grade');
            $table->string('overall_percentage');
            $table->integer('created_by');
            $table->timestamps();
        });
        Schema::table('student_results', function (Blueprint $table) {
    $table->unsignedBigInteger('session_id')->nullable()->after('rollno');
    $table->foreign('session_id')->references('id')->on('sessions')->onDelete('set null');
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_results');
    }
};
